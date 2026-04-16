<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\InstagramService;
use App\Services\ZernioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PublishToInstagramJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;
    public ?string $scheduledAt = null;

    public function __construct(public int $postId, ?string $scheduledAt = null) {
        $this->scheduledAt = $scheduledAt;
    }

    public function handle(InstagramService $instagram, ZernioService $zernio): void
    {
        set_time_limit(300);
        $post    = Post::with('company')->find($this->postId);

        if (!$post) {
            Log::warning('Instagram: Post not found for publication', ['post_id' => $this->postId]);
            return;
        }

        // Verification: Check if status is already published/failed or if scheduled_at was changed/cancelled
        if (in_array($post->status, ['published', 'failed'])) {
            Log::info('Instagram: Skipping job, post already has terminal status', ['status' => $post->status]);
            return;
        }

        if ($this->scheduledAt && (!$post->scheduled_at || $post->scheduled_at->toDateTimeString() !== $this->scheduledAt)) {
            Log::info('Instagram: Skipping job, scheduled_at mismatch (rescheduled or cancelled)', [
                'job_time' => $this->scheduledAt,
                'db_time' => $post->scheduled_at?->toDateTimeString()
            ]);
            return;
        }

        // Verification: Check if platforms still contains instagram
        $platforms = $post->platforms ?? [];
        if (!in_array('instagram', $platforms)) {
            Log::info('Instagram: Skipping job, platform no longer selected for this post', ['post_id' => $this->postId]);
            return;
        }

        $company = $post->company;

        if (!$company) {
            Log::warning('Instagram: Company not found for post', ['post_id' => $this->postId]);
            return;
        }

        if (!$company->instagram_connected) {
            Log::info('Instagram: Company not connected to Instagram, skipping publication', ['post_id' => $this->postId, 'company_id' => $company->id]);
            return;
        }

        try {
            Log::info('Instagram: starting publication job', ['post_id' => $this->postId, 'company_id' => $company->id]);
            $igMediaId = $instagram->publishPost($post);
            Log::info('Instagram: post publié', [
                'post_id'    => $this->postId,
                'ig_media_id' => $igMediaId,
                'company_id' => $company->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Instagram: publication échouée', [
                'post_id' => $this->postId,
                'company_id' => $company->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
        }
    }
}
