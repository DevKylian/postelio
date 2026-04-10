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
        // Force HTTPS when behind a reverse proxy (Cloudflare tunnel, etc.)
        if (request()->header('X-Forwarded-Proto') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }

        // Force French locale for dates
        \Carbon\Carbon::setLocale('fr');
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra', 'fr_FR');
    }
}
