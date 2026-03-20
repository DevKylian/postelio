<?php

use App\Http\Controllers\FacebookController;
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
    Route::view('statistics', 'statistics')->name('statistics');

    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Instagram OAuth
    Route::get('/auth/instagram', [InstagramController::class, 'connect'])->name('instagram.connect');
    Route::get('/auth/instagram/callback', [InstagramController::class, 'callback'])->name('instagram.callback');
    Route::delete('/auth/instagram', [InstagramController::class, 'disconnect'])->name('instagram.disconnect');

    // Facebook OAuth
    Route::get('/auth/facebook', [FacebookController::class, 'connect'])->name('facebook.connect');
    Route::get('/auth/facebook/callback', [FacebookController::class, 'callback'])->name('facebook.callback');
    Route::delete('/auth/facebook', [FacebookController::class, 'disconnect'])->name('facebook.disconnect');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
