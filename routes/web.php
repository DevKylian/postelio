<?php

use App\Http\Controllers\InstagramController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('posts', [PostController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('posts.index');

Route::delete('posts/{post}', [PostController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('posts.destroy');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('generate-post', 'generate-post')
    ->middleware(['auth', 'verified'])
    ->name('generate-post');

Route::view('templates-preview', 'templates-preview')
    ->middleware(['auth', 'verified'])
    ->name('templates-preview');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('post-settings', 'post-settings')
    ->middleware(['auth', 'verified'])
    ->name('post-settings');

// Instagram OAuth
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/auth/instagram', [InstagramController::class, 'connect'])->name('instagram.connect');
    Route::get('/auth/instagram/callback', [InstagramController::class, 'callback'])->name('instagram.callback');
    Route::delete('/auth/instagram', [InstagramController::class, 'disconnect'])->name('instagram.disconnect');
});

require __DIR__.'/auth.php';
