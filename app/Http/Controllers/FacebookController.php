<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacebookController extends Controller
{
    private const GRAPH_BASE = 'https://graph.facebook.com/v21.0';

    /**
     * Redirect the user to the Facebook OAuth authorization page.
     */
    public function connect(): RedirectResponse
    {
        $state = Str::random(32);
        session(['facebook_oauth_state' => $state]);

        $params = http_build_query([
            'client_id'     => config('services.facebook.client_id'),
            'redirect_uri'  => config('services.facebook.redirect'),
            'scope'         => 'pages_show_list,pages_read_engagement,pages_manage_posts,pages_read_user_content',
            'response_type' => 'code',
            'state'         => $state,
        ]);

        return redirect("https://www.facebook.com/v21.0/dialog/oauth?{$params}");
    }

    /**
     * Handle the Facebook OAuth callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->query('state') !== session('facebook_oauth_state')) {
            return redirect()->route('post-settings')
                ->with('error', 'Connexion Facebook annulée (état invalide).');
        }

        if ($request->has('error')) {
            return redirect()->route('post-settings')
                ->with('error', 'Connexion Facebook refusée.');
        }

        try {
            // Exchange code for user access token
            $tokenResponse = Http::get(self::GRAPH_BASE . '/oauth/access_token', [
                'client_id'     => config('services.facebook.client_id'),
                'client_secret' => config('services.facebook.client_secret'),
                'redirect_uri'  => config('services.facebook.redirect'),
                'code'          => $request->query('code'),
            ]);

            if (!$tokenResponse->successful()) {
                throw new \RuntimeException('Token exchange failed: ' . $tokenResponse->body());
            }

            $userAccessToken = $tokenResponse->json('access_token');

            // Get list of pages the user manages
            $pagesResponse = Http::get(self::GRAPH_BASE . '/me/accounts', [
                'access_token' => $userAccessToken,
                'fields'       => 'id,name,access_token,picture{url}',
            ]);

            if (!$pagesResponse->successful()) {
                throw new \RuntimeException('Failed to fetch pages: ' . $pagesResponse->body());
            }

            $pages = $pagesResponse->json('data', []);

            if (empty($pages)) {
                return redirect()->route('post-settings')
                    ->with('error', 'Aucune Page Facebook trouvée. Vous devez être administrateur d\'au moins une Page.');
            }

            // Use the first page (most common case for artisans)
            $page = $pages[0];

            Auth::user()->company->update([
                'facebook_access_token'     => $page['access_token'],
                'facebook_page_id'          => $page['id'],
                'facebook_page_name'        => $page['name'],
                'facebook_page_picture_url' => $page['picture']['data']['url'] ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Facebook OAuth failed', ['error' => $e->getMessage()]);

            return redirect()->route('post-settings')
                ->with('error', 'Connexion Facebook échouée : ' . $e->getMessage());
        }

        return redirect()->route('post-settings')
            ->with('success', 'Facebook connecté avec succès !');
    }

    /**
     * Remove the Facebook credentials from the company.
     */
    public function disconnect(): RedirectResponse
    {
        Auth::user()->company->update([
            'facebook_access_token'     => null,
            'facebook_page_id'          => null,
            'facebook_page_name'        => null,
            'facebook_page_picture_url' => null,
        ]);

        return redirect()->route('post-settings')
            ->with('success', 'Facebook déconnecté.');
    }
}
