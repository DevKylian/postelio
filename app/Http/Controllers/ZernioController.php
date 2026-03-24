<?php

namespace App\Http\Controllers;

use App\Services\ZernioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ZernioController extends Controller
{
    public function __construct(private ZernioService $zernio) {}

    /**
     * Synchronize connected accounts from Zernio.
     */
    public function sync(): RedirectResponse
    {
        try {
            $company = Auth::user()->company;
            $accounts = $this->zernio->listAccounts();

            $fbAccount = collect($accounts)->firstWhere('platform', 'facebook');
            $igAccount = collect($accounts)->firstWhere('platform', 'instagram');

            $updates = [];

            if ($fbAccount && is_array($fbAccount)) {
                $updates['zernio_facebook_account_id'] = $fbAccount['_id'];
                $updates['facebook_page_id']           = $fbAccount['platformUserId'];
                $updates['facebook_page_name']         = $fbAccount['displayName'] ?? $fbAccount['name'] ?? 'Nom inconnu';
            }

            if ($igAccount && is_array($igAccount)) {
                $updates['zernio_instagram_account_id']   = $igAccount['_id'];
                $updates['instagram_business_account_id'] = $igAccount['platformUserId'];
                $updates['instagram_username']            = $igAccount['username'] ?? $igAccount['displayName'] ?? 'Inconnu';
                $updates['instagram_profile_picture_url'] = $igAccount['profilePicture'] ?? null;
            }

            if (!empty($updates)) {
                $company->update($updates);
                return redirect()->route('post-settings')->with('success', 'Comptes synchronisés depuis Zernio.');
            }

            return redirect()->route('post-settings')->with('info', 'Aucun nouveau compte trouvé sur Zernio.');
        } catch (\Throwable $e) {
            return redirect()->route('post-settings')->with('error', 'Erreur lors de la synchronisation : ' . $e->getMessage());
        }
    }
}
