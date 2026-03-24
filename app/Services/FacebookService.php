<?php

namespace App\Services;

use App\Models\Post;

class FacebookService
{
    public function __construct(private ZernioService $zernio) {}

    /**
     * Publish a post to a Facebook Page via Zernio.
     */
    public function publishPost(Post $post): string
    {
        $company   = $post->company;
        $accountId = $company->zernio_facebook_account_id;

        if (!$accountId) {
            throw new \RuntimeException('Compte Facebook non connecté via Zernio.');
        }

        return $this->zernio->publishPost($post, 'facebook', $accountId);
    }

    /**
     * Get page details via Zernio.
     */
    public function getPageStats(string $token, string $pageId): array
    {
        $accounts = $this->zernio->listAccounts();
        $account  = collect($accounts)->firstWhere('_id', $pageId);

        if (!is_array($account)) {
            return [
                'name' => auth()->user()->company->facebook_page_name ?? 'Page Facebook',
                'followers_count' => 0,
                'category' => 'Non disponible via Zernio',
            ];
        }

        $profileData = $account['metadata']['profileData'] ?? [];

        return [
            'name'            => $profileData['displayName'] ?? ($account['displayName'] ?? ($account['name'] ?? 'Page Facebook')),
            'followers_count' => $profileData['followersCount'] ?? ($account['followersCount'] ?? 0),
            'category'        => $account['category'] ?? 'Social Media Page',
            'picture_url'     => $profileData['profilePicture'] ?? ($account['profilePictureUrl'] ?? ''),
        ];
    }

    /**
     * Get recent posts via Zernio with pagination.
     */
    public function getRecentPosts(string $token, string $pageId, int $page = 1, int $limit = 24): array
    {
        $response = $this->zernio->getAccountPosts($pageId, [
            'page' => $page,
            'limit' => $limit,
        ]);
        
        $postsData = $response['posts'] ?? [];
        $pagination = $response['pagination'] ?? [];

        $media = array_map(function($post) {
            return [
                'id'              => $post['id'] ?? '',
                'message'         => $post['message'] ?? '',
                'full_picture'    => $post['picture'] ?? '',
                'like_count'      => $post['likeCount'] ?? 0,
                'comments_count'  => $post['commentCount'] ?? 0,
                'shares_count'    => $post['sharesCount'] ?? 0,
                'created_time'    => $post['createdTime'] ?? now()->toIso8601String(),
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
