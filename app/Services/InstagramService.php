<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class InstagramService
{
    private const GRAPH_BASE    = 'https://graph.facebook.com/v21.0';
    private const API_VERSION   = 'v21.0';

    private string $appId;
    private string $appSecret;
    private string $callbackUrl;

    public function __construct()
    {
        $this->appId       = config('services.instagram.client_id');
        $this->appSecret   = config('services.instagram.client_secret');
        $this->callbackUrl = config('services.instagram.redirect');
    }

    /**
     * Generate the Meta OAuth authorization URL.
     */
    public function getAuthUrl(string $state): string
    {
        $params = http_build_query([
            'client_id'     => $this->appId,
            'redirect_uri'  => $this->callbackUrl,
            'scope'         => 'instagram_basic,instagram_content_publish,pages_read_engagement,pages_show_list,business_management',
            'response_type' => 'code',
            'state'         => $state,
        ]);

        return 'https://www.facebook.com/' . self::API_VERSION . '/dialog/oauth?' . $params;
    }

    /**
     * Exchange OAuth code for a long-lived user access token (valid ~60 days).
     * Returns array with access_token and expires_in.
     */
    public function exchangeCodeForLongLivedToken(string $code): array
    {
        $short = Http::get(self::GRAPH_BASE . '/oauth/access_token', [
            'client_id'     => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri'  => $this->callbackUrl,
            'code'          => $code,
        ])->json();

        if (empty($short['access_token'])) {
            throw new \RuntimeException('Token exchange failed: ' . json_encode($short));
        }

        $long = Http::get(self::GRAPH_BASE . '/oauth/access_token', [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $this->appId,
            'client_secret'     => $this->appSecret,
            'fb_exchange_token' => $short['access_token'],
        ])->json();

        if (empty($long['access_token'])) {
            throw new \RuntimeException('Long-lived token exchange failed: ' . json_encode($long));
        }

        return $long;
    }

    /**
     * Find the Instagram Business Account ID linked to one of the user's Facebook Pages.
     * Returns ['id' => ..., 'username' => ...].
     */
    public function getInstagramBusinessAccount(string $accessToken): array
    {
        $me = Http::get(self::GRAPH_BASE . '/me', [
            'access_token' => $accessToken,
            'fields'       => 'id,name',
        ])->json();

        $permissions = Http::get(self::GRAPH_BASE . '/me/permissions', [
            'access_token' => $accessToken,
        ])->json();

        \Log::debug('Instagram OAuth user', ['me' => $me, 'permissions' => $permissions]);

        $response = Http::get(self::GRAPH_BASE . '/me/accounts', [
            'access_token' => $accessToken,
            'fields'       => 'id,name,instagram_business_account',
        ])->json();

        $pages = $response['data'] ?? [];

        \Log::debug('Instagram /me/accounts response', ['response' => $response]);

        // Check direct pages first
        foreach ($pages as $page) {
            if (!empty($page['instagram_business_account']['id'])) {
                return $this->resolveIgAccount($page['instagram_business_account']['id'], $accessToken);
            }
        }

        // Fallback: pages managed via Business Manager
        $businesses = Http::get(self::GRAPH_BASE . '/me/businesses', [
            'access_token' => $accessToken,
            'fields'       => 'id,name',
        ])->json()['data'] ?? [];

        \Log::debug('Instagram /me/businesses', ['businesses' => $businesses]);

        foreach ($businesses as $business) {
            $bizPages = Http::get(self::GRAPH_BASE . '/' . $business['id'] . '/owned_pages', [
                'access_token' => $accessToken,
                'fields'       => 'id,name,instagram_business_account',
            ])->json()['data'] ?? [];

            foreach ($bizPages as $page) {
                if (!empty($page['instagram_business_account']['id'])) {
                    return $this->resolveIgAccount($page['instagram_business_account']['id'], $accessToken);
                }
            }
        }

        $pagesDebug = array_map(fn($p) => ['id' => $p['id'] ?? '?', 'name' => $p['name'] ?? '?', 'has_ig' => !empty($p['instagram_business_account'])], $pages);
        $grantedPerms = array_column(
            array_filter($permissions['data'] ?? [], fn($p) => $p['status'] === 'granted'),
            'permission'
        );

        throw new \RuntimeException(
            'Aucun compte Instagram Business trouvé lié à une Page Facebook. ' .
            'Connecté en tant que : ' . ($me['name'] ?? '?') . ' (id: ' . ($me['id'] ?? '?') . '). ' .
            'Pages trouvées : ' . json_encode($pagesDebug) . '. ' .
            'Business Manager : ' . json_encode(array_column($businesses, 'name')) . '. ' .
            'Permissions accordées : ' . implode(', ', $grantedPerms)
        );
    }

    private function resolveIgAccount(string $igId, string $accessToken): array
    {
        $ig = Http::get(self::GRAPH_BASE . "/{$igId}", [
            'fields'       => 'id,username',
            'access_token' => $accessToken,
        ])->json();

        return [
            'id'       => $igId,
            'username' => $ig['username'] ?? '',
        ];
    }

    /**
     * Publish a post to Instagram. Handles both single image and carousel.
     * Returns the Instagram media ID.
     */
    public function publishPost(Post $post): string
    {
        $company = $post->company;
        $token   = $company->instagram_access_token;
        $igId    = $company->instagram_business_account_id;
        $caption = $post->caption;

        $slides = $post->slides ?? [];

        if (count($slides) > 1) {
            return $this->publishCarousel($igId, $token, $slides, $caption);
        }

        $imagePath = $post->image_path;
        $imageUrl  = $this->publicUrl($imagePath);


        return $this->publishSingleImage($igId, $token, $imageUrl, $caption);
    }

    private function publishSingleImage(string $igId, string $token, string $imageUrl, string $caption): string
    {
        $container = Http::post(self::GRAPH_BASE . "/{$igId}/media", [
            'image_url'    => $imageUrl,
            'caption'      => $caption,
            'access_token' => $token,
        ])->json();

        if (empty($container['id'])) {
            throw new \RuntimeException('Création du container échouée : ' . json_encode($container));
        }

        $result = Http::post(self::GRAPH_BASE . "/{$igId}/media_publish", [
            'creation_id'  => $container['id'],
            'access_token' => $token,
        ])->json();

        if (empty($result['id'])) {
            throw new \RuntimeException('Publication échouée : ' . json_encode($result));
        }

        return $result['id'];
    }

    private function publishCarousel(string $igId, string $token, array $slides, string $caption): string
    {
        // Step 1 — upload each slide as a carousel item (no caption)
        $itemIds = [];
        foreach ($slides as $slidePath) {
            $item = Http::post(self::GRAPH_BASE . "/{$igId}/media", [
                'image_url'        => $this->publicUrl($slidePath),
                'is_carousel_item' => true,
                'access_token'     => $token,
            ])->json();

            if (empty($item['id'])) {
                throw new \RuntimeException('Carousel item échoué : ' . json_encode($item));
            }

            $itemIds[] = $item['id'];
        }

        // Step 2 — create carousel container with caption
        $container = Http::post(self::GRAPH_BASE . "/{$igId}/media", [
            'media_type'   => 'CAROUSEL',
            'children'     => implode(',', $itemIds),
            'caption'      => $caption,
            'access_token' => $token,
        ])->json();

        if (empty($container['id'])) {
            throw new \RuntimeException('Carousel container échoué : ' . json_encode($container));
        }

        // Step 3 — publish
        $result = Http::post(self::GRAPH_BASE . "/{$igId}/media_publish", [
            'creation_id'  => $container['id'],
            'access_token' => $token,
        ])->json();

        if (empty($result['id'])) {
            throw new \RuntimeException('Publication carousel échouée : ' . json_encode($result));
        }

        return $result['id'];
    }

    private function publicUrl(string $storagePath): string
    {
        $localUrl = Storage::disk('public')->url($storagePath);

        $imgbbKey = config('services.imgbb.api_key');

        if (!$imgbbKey) {
            return $localUrl;
        }

        try {
            $imageData = Storage::disk('public')->get($storagePath);
            $response  = Http::asForm()->post('https://api.imgbb.com/1/upload?key=' . urlencode($imgbbKey), [
                'image'      => base64_encode($imageData),
                'expiration' => 600,
            ])->json();

            if (!empty($response['data']['url'])) {
                return $response['data']['url'];
            }
        } catch (\Throwable $e) {
            \Log::warning('imgbb upload failed, falling back to local URL', ['error' => $e->getMessage()]);
        }

        return $localUrl;
    }
}
