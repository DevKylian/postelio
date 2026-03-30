<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PwaController extends Controller
{
    /**
     * Handles post generation request synced from the offline queue.
     */
    public function queueGenerate(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // This is a simplified version of the logic in PostGenerator::generate()
        // In a real app, we might want to consolidate this logic in a Service.
        
        $data = $request->validate([
            'template'     => 'required|string',
            'tone'         => 'nullable|string',
            'audio'        => 'nullable|file|max:20000',
            'imageBefore'  => 'nullable|file|max:10000',
            'imageAfter'   => 'nullable|file|max:10000',
            'textContent'  => 'nullable|string',
            'visualStyle'  => 'nullable|string',
            'platforms'    => 'nullable|array',
            'scheduledAt'  => 'nullable|string',
        ]);

        // Store files
        $audioPath       = $request->hasFile('audio') ? $request->file('audio')->store('memos', 'public') : null;
        $imageBeforePath = $request->hasFile('imageBefore') ? $request->file('imageBefore')->store('projects', 'public') : null;
        $imageAfterPath  = $request->hasFile('imageAfter')  ? $request->file('imageAfter')->store('projects', 'public')  : null;

        // Construct template options (similar to PostGenerator)
        $templateOptions = [
            'tone'            => $data['tone'] ?? 'professionnel',
            'text_content'    => $data['textContent'] ?? null,
            'visual_style'    => $data['visualStyle'] ?? 'standard',
            'includeHashtags' => true,
        ];

        $post = Post::create([
            'user_id'           => $user->id,
            'company_id'        => $user->company->id,
            'audio_path'        => $audioPath,
            'image_path'        => in_array($data['template'], ['lifestyle', 'quality_focus']) ? $imageBeforePath : null,
            'image_before_path' => $imageBeforePath,
            'image_after_path'  => $imageAfterPath,
            'template'          => $data['template'],
            'template_options'  => $templateOptions,
            'platforms'         => $data['platforms'] ?? ['instagram'],
            'status'            => Post::STATUS_PENDING,
            'scheduled_at'      => isset($data['scheduledAt']) ? \Carbon\Carbon::parse($data['scheduledAt']) : null,
        ]);

        // Dispatch generation job
        \App\Jobs\ProcessPostGeneration::dispatch($post);

        return response()->json([
            'success' => true,
            'post_id' => $post->id,
            'message' => 'Post synchronisé et génération lancée.'
        ]);
    }

    /**
     * Returns pending offline queue items for the authenticated user.
     */
    public function pendingQueue(Request $request): JsonResponse
    {
        $user  = Auth::user();
        $posts = $user->posts()
            ->where('status', 'pending_sync')
            ->orderByDesc('created_at')
            ->get(['id', 'status', 'created_at', 'template_key']);

        return response()->json(['items' => $posts]);
    }
}
