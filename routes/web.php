<?php

use App\Http\Controllers\InstagramController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

// Onboarding (auth required, but NOT onboarding middleware)
Volt::route('onboarding', 'pages.onboarding')
    ->middleware(['auth', 'verified'])
    ->name('onboarding');

// App routes (require auth + verified + onboarding completed)
Route::middleware(['auth', 'verified', 'onboarding'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('generate-post', 'generate-post')->name('generate-post');
    Route::view('templates-preview', 'templates-preview')->name('templates-preview');
    Route::view('calendar', 'calendar')->name('calendar');
    Route::view('post-settings', 'post-settings')->name('post-settings');
    Route::view('instagram-stats', 'instagram-stats')->name('instagram-stats');

    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Instagram OAuth
    Route::get('/auth/instagram', [InstagramController::class, 'connect'])->name('instagram.connect');
    Route::get('/auth/instagram/callback', [InstagramController::class, 'callback'])->name('instagram.callback');
    Route::delete('/auth/instagram', [InstagramController::class, 'disconnect'])->name('instagram.disconnect');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
