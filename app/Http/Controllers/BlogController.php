<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Schema;

class BlogController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('blog_posts')) {
            $posts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);
            return view('blog.index', compact('posts'));
        }

        $posts = BlogPost::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views count once per session
        $sessionKey = 'viewed_post_' . $post->id;
        if (!session()->has($sessionKey)) {
            $post->increment('views_count');
            session()->put($sessionKey, true);
        }

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
