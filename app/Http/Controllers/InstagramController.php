<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ZernioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstagramController extends Controller
{
    public function __construct(private ZernioService $zernio) {}

    /**
     * Redirect the user to the Meta OAuth authorization page.
     */
    public function connect(): RedirectResponse
    {
        if (!Setting::get('instagram_connection_enabled', true)) {
            return redirect()->route('post-settings')
                ->with('error', 'La connexion Instagram est temporairement désactivée.');
        }

        try {
            $company   = Auth::user()->company;
            $profileId = $company->getZernioProfileId();
            $authUrl   = $this->zernio->getConnectUrl('instagram', $profileId, route('instagram.callback'));

            return redirect($authUrl);
        } catch (\Throwable $e) {
            Log::error('Zernio Instagram connect failed', ['error' => $e->getMessage()]);
            return redirect()->route('post-settings')->with('error', 'Impossible de se connecter à Zernio.');
        }
    }

    /**
     * Handle the Meta OAuth callback, store tokens and IG account details.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $company  = Auth::user()->company;
            $accounts = $this->zernio->listAccounts();

            // Find the first instagram account that belongs to this profile
            $igAccount = collect($accounts)->first(function($acc) use ($company) {
                // profileId can be an object with _id or just a string ID
                $accProfileId = is_array($acc['profileId']) ? $acc['profileId']['_id'] : $acc['profileId'];
                return $acc['platform'] === 'instagram' && $accProfileId === $company->zernio_profile_id;
            });

            if (!$igAccount) {
                throw new \RuntimeException('Aucun compte Instagram trouvé sur Zernio après connexion.');
            }

            $company->update([
                'zernio_instagram_account_id'   => $igAccount['_id'],
                'instagram_business_account_id' => $igAccount['platformUserId'],
                'instagram_username'            => $igAccount['username'] ?? $igAccount['displayName'] ?? 'Inconnu',
                'instagram_profile_picture_url' => $igAccount['profilePicture'] ?? $igAccount['picture'] ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Zernio Instagram callback failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('post-settings')
                ->with('error', 'Connexion Instagram échouée : ' . $e->getMessage());
        }

        return redirect()->route('post-settings')
            ->with('success', 'Instagram connecté avec succès via Zernio !');
    }

    /**
     * Remove the Instagram credentials from the company.
     */
    public function disconnect(): RedirectResponse
    {
        Auth::user()->company->update([
            'zernio_instagram_account_id'   => null,
            'instagram_access_token'        => null,
            'instagram_business_account_id' => null,
            'instagram_username'            => null,
            'instagram_profile_picture_url' => null,
            'instagram_token_expires_at'    => null,
        ]);

        return redirect()->route('post-settings')
            ->with('success', 'Instagram déconnecté.');
    }
}
