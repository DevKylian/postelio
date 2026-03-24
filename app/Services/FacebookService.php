<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FacebookService
{
    private const GRAPH_BASE = 'https://graph.facebook.com/v21.0';

    /**
     * Publish a post (single image or carousel) to a Facebook Page.
     */
    public function publishPost(Post $post): string
    {
        $company     = $post->company;
        $accessToken = $company->facebook_access_token;
        $pageId      = $company->facebook_page_id;
        $caption     = $post->caption ?? '';

        $fbSlides = $post->facebook_slides ?? [];
        $fbImage  = $post->facebook_image_path;

        // Carousel (multiple photos)
        if (count($fbSlides) > 1) {
            return $this->publishCarousel($pageId, $accessToken, $fbSlides, $caption);
        }

        // Single image
        $imagePath = $fbSlides[0] ?? $fbImage ?? $post->image_path;

        if (!$imagePath) {
            throw new \RuntimeException('No image to publish to Facebook.');
        }

        $imageUrl = $this->getPublicUrl($imagePath);

        $response = Http::timeout(120)->post(self::GRAPH_BASE . "/{$pageId}/photos", [
            'url'          => $imageUrl,
            'message'      => $caption,
            'access_token' => $accessToken,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Facebook publish failed: ' . $response->body());
        }

        return $response->json('id');
    }

    private function publishCarousel(string $pageId, string $accessToken, array $slides, string $caption): string
    {
        $photoIds = [];

        // Upload each photo unpublished
        foreach ($slides as $slide) {
            $imageUrl = $this->getPublicUrl($slide);

            $response = Http::timeout(120)->post(self::GRAPH_BASE . "/{$pageId}/photos", [
                'url'          => $imageUrl,
                'published'    => false,
                'access_token' => $accessToken,
            ]);

            if (!$response->successful()) {
                throw new \RuntimeException('Facebook carousel upload failed: ' . $response->body());
            }

            $photoIds[] = $response->json('id');
        }

        // Publish the multi-photo post
        $attachedMedia = [];
        foreach ($photoIds as $id) {
            $attachedMedia[] = ['media_fbid' => $id];
        }

        $params = [
            'message'        => $caption,
            'access_token'   => $accessToken,
            'attached_media' => $attachedMedia,
        ];

        $response = Http::timeout(120)->post(self::GRAPH_BASE . "/{$pageId}/feed", $params);

        if (!$response->successful()) {
            throw new \RuntimeException('Facebook carousel publish failed: ' . $response->body());
        }

        return $response->json('id');
    }

    /**
     * Get page stats (name, followers, picture, etc.).
     */
    public function getPageStats(string $accessToken, string $pageId): array
    {
        $response = Http::timeout(120)->get(self::GRAPH_BASE . "/{$pageId}", [
            'fields'       => 'name,followers_count,fan_count,picture{url},about,website,category',
            'access_token' => $accessToken,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Facebook page stats failed: ' . $response->body());
        }

        $data = $response->json();

        return [
            'name'            => $data['name'] ?? '',
            'followers_count' => $data['followers_count'] ?? $data['fan_count'] ?? 0,
            'picture_url'     => $data['picture']['data']['url'] ?? null,
            'about'           => $data['about'] ?? '',
            'website'         => $data['website'] ?? '',
            'category'        => $data['category'] ?? '',
        ];
    }

    /**
     * Get recent posts from the Facebook Page.
     */
    public function getRecentPosts(string $accessToken, string $pageId, int $limit = 24): array
    {
        $response = Http::timeout(120)->get(self::GRAPH_BASE . "/{$pageId}/posts", [
            'fields'       => 'message,created_time,full_picture,likes.summary(true),comments.summary(true),shares',
            'limit'        => $limit,
            'access_token' => $accessToken,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Facebook recent posts failed: ' . $response->body());
        }

        $posts = $response->json('data', []);

        return array_map(fn($p) => [
            'id'             => $p['id'],
            'message'        => $p['message'] ?? '',
            'created_time'   => $p['created_time'],
            'full_picture'   => $p['full_picture'] ?? null,
            'like_count'     => $p['likes']['summary']['total_count'] ?? 0,
            'comments_count' => $p['comments']['summary']['total_count'] ?? 0,
            'shares_count'   => $p['shares']['count'] ?? 0,
        ], $posts);
    }

    private function getPublicUrl(string $path): string
    {
        $localUrl = url(Storage::url($path));

        $imgbbKey = config('services.imgbb.api_key');

        if (!$imgbbKey) {
            return $localUrl;
        }

        try {
            $imageData = Storage::disk('public')->get($path);
            $response  = Http::timeout(120)->asForm()->post('https://api.imgbb.com/1/upload?key=' . urlencode($imgbbKey), [
                'image'      => base64_encode($imageData),
                'expiration' => 600,
            ])->json();

            if (!empty($response['data']['url'])) {
                return $response['data']['url'];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('imgbb upload failed for Facebook, falling back to local URL', ['error' => $e->getMessage()]);
        }

        return $localUrl;
    }
}
