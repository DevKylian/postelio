<?php

namespace App\Http\Controllers;

use App\Jobs\TranscribeBioAudio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    /**
     * Upload a bio audio file during onboarding.
     * Returns the storage path for the Livewire component to store.
     */
    public function uploadBioAudio(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => ['required', 'file', 'mimes:webm,ogg,mp4,wav,m4a', 'max:20480'],
        ]);

        $user = Auth::user();
        $file = $request->file('audio');
        $ext  = $file->getClientOriginalExtension() ?: 'webm';
        $path = 'bio-audio/' . $user->id . '_' . Str::uuid() . '.' . $ext;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return response()->json([
            'path' => $path,
        ]);
    }

    /**
     * Dispatch transcription job for bio audio (called after onboarding complete).
     */
    public function transcribeBioAudio(Request $request): JsonResponse
    {
        $request->validate([
            'audio_path' => ['required', 'string'],
        ]);

        $user    = Auth::user();
        $company = $user->company;

        if (! $company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        TranscribeBioAudio::dispatch($company->id, $request->audio_path);

        return response()->json(['status' => 'queued']);
    }
}
