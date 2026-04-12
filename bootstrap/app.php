<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Fix for Windows/PHP 8.4 tmpfile() failure in Livewire
$tmpDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'tmp';
if (!is_dir($tmpDir)) @mkdir($tmpDir, 0777, true);
putenv("TMP=$tmpDir");
putenv("TEMP=$tmpDir");
ini_set('upload_tmp_dir', $tmpDir);

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (Cloudflare tunnel, etc.)
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\EnsureNotBanned::class,
            \App\Http\Middleware\AuditLogMiddleware::class,
        ]);
        $middleware->alias([
            'onboarding' => \App\Http\Middleware\EnsureOnboardingCompleted::class,
            'subscribed' => \App\Http\Middleware\EnsureSubscribed::class,
            'admin'      => \App\Http\Middleware\EnsureIsAdmin::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
