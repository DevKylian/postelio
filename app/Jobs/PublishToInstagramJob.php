<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\InstagramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PublishToInstagramJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;

    public function __construct(public int $postId) {}

    public function handle(InstagramService $instagram): void
    {
        set_time_limit(300);
        $post    = Post::with('company')->find($this->postId);

        if (!$post) {
            return;
        }

        $company = $post->company;

        if (!$company || !$company->instagram_connected) {
            return;
        }

        try {
            $igMediaId = $instagram->publishPost($post);
            Log::info('Instagram: post publié', [
                'post_id'    => $this->postId,
                'ig_media_id' => $igMediaId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Instagram: publication échouée', [
                'post_id' => $this->postId,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
