<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS and same URL when behind a reverse proxy (Cloudflare tunnel, etc.)
        if (request()->header('X-Forwarded-Proto') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }

        // Fix for 'artisan optimize' caching wrong APP_URL (e.g. 127.0.0.1 vs localhost vs tunnel)
        if (!app()->runningInConsole()) {
            $currentUrl = request()->getSchemeAndHttpHost();
            URL::forceRootUrl($currentUrl);
            config(['app.url' => $currentUrl]);
        }
    }
}
