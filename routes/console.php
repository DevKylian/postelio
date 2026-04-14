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
        ->whereHas('company', fn ($q) => $q->whereNotNull('instagram_access_token'))
        ->each(function (Post $post) {
            PublishToInstagramJob::dispatch($post->id);
            $post->update(['scheduled_at' => null]);
        });
})->everyMinute()->name('publish-scheduled-posts')->withoutOverlapping();

// Sauvegardes de la base de données (2 fois par jour)
Schedule::command('backup:run --only-db')
    ->twiceDaily(3, 15)
    ->name('db-backup')
    ->appendOutputTo(storage_path('logs/backup.log'));

// Nettoyage des vieux backups (1 fois par jour)
Schedule::command('backup:clean')
    ->dailyAt('03:30')
    ->name('backup-cleanup')
    ->appendOutputTo(storage_path('logs/backup.log'));
