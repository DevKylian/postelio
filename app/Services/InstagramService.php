<?php

namespace App\Services;

use App\Models\Post;

class InstagramService
{
    public function __construct(private ZernioService $zernio) {}

    /**
     * Publish a post to Instagram via Zernio.
     */
    public function publishPost(Post $post): string
    {
        $company   = $post->company;
        $accountId = $company->zernio_instagram_account_id;

        if (!$accountId) {
             throw new \RuntimeException('Compte Instagram non connecté via Zernio.');
        }

        return $this->zernio->publishPost($post, 'instagram', $accountId);
    }

    /**
     * Get account details via Zernio.
     */
    public function getAccountStats(string $token, string $accountId): array
    {
        $accounts = $this->zernio->listAccounts();
        $account  = collect($accounts)->firstWhere('_id', $accountId);

        if (!is_array($account)) {
            return [
                'username' => auth()->user()->company->instagram_username ?? 'Compte Instagram',
                'followers_count' => 0,
                'follows_count' => 0,
                'media_count' => 0,
            ];
        }

        $profileData = $account['metadata']['profileData'] ?? [];

        return [
            'username'            => $profileData['username'] ?? ($account['username'] ?? ($account['name'] ?? 'Compte Instagram')),
            'name'                => $profileData['displayName'] ?? ($account['name'] ?? ''),
            'followers_count'     => $profileData['followersCount'] ?? ($account['followersCount'] ?? 0),
            'follows_count'       => $profileData['followsCount'] ?? ($account['followsCount'] ?? 0),
            'media_count'         => $profileData['mediaCount'] ?? ($account['mediaCount'] ?? 0),
            'profile_picture_url' => $profileData['profilePicture'] ?? ($account['profilePictureUrl'] ?? ''),
        ];
    }

    /**
     * Get recent media via Zernio with pagination.
     */
    public function getRecentMedia(string $token, string $accountId, int $page = 1, int $limit = 24): array
    {
        $response = $this->zernio->getAccountPosts($accountId, [
            'page' => $page,
            'limit' => $limit,
        ]);
        
        $postsData = $response['posts'] ?? [];
        $pagination = $response['pagination'] ?? [];

        $media = array_map(function($post) {
            $mediaType = strtoupper($post['mediaType'] ?? 'IMAGE');
            if ($mediaType === 'CAROUSEL') $mediaType = 'CAROUSEL_ALBUM';
            
            return [
                'id'              => $post['id'] ?? '',
                'caption'         => $post['message'] ?? '',
                'media_type'      => $mediaType,
                'media_url'       => $post['picture'] ?? '',
                'thumbnail_url'   => $post['picture'] ?? '',
                'like_count'      => $post['likeCount'] ?? 0,
                'comments_count'  => $post['commentCount'] ?? 0,
                'timestamp'       => $post['createdTime'] ?? now()->toIso8601String(),
                'permalink'       => $post['permalink'] ?? null,
            ];
        }, $postsData);

        return [
            'media' => $media,
            'has_more' => ($pagination['page'] ?? 1) < ($pagination['pages'] ?? 1),
            'total' => $pagination['total'] ?? count($media),
        ];
    }
}
