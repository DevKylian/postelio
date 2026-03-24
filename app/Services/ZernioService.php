<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZernioService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.zernio.api_key');
        $this->baseUrl = config('services.zernio.base_url', 'https://zernio.com/api/v1');
    }

    /**
     * Create a Zernio Profile for a Company.
     */
    public function createProfile(Company $company): string
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/profiles", [
                'name'        => $company->name,
                'description' => "Profile for Company ID: {$company->id}",
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Failed to create Zernio profile: ' . $response->body());
        }

        $profileId = $response->json('profile._id');
        $company->update(['zernio_profile_id' => $profileId]);

        return $profileId;
    }

    /**
     * Get the connection URL for a social platform.
     */
    public function getConnectUrl(string $platform, string $profileId, ?string $redirectUrl = null): string
    {
        $params = ['profileId' => $profileId];
        if ($redirectUrl) {
            $params['redirect_url'] = $redirectUrl;
        }

        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/connect/{$platform}", $params);

        if (!$response->successful()) {
            throw new \RuntimeException("Failed to get Zernio connect URL for {$platform}: " . $response->body());
        }

        return $response->json('authUrl');
    }

    /**
     * List connected accounts for the profile.
     */
    public function listAccounts(): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/accounts");

        if (!$response->successful()) {
            throw new \RuntimeException('Failed to list Zernio accounts: ' . $response->body());
        }

        return $response->json('accounts', []);
    }

    /**
     * Publish a post via Zernio.
     */
    public function publishPost(Post $post, string $platform, string $accountId): string
    {
        $caption = $post->caption ?? '';

        // Media handling
        $slides = $post->slides ?? $post->facebook_slides ?? [];
        $image  = $post->image_path ?? $post->facebook_image_path;

        $mediaPaths = count($slides) > 0 ? $slides : ($image ? [$image] : []);
        $mediaUrls = [];

        foreach ($mediaPaths as $path) {
            $mediaUrls[] = $this->getPublicUrl($path);
        }

        $payload = [
            'content'    => $caption,
            'publishNow' => true,
            'platforms'  => [
                ['platform' => $platform, 'accountId' => $accountId]
            ],
        ];

        // Based on Zernio general API practices, media might be sent as an array of URLs or similar.
        // The quickstart doc didn't show media upload specifically, but mentioned it.
        // I'll assume it's "media" or "mediaUrls" array. 
        // Media handling
        $mediaUrls = [];
        $slides = ($platform === 'facebook') ? ($post->facebook_slides ?? $post->slides) : $post->slides;

        // Fallback to image_path if slides are missing
        if (empty($slides) && $post->image_path) {
            $slides = [$post->image_path];
        }

        if ($slides) {
            $slidesArray = is_array($slides) ? $slides : [$slides];
            foreach ($slidesArray as $slide) {
                if ($url = $this->getPublicUrl($slide)) {
                    $mediaUrls[] = $url;
                }
            }
        }

        if (!empty($mediaUrls)) {
            // As per Zernio docs: mediaItems: [ { type: 'image', url: '...' } ]
            $payload['mediaItems'] = array_map(function($url) {
                return [
                    'type' => 'image',
                    'url'  => $url
                ];
            }, $mediaUrls);
        }

        Log::info("Sending Zernio post request", ['payload' => $payload]);

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/posts", $payload);

        if (!$response->successful()) {
            Log::error('Zernio publish failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'payload' => $payload,
            ]);
            throw new \RuntimeException('Zernio publish failed: ' . $response->body());
        }

        $zernioId = $response->json('post._id');
        $post->update(['zernio_id' => $zernioId]);

        return $zernioId ?? '';
    }

    /**
     * Get posts list (published through Zernio).
     */
    public function getPosts(array $params = []): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/posts", $params);

        if (!$response->successful()) {
            Log::error('Zernio getPosts failed', ['body' => $response->body()]);
            return [];
        }

        return $response->json();
    }

    /**
     * Get all account posts (full media library from platform).
     */
    public function getAccountPosts(string $accountId, array $params = []): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/accounts/{$accountId}/posts", $params);

        if (!$response->successful()) {
            Log::error('Zernio getAccountPosts failed', [
                'body' => $response->body(),
                'accountId' => $accountId,
                'params' => $params
            ]);
            return [];
        }

        return $response->json();
    }

    /**
     * Get followers stats history.
     */
    public function getFollowerStats(array $params = []): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/accounts/follower-stats", $params);

        if (!$response->successful()) {
            Log::error('Zernio getFollowerStats failed', ['body' => $response->body()]);
            return [];
        }

        return $response->json();
    }

    /**
     * Get post analytics.
     */
    public function getPostAnalytics(array $params = []): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/analytics", $params);

        if (!$response->successful()) {
            Log::error('Zernio getPostAnalytics failed', ['body' => $response->body()]);
            return [];
        }

        return $response->json();
    }

    /**
     * Get Instagram account-level insights.
     */
    public function getInstagramAccountInsights(array $params = []): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/analytics/instagram/account-insights", $params);

        if (!$response->successful()) {
            Log::error('Zernio getInstagramAccountInsights failed', ['body' => $response->body()]);
            return [];
        }

        return $response->json();
    }

    private function getPublicUrl(string $path): string
    {
        // Reuse imgbb logic from existing services if needed, 
        // or just use local URL if Zernio can access it.
        // But since Meta couldn't, Zernio probably needs it public too.
        
        $imgbbKey = config('services.imgbb.api_key');
        $localUrl = url(\Illuminate\Support\Facades\Storage::url($path));

        if (!$imgbbKey) {
            return $localUrl;
        }

        try {
            $imageData = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
            $response  = Http::timeout(120)->asForm()->post('https://api.imgbb.com/1/upload?key=' . urlencode($imgbbKey), [
                'image'      => base64_encode($imageData),
                'expiration' => 600,
            ])->json();

            if (!empty($response['data']['url'])) {
                return $response['data']['url'];
            }
        } catch (\Throwable $e) {
            Log::warning('imgbb upload failed for Zernio, falling back to local URL', ['error' => $e->getMessage()]);
        }

        return $localUrl;
    }
}
