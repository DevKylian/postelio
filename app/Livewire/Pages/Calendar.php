<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Jobs\PublishToInstagramJob;
use App\Jobs\PublishToFacebookJob;

class Calendar extends Component
{
    public int $year;
    public int $month;
    public string $view = 'week'; // 'month' | 'week'
    public int $weekOffset = 0;    // offset in weeks from current week

    // Post detail modal
    public ?int    $selectedPostId    = null;
    public ?array  $selectedPost      = null;
    public ?string $newScheduledDate  = null;
    public ?string $newScheduledTime  = null;
    public ?string $rescheduleError   = null;   // shown in modal
    public ?string $rescheduleWarning = null;   // shown in modal

    public function mount(): void
    {
        $this->year  = (int) now()->format('Y');
        $this->month = (int) now()->format('n');
    }

    // ── View switch ────────────────────────────────────────────────────────────

    public function switchView(string $view): void
    {
        $this->view = $view;
    }

    // ── Month navigation ───────────────────────────────────────────────────────

    public function previousMonth(): void
    {
        $date        = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year  = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date        = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year  = $date->year;
        $this->month = $date->month;
    }

    // ── Week navigation ────────────────────────────────────────────────────────

    public function previousWeek(): void
    {
        $this->weekOffset--;
    }

    public function nextWeek(): void
    {
        $this->weekOffset++;
    }

    public function goToCurrentWeek(): void
    {
        $this->weekOffset = 0;
    }

    // ── Post detail modal ──────────────────────────────────────────────────────

    public function openPost(int $postId): void
    {
        $post = Post::where('user_id', Auth::id())->find($postId);
        if (! $post) {
            return;
        }

        $date = $post->scheduled_at ?? $post->created_at;

        $this->selectedPostId  = $postId;
        $this->selectedPost    = [
            'id'           => $post->id,
            'caption'      => $post->caption,
            'template'     => $post->template,
            'scheduled'    => $post->scheduled_at !== null,
            'thumbnail'    => $this->getThumbnail($post),
            'scheduled_at' => $post->scheduled_at?->format('d/m/Y à H\hi'),
            'created_at'   => $post->created_at->format('d/m/Y à H\hi'),
            'status'       => $post->status,
            'platforms'    => $post->platforms ?? [],
            'ig_connected' => $post->company->instagram_connected ?? false,
            'fb_connected' => $post->company->facebook_connected ?? false,
        ];
        $this->newScheduledDate = $date->format('Y-m-d');
        $this->newScheduledTime = $date->format('H:i');
    }

    public function closePost(): void
    {
        $this->selectedPostId    = null;
        $this->selectedPost      = null;
        $this->newScheduledDate  = null;
        $this->newScheduledTime  = null;
        $this->rescheduleError   = null;
        $this->rescheduleWarning = null;
    }

    public function togglePlatform(string $platform): void
    {
        if (!$this->selectedPostId) return;

        $post = Post::where('user_id', Auth::id())->find($this->selectedPostId);
        if (!$post) return;

        $platforms = $post->platforms ?? [];

        if (in_array($platform, $platforms)) {
            $platforms = array_values(array_filter($platforms, fn($p) => $p !== $platform));
        } else {
            $platforms[] = $platform;
        }

        $post->platforms = $platforms;
        
        // Si on active des plateformes sur un post déjà planifié dans le futur, 
        // on s'assure qu'il est en statut 'completed' (prêt pour publication)
        if (count($platforms) > 0 && $post->scheduled_at && $post->scheduled_at->isFuture()) {
            $post->status = Post::STATUS_COMPLETED;
            $this->selectedPost['status'] = Post::STATUS_COMPLETED;
        }
        
        $post->save();

        // Refresh state
        $this->selectedPost['platforms'] = $platforms;

        // Redispatch if scheduled in future
        if (count($platforms) > 0 && $post->scheduled_at && $post->scheduled_at->isFuture()) {
            $this->dispatchPublishJobs($post, $post->scheduled_at);
        }

        $this->dispatch('calendar-move-blocked', message: 'Platformes mises à jour.');
    }

    public function reschedulePost(): void
    {
        $this->rescheduleError   = null;
        $this->rescheduleWarning = null;

        if (! $this->selectedPostId || ! $this->newScheduledDate) {
            return;
        }

        $post = Post::with('company')->where('user_id', Auth::id())->find($this->selectedPostId);
        if (! $post) {
            return;
        }

        $datetime = Carbon::parse($this->newScheduledDate . ' ' . ($this->newScheduledTime ?? '00:00'));

        // ── Guard: date dans le passé ───────────────────────────────────────────
        if ($datetime->isPast()) {
            // Si le post est déjà publié → on met juste à jour la date (historique)
            if ($post->status === 'published') {
                $post->scheduled_at = $datetime;
                $post->save();
                $this->closePost();
                session()->flash('success', 'Date d\'historique mise à jour.');
                return;
            }

            // Sinon → on bloque, on ne peut pas planifier dans le passé
            $this->rescheduleError = 'Impossible de planifier à une date passée. Choisissez une date future ou publiez immédiatement.';
            return;
        }

        // ── Nouveau scheduled_at en base ────────────────────────────────────────
        $post->scheduled_at = $datetime;
        $post->save();

        // ── Redispatch du job si le post est prévu pour publication auto ────────
        // Le job existant en queue se detecra lui-même (mismatch scheduled_at)
        // et s'annulera automatiquement.
        $this->dispatchPublishJobs($post, $datetime);

        // Sync vue mois sur la nouvelle date
        $this->year  = $datetime->year;
        $this->month = $datetime->month;

        $this->closePost();
        session()->flash('success', 'Publication replanifiée pour le ' . $datetime->locale('fr')->isoFormat('D MMM à HH:mm') . '.');
    }

    /**
     * Called by drag-and-drop: move a post to a new date.
     * Preserves the original time. Blocks if target date is in the past.
     */
    public function movePost(int $postId, string $newDate): void
    {
        $post = Post::with('company')->where('user_id', Auth::id())->find($postId);
        if (! $post) {
            return;
        }

        $currentDate = $post->scheduled_at ?? $post->created_at;
        $datetime    = Carbon::parse($newDate)->setTime(
            $currentDate->hour,
            $currentDate->minute,
        );

        // ── Guard: post déjà publié ───────────────────────────────────────────
        if ($post->status === Post::STATUS_PUBLISHED) {
            $this->dispatch('calendar-move-blocked', message: 'Impossible de déplacer un post déjà publié.');
            return;
        }

        // ── Guard: drop sur une date passée ────────────────────────────────────
        if ($datetime->isPast()) {
            $this->dispatch('calendar-move-blocked', message: 'Impossible de planifier dans le passé.');
            return;
        }

        $post->scheduled_at = $datetime;
        
        // Si déplacé dans le futur avec des plateformes actives
        // -> on le marque comme prêt (completed) et on met en queue
        $platforms = $post->platforms ?? [];
        if ($datetime->isFuture() && count($platforms) > 0) {
            $post->status = Post::STATUS_COMPLETED;
            $this->dispatchPublishJobs($post, $datetime);
        }
        
        $post->save();
    }

    /**
     * Dispatch publish jobs for all connected platforms.
     * The previously queued job will self-cancel on scheduled_at mismatch.
     */
    private function dispatchPublishJobs(Post $post, Carbon $datetime): void
    {
        $company   = $post->company;
        $platforms = $post->platforms ?? [];

        if (! $company) {
            return;
        }

        $delay         = $datetime->isFuture() ? $datetime : null;
        $scheduledAtStr = $datetime->toDateTimeString();

        if (in_array('instagram', $platforms) && $company->instagram_connected) {
            $job = PublishToInstagramJob::dispatch($post->id, $scheduledAtStr);
            if ($delay) $job->delay($delay);
        }

        if (in_array('facebook', $platforms) && $company->facebook_connected) {
            $job = PublishToFacebookJob::dispatch($post->id, $scheduledAtStr);
            if ($delay) $job->delay($delay);
        }
    }

    /**
     * Redirect to post generator with a pre-filled scheduled date.
     */
    public function createPostForDate(string $date): void
    {
        $this->redirect(route('generate-post', ['scheduled_date' => $date]), navigate: true);
    }

    // ── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        if ($this->view === 'week') {
            return $this->renderWeek();
        }

        return $this->renderMonth();
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function renderMonth()
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $posts = $this->fetchPosts($startOfMonth, $endOfMonth);

        $postsByDay = [];
        foreach ($posts as $post) {
            $day              = ($post->scheduled_at ?? $post->created_at)->day;
            $postsByDay[$day][] = $this->formatPost($post);
        }

        $firstDayOfWeek = $startOfMonth->dayOfWeekIso;
        $daysInMonth    = $startOfMonth->daysInMonth;
        $isCurrentMonth = now()->year === $this->year && now()->month === $this->month;

        return view('livewire.pages.calendar', [
            'postsByDay'     => $postsByDay,
            'firstDayOfWeek' => $firstDayOfWeek,
            'daysInMonth'    => $daysInMonth,
            'leadingBlanks'  => $firstDayOfWeek - 1,
            'today'          => now()->day,
            'isCurrentMonth' => $isCurrentMonth,
            'monthLabel'     => $startOfMonth->locale('fr')->isoFormat('MMMM YYYY'),
            'totalPosts'     => $posts->count(),
            'weekDays'       => null,
            'weekLabel'      => null,
            'isCurrentWeek'  => false,
        ]);
    }

    private function renderWeek()
    {
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY)->addWeeks($this->weekOffset);
        $endOfWeek   = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

        $posts = $this->fetchPosts($startOfWeek, $endOfWeek);

        // Build weekDays array (Mon=0 … Sun=6)
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day        = $startOfWeek->copy()->addDays($i);
            $weekDays[] = [
                'date'    => $day,
                'dayNum'  => $day->day,
                'dayName' => $day->locale('fr')->isoFormat('ddd'),
                'isToday' => $day->isToday(),
                'isPast'  => $day->lt(now()->startOfDay()) && ! $day->isToday(),
                'posts'   => [],
            ];
        }

        foreach ($posts as $post) {
            $date      = $post->scheduled_at ?? $post->created_at;
            $idx       = $date->dayOfWeekIso - 1; // Mon=0 … Sun=6
            if (isset($weekDays[$idx])) {
                $weekDays[$idx]['posts'][] = $this->formatPost($post);
            }
        }

        $weekLabel = $startOfWeek->locale('fr')->isoFormat('D MMM')
                   . ' – '
                   . $endOfWeek->locale('fr')->isoFormat('D MMM YYYY');

        return view('livewire.pages.calendar', [
            'postsByDay'     => [],
            'firstDayOfWeek' => 1,
            'daysInMonth'    => 0,
            'leadingBlanks'  => 0,
            'today'          => now()->day,
            'isCurrentMonth' => false,
            'monthLabel'     => $weekLabel,
            'totalPosts'     => $posts->count(),
            'weekDays'       => $weekDays,
            'weekLabel'      => $weekLabel,
            'isCurrentWeek'  => $this->weekOffset === 0,
        ]);
    }

    private function fetchPosts(Carbon $start, Carbon $end)
    {
        return Post::where('user_id', Auth::id())
            ->whereIn('status', [Post::STATUS_COMPLETED, Post::STATUS_REVIEW_PENDING, Post::STATUS_PUBLISHED])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('scheduled_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('scheduled_at')
                         ->whereBetween('created_at', [$start, $end]);
                  });
            })
            ->orderBy('created_at')
            ->get();
    }

    private function formatPost(Post $post): array
    {
        $date = $post->scheduled_at ?? $post->created_at;

        return [
            'id'        => $post->id,
            'template'  => $post->template,
            'scheduled' => $post->scheduled_at !== null,
            'thumbnail' => $this->getThumbnail($post),
            'caption'   => $post->caption,
            'time'      => $date->format('H:i'),
            'status'    => $post->status,
        ];
    }

    private function getThumbnail(Post $post): ?string
    {
        if ($post->slides && count($post->slides) > 0) {
            return Storage::url($post->slides[0]);
        }
        if ($post->image_path) {
            return Storage::url($post->image_path);
        }

        return null;
    }
}
