<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPostGeneration;
use App\Models\CustomTemplate;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PwaController extends Controller
{
    /**
     * Handles a post generation request synced from the offline queue.
     * Mirrors the logic in PostGenerator::generate().
     */
    public function queueGenerate(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'template'         => 'required|string',
            'tone'             => 'nullable|string|in:professionnel,chaleureux,expert,dynamique',
            'audio'            => 'nullable|file|max:20480',
            'imageBefore'      => 'nullable|file|image|max:10240',
            'imageAfter'       => 'nullable|file|image|max:10240',
            'extraImages'      => 'nullable|array|max:5',
            'extraImages.*'    => 'file|image|max:10240',
            'textContent'      => 'nullable|string|max:2000',
            'visualStyle'      => 'nullable|string',
            'platforms'        => 'nullable|array',
            'platforms.*'      => 'string',
            'scheduledAt'      => 'nullable|string',
            'customTemplateId' => 'nullable|integer',
            'includeHashtags'  => 'nullable',
            'clientName'       => 'nullable|string|max:100',
            'serviceType'      => 'nullable|string|max:200',
            'workCategory'     => 'nullable|string|max:200',
            'jobDuration'      => 'nullable|string|max:100',
            'faqCategory'      => 'nullable|string|max:100',
            'season'           => 'nullable|string',
            'targetSlides'     => 'nullable|integer|min:3|max:10',
            'customNote'       => 'nullable|string|max:300',
        ]);

        // ── Store uploaded files ───────────────────────────────────────────────
        $audioPath       = $request->hasFile('audio')       ? $request->file('audio')->store('memos', 'public')      : null;
        $imageBeforePath = $request->hasFile('imageBefore') ? $request->file('imageBefore')->store('projects', 'public') : null;
        $imageAfterPath  = $request->hasFile('imageAfter')  ? $request->file('imageAfter')->store('projects', 'public')  : null;

        $extraImagePaths = [];
        if ($request->hasFile('extraImages')) {
            foreach ($request->file('extraImages') as $img) {
                $extraImagePaths[] = $img->store('projects', 'public');
            }
        }

        // ── Resolve custom template options ───────────────────────────────────
        $customTemplatePrompt = null;
        $customVisualStyle    = null;
        $customLayoutType     = 'standard';
        $customTemplateName   = null;
        $customPhotoModeOpt   = 'none';

        if ($data['template'] === 'custom' && !empty($data['customTemplateId'])) {
            $ct = CustomTemplate::where('id', $data['customTemplateId'])
                                ->where('user_id', $user->id)
                                ->first();
            if ($ct) {
                $customTemplatePrompt = $ct->ai_prompt;
                $customVisualStyle    = $ct->visual_style;
                $customLayoutType     = $ct->layout_type ?? 'standard';
                $customTemplateName   = $ct->name;
                $customPhotoModeOpt   = $ct->photo_mode ?? 'none';
            }
        }

        // ── Build template options (mirrors PostGenerator::generate) ──────────
        $visualStyle = $data['visualStyle'] ?? 'standard';
        if (!$user->hasFullTemplateAccess()) {
            $visualStyle = 'standard';
        }

        $templateOptions = [
            'tone'                   => $data['tone'] ?? 'professionnel',
            'includeHashtags'        => filter_var($data['includeHashtags'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'clientName'             => $data['clientName'] ?? null,
            'serviceType'            => $data['serviceType'] ?? null,
            'workCategory'           => $data['workCategory'] ?? null,
            'jobDuration'            => $data['jobDuration'] ?? null,
            'faqCategory'            => $data['faqCategory'] ?? null,
            'season'                 => ($data['season'] ?? 'auto') !== 'auto' ? ($data['season'] ?? null) : null,
            'targetSlides'           => (int) ($data['targetSlides'] ?? 5),
            'customNote'             => $data['customNote'] ?? null,
            'custom_template_id'     => $data['customTemplateId'] ?? null,
            'custom_template_prompt' => $customTemplatePrompt,
            'custom_visual_style'    => $customVisualStyle,
            'custom_layout_type'     => $customLayoutType,
            'custom_template_name'   => $customTemplateName,
            'custom_photo_mode'      => $customPhotoModeOpt,
            'text_content'           => !empty(trim($data['textContent'] ?? '')) ? trim($data['textContent']) : null,
            'visual_style'           => $visualStyle,
            'company_phone'          => $user->company?->phone,
            'company_email'          => $user->company?->email,
            'company_website'        => $user->company?->website,
            'company_city'           => $user->company?->city,
            'company_trade'          => $user->company?->trade,
        ];

        // ── Sanitize platforms ─────────────────────────────────────────────────
        $selectedPlatforms = is_array($data['platforms'] ?? null) ? $data['platforms'] : ['instagram'];
        if (!$user->canPublishToSocialMedia()) {
            $selectedPlatforms = array_values(array_diff($selectedPlatforms, ['instagram', 'facebook']));
        }

        // ── Create the Post record ─────────────────────────────────────────────
        $template = $data['template'];
        $post = Post::create([
            'user_id'           => $user->id,
            'company_id'        => $user->company->id,
            'audio_path'        => $audioPath,
            'image_path'        => in_array($template, ['lifestyle', 'quality_focus']) ? $imageBeforePath : null,
            'image_before_path' => $imageBeforePath,
            'image_after_path'  => $imageAfterPath,
            'extra_images'      => count($extraImagePaths) ? $extraImagePaths : null,
            'template'          => $template,
            'template_options'  => $templateOptions,
            'platforms'         => $selectedPlatforms,
            'status'            => Post::STATUS_PENDING,
            'scheduled_at'      => !empty($data['scheduledAt']) ? Carbon::parse($data['scheduledAt']) : null,
        ]);

        ProcessPostGeneration::dispatch($post);

        return response()->json([
            'success' => true,
            'post_id' => $post->id,
            'message' => 'Post synchronisé et génération lancée.',
        ]);
    }

    /**
     * Returns pending offline queue items for the authenticated user.
     */
    public function pendingQueue(Request $request): JsonResponse
    {
        $user  = Auth::user();
        $posts = $user->posts()
            ->whereIn('status', [Post::STATUS_PENDING, 'pending_sync'])
            ->orderByDesc('created_at')
            ->get(['id', 'status', 'created_at', 'template']);

        return response()->json(['items' => $posts]);
    }
}
