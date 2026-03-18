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

    public function mount(): void
    {
        $this->year  = (int) now()->format('Y');
        $this->month = (int) now()->format('n');
    }

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

    public function render()
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $posts = Post::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'review_pending'])
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('scheduled_at', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->whereNull('scheduled_at')
                         ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                  });
            })
            ->orderBy('created_at')
            ->get();

        // Group posts by calendar day (1-31)
        $postsByDay = [];
        foreach ($posts as $post) {
            $day = ($post->scheduled_at ?? $post->created_at)->day;
            $postsByDay[$day][] = [
                'id'          => $post->id,
                'template'    => $post->template,
                'scheduled'   => $post->scheduled_at !== null,
                'thumbnail'   => $this->getThumbnail($post),
                'caption'     => $post->caption,
            ];
        }

        $firstDayOfWeek = $startOfMonth->dayOfWeekIso; // 1=Mon … 7=Sun
        $daysInMonth    = $startOfMonth->daysInMonth;
        $today          = now()->day;
        $isCurrentMonth = now()->year === $this->year && now()->month === $this->month;

        // Previous month tail days to fill the first row
        $leadingBlanks = $firstDayOfWeek - 1;

        return view('livewire.pages.calendar', [
            'postsByDay'     => $postsByDay,
            'firstDayOfWeek' => $firstDayOfWeek,
            'daysInMonth'    => $daysInMonth,
            'leadingBlanks'  => $leadingBlanks,
            'today'          => $today,
            'isCurrentMonth' => $isCurrentMonth,
            'monthLabel'     => $startOfMonth->locale('fr')->isoFormat('MMMM YYYY'),
            'totalPosts'     => $posts->count(),
        ]);
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
