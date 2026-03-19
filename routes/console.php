<?php

use App\Jobs\PublishToInstagramJob;
use App\Models\Post;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Chaque minute : dispatcher les posts Instagram programmés arrivés à échéance
Schedule::call(function () {
    Post::where('status', 'completed')
        ->whereNotNull('scheduled_at')
        ->where('scheduled_at', '<=', now())
        ->whereHas('company', fn ($q) => $q->where('instagram_connected', true))
        ->each(function (Post $post) {
            PublishToInstagramJob::dispatch($post->id);
            // Effacer scheduled_at pour éviter un double-dispatch lors du prochain tick
            $post->update(['scheduled_at' => null]);
        });
})->everyMinute()->name('publish-scheduled-posts')->withoutOverlapping();
