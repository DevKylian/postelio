<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\FacebookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PublishToFacebookJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;

    public function __construct(public int $postId) {}

    public function handle(FacebookService $facebook): void
    {
        set_time_limit(300);
        $post = Post::with('company')->find($this->postId);

        if (!$post) {
            return;
        }

        $company = $post->company;

        if (!$company || !$company->facebook_connected) {
            return;
        }

        try {
            $fbPostId = $facebook->publishPost($post);
            Log::info('Facebook: post publié', [
                'post_id'    => $this->postId,
                'fb_post_id' => $fbPostId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Facebook: publication échouée', [
                'post_id' => $this->postId,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
