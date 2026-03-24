<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class RenderPostController extends Controller
{
    public function show(Request $request, Post $post)
    {
        return view('render.post', [
            'post' => $post,
            'slideIndex' => $request->query('slideIndex'),
        ]);
    }
}
