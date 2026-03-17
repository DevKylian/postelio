<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the generated posts.
     */
    public function index()
    {
        $posts = Post::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('posts.index', compact('posts'));
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        // Ensure user owns the post
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        // 1. Delete Files from Storage
        $filesToDelete = [
            $post->audio_path,
            $post->image_path,
            $post->image_before_path,
            $post->image_after_path
        ];

        foreach ($filesToDelete as $path) {
            if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }

        // 2. Delete Record
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post supprimé avec succès.');
    }
}
