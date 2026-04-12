<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostList extends Component
{
    public ?Post $selectedPost = null;

    public bool $showPublishModal = false;
    public ?int $postToPublishId = null;
    public array $publishPlatforms = [];
    public string $publishScheduleType = 'now';
    public string $publishScheduledAt = '';
    public array $existingPlatforms = [];

    public function selectPost(Post $post)
    {
        $this->selectedPost = $post;
    }

    public function closeModal()
    {
        $this->selectedPost = null;
    }

    public function openPublishModal(Post $post)
    {
        $this->postToPublishId = $post->id;
        
        // If post is already published, we treat platforms in DB as "done"
        // If it's NOT published (completed, scheduled, failed), platforms are "planned"
        $isDone = $post->status === 'published';
        $this->existingPlatforms = $isDone ? ($post->platforms ?? []) : [];
        
        $this->publishPlatforms = !$isDone ? ($post->platforms ?? []) : [];
        $this->publishScheduleType = 'now';
        $this->publishScheduledAt = '';
        
        $company = Auth::user()->company;
        
        // Add connected platforms that aren't yet in the list
        if ($company?->instagram_connected && !in_array('instagram', $this->publishPlatforms) && !in_array('instagram', $this->existingPlatforms)) {
            $this->publishPlatforms[] = 'instagram';
        }
        if ($company?->facebook_connected && !in_array('facebook', $this->publishPlatforms) && !in_array('facebook', $this->existingPlatforms)) {
            $this->publishPlatforms[] = 'facebook';
        }
        
        $this->showPublishModal = true;
    }

    public function closePublishModal()
    {
        $this->showPublishModal = false;
        $this->postToPublishId = null;
    }

    public function publishPost()
    {
        $this->validate([
            'publishPlatforms' => 'required|array|min:1',
            'publishScheduleType' => 'required|in:now,scheduled',
            'publishScheduledAt' => $this->publishScheduleType === 'scheduled' ? 'required|date|after:now' : 'nullable',
        ]);

        $post = Post::where('id', $this->postToPublishId)->where('user_id', Auth::id())->first();
        if (!$post) {
            $this->closePublishModal();
            return;
        }

        $existingPlatforms = $post->platforms ?? [];
        $newPlatforms = $this->publishPlatforms; 

        $scheduledAt = ($this->publishScheduleType === 'scheduled' && $this->publishScheduledAt)
            ? \Carbon\Carbon::parse($this->publishScheduledAt)
            : null;

        // Merge platforms (preserving already published ones if any)
        $allPlatforms = array_unique(array_merge($existingPlatforms, $newPlatforms));

        $updateData = [
            'platforms' => $allPlatforms,
            'scheduled_at' => $scheduledAt,
        ];

        // Only set to pending if it was failed or something; 
        // if it's already completed, keep it completed so it doesn't show "Génération..."
        if (!in_array($post->status, ['completed', 'published'])) {
            $updateData['status'] = 'pending';
        }

        $post->update($updateData);

        $company = $post->company;
        $delay = $scheduledAt && $scheduledAt->isFuture() ? $scheduledAt : null;
        $scheduledAtStr = $scheduledAt ? $scheduledAt->toDateTimeString() : null;

        if (in_array('instagram', $newPlatforms) && $company->instagram_connected) {
            $job = \App\Jobs\PublishToInstagramJob::dispatch($post->id, $scheduledAtStr);
            if ($delay) $job->delay($delay);
        }

        if (in_array('facebook', $newPlatforms) && $company->facebook_connected) {
            $job = \App\Jobs\PublishToFacebookJob::dispatch($post->id, $scheduledAtStr);
            if ($delay) $job->delay($delay);
        }

        session()->flash('success', $delay ? 'Post planifié avec succès !' : 'Post en cours de publication !');
        $this->closePublishModal();
    }

    public function cancelSchedule(Post $post)
    {
        if ($post->user_id !== Auth::id()) return;
        
        $updateData = ['scheduled_at' => null];
        if (!in_array($post->status, ['completed', 'published'])) {
            $updateData['status'] = 'pending';
        }
        $post->update($updateData);

        session()->flash('success', 'Planification annulée !');
    }

    public function publishNow(Post $post)
    {
        if ($post->user_id !== Auth::id()) return;

        $updateData = ['scheduled_at' => null];
        if (!in_array($post->status, ['completed', 'published'])) {
            $updateData['status'] = 'pending';
        }
        $post->update($updateData);

        $company = $post->company;
        $platforms = $post->platforms ?? [];

        if (in_array('instagram', $platforms) && $company->instagram_connected) {
            \App\Jobs\PublishToInstagramJob::dispatch($post->id, null);
        }

        if (in_array('facebook', $platforms) && $company->facebook_connected) {
            \App\Jobs\PublishToFacebookJob::dispatch($post->id, null);
        }

        session()->flash('success', 'Publication immédiate lancée !');
    }

    public function editSchedule(Post $post)
    {
        $this->openPublishModal($post);
        
        if ($post->scheduled_at) {
            $this->publishScheduleType = 'scheduled';
            $this->publishScheduledAt = $post->scheduled_at->format('Y-m-d\TH:i');
        }
    }

    /**
     * Synchronize post status from Zernio API if webhook was missed.
     */
    public function refreshStatus($postId)
    {
        $post = Post::where('id', $postId)->where('user_id', Auth::id())->first();

        if (!$post || !$post->zernio_id) {
            return;
        }

        try {
            $zernio = app(\App\Services\ZernioService::class);
            $zernioPost = $zernio->getZernioPost($post->zernio_id);

            if ($zernioPost) {
                // Try to find status at root or in nested post key
                $zStatus = $zernioPost['status'] ?? $zernioPost['post']['status'] ?? null;
                $zStatus = strtolower((string) $zStatus);
                
                if (in_array($zStatus, ['published', 'posted', 'success'])) {
                    $post->status = Post::STATUS_PUBLISHED;
                    $post->save();
                    session()->flash('success', 'Statut mis à jour : le post est bien en ligne !');
                } elseif (in_array($zStatus, ['failed', 'error'])) {
                    $post->status = Post::STATUS_FAILED;
                    $post->save();
                    session()->flash('error', 'Le post a échoué sur Zernio.');
                }
            }
        } catch (\Throwable $e) {
            // Silently fail or log minimal error in production
        }
    }

    public function render()
    {
        $posts = Post::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'processing', 'review_pending', 'completed'])
            ->latest()
            ->get();

        $hasPendingPosts = $posts->contains(function ($post) {
            // actively processing (generating images/slides)
            if ($post->status === 'processing') return true;

            // scheduled for very soon (within next 30 seconds)
            if ($post->status === 'pending' && $post->scheduled_at && $post->scheduled_at->isPast()) return true;
            if ($post->status === 'pending' && $post->scheduled_at && $post->scheduled_at->diffInSeconds(now()) < 30) return true;
            
            // Waiting for Zernio Webhook (sent but not yet confirmed/failed)
            if ($post->status === 'completed' && $post->zernio_id) {
                // Only poll if it was updated in the last 10 minutes to avoid infinite polling
                return $post->updated_at->gt(now()->subMinutes(10));
            }

            return false;
        });
        
        $company = Auth::user()->company;
        $igConnected = $company?->instagram_connected ?? false;
        $fbConnected = $company?->facebook_connected ?? false;

        return view('livewire.post-list', compact('posts', 'hasPendingPosts', 'igConnected', 'fbConnected', 'company'));
    }
}
