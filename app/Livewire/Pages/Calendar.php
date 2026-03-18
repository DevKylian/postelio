<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Calendar extends Component
{
    public int $year;
    public int $month;
    public string $view = 'month'; // 'month' | 'week'
    public int $weekOffset = 0;    // offset in weeks from current week

    // Post detail modal
    public ?int $selectedPostId     = null;
    public ?array $selectedPost     = null;
    public ?string $newScheduledDate = null;
    public ?string $newScheduledTime = null;

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
        ];
        $this->newScheduledDate = $date->format('Y-m-d');
        $this->newScheduledTime = $date->format('H:i');
    }

    public function closePost(): void
    {
        $this->selectedPostId   = null;
        $this->selectedPost     = null;
        $this->newScheduledDate = null;
        $this->newScheduledTime = null;
    }

    public function reschedulePost(): void
    {
        if (! $this->selectedPostId || ! $this->newScheduledDate) {
            return;
        }

        $post = Post::where('user_id', Auth::id())->find($this->selectedPostId);
        if (! $post) {
            return;
        }

        $datetime           = Carbon::parse($this->newScheduledDate . ' ' . ($this->newScheduledTime ?? '00:00'));
        $post->scheduled_at = $datetime;
        $post->save();

        // Sync month view to the new date
        $this->year  = $datetime->year;
        $this->month = $datetime->month;

        $this->closePost();
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
            ->whereIn('status', ['completed', 'review_pending'])
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
