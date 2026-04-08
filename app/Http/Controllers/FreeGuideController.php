<?php

namespace App\Http\Controllers;

use App\Models\FreeGuide;
use Illuminate\Http\Request;

class FreeGuideController extends Controller
{
    /**
     * Display a listing of the free guides.
     */
    public function index()
    {
        $guides = FreeGuide::published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('guides.index', compact('guides'));
    }

    /**
     * Display the specified free guide.
     */
    public function show(string $slug)
    {
        $guide = FreeGuide::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment downloads/views count once per session
        $sessionKey = 'viewed_guide_' . $guide->id;
        if (!session()->has($sessionKey)) {
            $guide->increment('downloads_count');
            session()->put($sessionKey, true);
        }

        // Get other guides for the sidebar/footer
        $otherGuides = FreeGuide::published()
            ->where('id', '!=', $guide->id)
            ->limit(3)
            ->get();

        return view('guides.show', compact('guide', 'otherGuides'));
    }

    /**
     * Subscribe a lead to a free guide.
     */
    public function subscribe(Request $request, string $slug)
    {
        $guide = FreeGuide::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $request->validate([
            'email' => 'required|email',
        ]);

        // Save lead if not already existing for this guide
        \App\Models\FreeGuideLead::firstOrCreate([
            'free_guide_id' => $guide->id,
            'email' => $request->email,
        ]);

        // Mark as unlocked in session
        session()->put('unlocked_guide_' . $guide->id, true);

        return back()->with('success', 'Bienvenue ! Le guide est maintenant déverrouillé.');
    }
}
