<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

// Blog (public)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Legal pages (public)
Route::view('/mentions-legales', 'legal.mentions')->name('legal.mentions');
Route::view('/cgv', 'legal.cgv')->name('legal.cgv');
Route::view('/politique-confidentialite', 'legal.confidentialite')->name('legal.confidentialite');

// Stripe webhooks (no CSRF verification)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
    ->name('cashier.webhook');

// Onboarding (auth required, but NOT onboarding middleware)
Volt::route('onboarding', 'pages.onboarding')
    ->middleware(['auth', 'verified'])
    ->name('onboarding');

// Subscription routes (auth required, but no subscription check)
Route::middleware(['auth', 'verified', 'onboarding'])->group(function () {
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::post('/subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::get('/subscription/billing', [SubscriptionController::class, 'billing'])->name('subscription.billing');
    Route::post('/subscription/portal', [SubscriptionController::class, 'portal'])->name('subscription.portal');
    Route::post('/subscription/resume', [SubscriptionController::class, 'resume'])->name('subscription.resume');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::post('/subscription/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscription.change-plan');
});

// App routes (require auth + verified + onboarding completed)
Route::middleware(['auth', 'verified', 'onboarding'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('generate-post', 'generate-post')->name('generate-post');
    Route::view('templates-preview', 'templates-preview')->name('templates-preview');
    Route::view('calendar', 'calendar')->name('calendar');
    Route::view('post-settings', 'post-settings')->name('post-settings');
    Route::view('statistics', 'statistics')->name('statistics');
    Route::view('changelog', 'changelog')->name('changelog');

    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Admin routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/admin/users', \App\Livewire\Admin\Users\UserList::class)->name('admin.users.index');
        Route::get('/admin/users/{user}', \App\Livewire\Admin\Users\UserShow::class)->name('admin.users.show');
        Route::get('/admin/templates', \App\Livewire\Admin\Templates\TemplateManager::class)->name('admin.templates.index');
        Route::get('/admin/changelog', \App\Livewire\Admin\Changelog\ChangelogManager::class)->name('admin.changelog.index');
        Route::get('/admin/blog', \App\Livewire\Admin\Blog\BlogManager::class)->name('admin.blog.index');
        Route::get('/admin/server', \App\Livewire\Admin\ServerMonitor::class)->name('admin.server');
    });

    // Social & Sync routes (Premium only)
    Route::middleware(['subscribed'])->group(function () {
        // Instagram OAuth
        Route::get('/auth/instagram', [InstagramController::class, 'connect'])->name('instagram.connect');
        Route::get('/auth/instagram/callback', [InstagramController::class, 'callback'])->name('instagram.callback');
        Route::delete('/auth/instagram', [InstagramController::class, 'disconnect'])->name('instagram.disconnect');

        // Facebook OAuth
        Route::get('/auth/facebook', [FacebookController::class, 'connect'])->name('facebook.connect');
        Route::get('/auth/facebook/callback', [FacebookController::class, 'callback'])->name('facebook.callback');
        Route::delete('/auth/facebook', [FacebookController::class, 'disconnect'])->name('facebook.disconnect');

        // Zernio Sync
        Route::get('/auth/zernio/sync', [\App\Http\Controllers\ZernioController::class, 'sync'])->name('zernio.sync');
    });
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/render/post/{post}', [\App\Http\Controllers\RenderPostController::class, 'show'])->name('render.post');

// Social Auth
Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

require __DIR__.'/auth.php';
