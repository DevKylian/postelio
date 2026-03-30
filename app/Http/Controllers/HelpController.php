<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Display the Help Center index / Categories list
     */
    public function index()
    {
        $categories = FaqCategory::with(['questions' => function ($query) {
            $query->orderBy('sort_order')->orderBy('created_at', 'desc');
        }])->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        
        return view('help.index', compact('categories'));
    }

    /**
     * Display a specific Help question based on the slug
     */
    public function show($slug)
    {
        $questionItem = FaqQuestion::where('slug', $slug)->firstOrFail();
        $categoryName = $questionItem->category->name ?? 'Général';

        // We wrap the model in an array-like structure so the view expecting $questionItem['title'] still works,
        // or we can just pass the model directly and update the view to use object syntax.
        // Let's pass the model directly, the view currently uses $questionItem['title'].
        // Eloquent models implement ArrayAccess, so $questionItem['title'] works perfectly without changing the view!

        return view('help.show', compact('questionItem', 'categoryName'));
    }
}
