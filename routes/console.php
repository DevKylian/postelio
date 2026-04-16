<?php

use App\Jobs\PublishToInstagramJob;
use App\Models\Post;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Render a single post slide to a PNG file via Browsershot.
 * Used internally for parallel slide generation.
 *
 * Usage: php artisan post:render-slide {htmlPath} {outputPath} {aspectRatio}
 */
Artisan::command('post:render-slide {htmlPath} {outputPath} {aspectRatio}', function (string $htmlPath, string $outputPath, string $aspectRatio) {
    if (!file_exists($htmlPath)) {
        $this->error("HTML file not found: {$htmlPath}");
        return 1;
    }

    $html   = file_get_contents($htmlPath);
    $width  = 360;
    $height = ($aspectRatio === '4:5') ? 450 : 360;

    $instance = \Spatie\Browsershot\Browsershot::html($html)
        ->windowSize($width, $height)
        ->deviceScaleFactor(4)
        ->clip(0, 0, $width, $height)
        ->noSandbox()
        ->waitUntilNetworkIdle()
        ->timeout(90);

    $chromePath = env('BROWSERSHOT_CHROME_PATH');
    if ($chromePath) {
        $instance->setChromePath($chromePath);
    }

    $imageData = $instance->screenshot();
    file_put_contents($outputPath, $imageData);
})->describe('Render a single post slide to PNG (used for parallel generation)');

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
