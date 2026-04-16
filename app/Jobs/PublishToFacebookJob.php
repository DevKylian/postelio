<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\FacebookService;
use App\Services\InstagramService;
use App\Services\ZernioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PublishToFacebookJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;
    public ?string $scheduledAt = null;

    public function __construct(public int $postId, ?string $scheduledAt = null) {
        $this->scheduledAt = $scheduledAt;
    }

    public function handle(FacebookService $facebook, ZernioService $zernio): void
    {
        set_time_limit(300);
        $post = Post::with('company')->find($this->postId);

        if (!$post) {
            return;
        }

        // Verification: Check if status is already published/failed or if scheduled_at was changed/cancelled
        if (in_array($post->status, ['published', 'failed'])) {
            Log::info('Facebook: Skipping job, post already has terminal status', ['status' => $post->status]);
            return;
        }

        if ($this->scheduledAt && (!$post->scheduled_at || $post->scheduled_at->toDateTimeString() !== $this->scheduledAt)) {
            Log::info('Facebook: Skipping job, scheduled_at mismatch (rescheduled or cancelled)', [
                'job_time' => $this->scheduledAt,
                'db_time' => $post->scheduled_at?->toDateTimeString()
            ]);
            return;
        }

        // Verification: Check if platforms still contains facebook
        $platforms = $post->platforms ?? [];
        if (!in_array('facebook', $platforms)) {
            Log::info('Facebook: Skipping job, platform no longer selected for this post', ['post_id' => $this->postId]);
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
