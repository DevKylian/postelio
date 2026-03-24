<?php

namespace App\Http\Controllers;

use App\Services\ZernioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacebookController extends Controller
{
    public function __construct(private ZernioService $zernio) {}

    /**
     * Redirect the user to the Facebook OAuth authorization page.
     */
    public function connect(): RedirectResponse
    {
        try {
            $company   = Auth::user()->company;
            $profileId = $company->getZernioProfileId();
            $authUrl   = $this->zernio->getConnectUrl('facebook', $profileId, route('facebook.callback'));

            return redirect($authUrl);
        } catch (\Throwable $e) {
            Log::error('Zernio Facebook connect failed', ['error' => $e->getMessage()]);
            return redirect()->route('post-settings')->with('error', 'Impossible de se connecter à Zernio.');
        }
    }

    /**
     * Handle the Facebook OAuth callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $company  = Auth::user()->company;
            $accounts = $this->zernio->listAccounts();

            // Find the first facebook account that belongs to this profile
            $fbAccount = collect($accounts)->first(function($acc) use ($company) {
                // profileId can be an object with _id or just a string ID
                $accProfileId = is_array($acc['profileId']) ? $acc['profileId']['_id'] : $acc['profileId'];
                return $acc['platform'] === 'facebook' && $accProfileId === $company->zernio_profile_id;
            });

            if (!$fbAccount) {
                throw new \RuntimeException('Aucun compte Facebook trouvé sur Zernio après connexion.');
            }

            $company->update([
                'zernio_facebook_account_id' => $fbAccount['_id'],
                'facebook_page_id'           => $fbAccount['platformUserId'],
                'facebook_page_name'         => $fbAccount['displayName'] ?? $fbAccount['name'] ?? 'Nom inconnu',
                'facebook_page_picture_url'  => $fbAccount['profilePicture'] ?? $fbAccount['picture'] ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Zernio Facebook callback failed', ['error' => $e->getMessage()]);

            return redirect()->route('post-settings')
                ->with('error', 'Connexion Facebook échouée : ' . $e->getMessage());
        }

        return redirect()->route('post-settings')
            ->with('success', 'Facebook connecté avec succès via Zernio !');
    }

    /**
     * Remove the Facebook credentials from the company.
     */
    public function disconnect(): RedirectResponse
    {
        Auth::user()->company->update([
            'zernio_facebook_account_id' => null,
            'facebook_access_token'      => null,
            'facebook_page_id'           => null,
            'facebook_page_name'         => null,
            'facebook_page_picture_url'  => null,
        ]);

        return redirect()->route('post-settings')
            ->with('success', 'Facebook déconnecté.');
    }
}
