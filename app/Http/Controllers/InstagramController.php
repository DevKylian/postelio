<?php

namespace App\Http\Controllers;

use App\Services\InstagramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InstagramController extends Controller
{
    public function __construct(private InstagramService $instagram) {}

    /**
     * Redirect the user to the Meta OAuth authorization page.
     */
    public function connect(): RedirectResponse
    {
        $state = Str::random(32);
        session(['instagram_oauth_state' => $state]);

        return redirect($this->instagram->getAuthUrl($state));
    }

    /**
     * Handle the Meta OAuth callback, store tokens and IG account details.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->query('state') !== session('instagram_oauth_state')) {
            return redirect()->route('post-settings')
                ->with('error', 'Connexion Instagram annulée (état invalide).');
        }

        if ($request->has('error')) {
            return redirect()->route('post-settings')
                ->with('error', 'Connexion Instagram refusée.');
        }

        try {
            $tokenData   = $this->instagram->exchangeCodeForLongLivedToken($request->query('code'));
            $accessToken = $tokenData['access_token'];
            $expiresAt   = now()->addSeconds($tokenData['expires_in'] ?? 5_183_944);

            $igAccount = $this->instagram->getInstagramBusinessAccount($accessToken);

            Auth::user()->company->update([
                'instagram_access_token'        => $accessToken,
                'instagram_business_account_id' => $igAccount['id'],
                'instagram_username'            => $igAccount['username'],
                'instagram_token_expires_at'    => $expiresAt,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Instagram OAuth failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('post-settings')
                ->with('error', 'Connexion Instagram échouée : ' . $e->getMessage());
        }

        return redirect()->route('post-settings')
            ->with('success', 'Instagram connecté avec succès !');
    }

    /**
     * Remove the Instagram credentials from the company.
     */
    public function disconnect(): RedirectResponse
    {
        Auth::user()->company->update([
            'instagram_access_token'        => null,
            'instagram_business_account_id' => null,
            'instagram_username'            => null,
            'instagram_token_expires_at'    => null,
        ]);

        return redirect()->route('post-settings')
            ->with('success', 'Instagram déconnecté.');
    }
}
