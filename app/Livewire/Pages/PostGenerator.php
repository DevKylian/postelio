<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Models\CustomTemplate;
use App\Jobs\ProcessPostGeneration;
use App\Jobs\PublishToInstagramJob;
use Illuminate\Support\Facades\Auth;

class PostGenerator extends Component
{
    use WithFileUploads;

    public $audio;
    public $imageBefore;
    public $imageAfter;
    public $extraImages = [];        // for chantier_detail & step_by_step (up to 5)
    public $template = 'review';
    public $scheduleType = 'now';    // 'now' | 'scheduled'
    public $scheduledAt  = '';       // datetime-local string
    public $status = 'idle';
    public $errorMessage = null;
    public $generatedPostId;
    public $previewImageUrl;
    public $previewContent;
    public $previewSlides = [];      // array of URLs for carousel preview

    // ── Template customization options ──
    public $tone          = 'professionnel'; // professionnel | chaleureux | expert | dynamique
    public $includeHashtags = true;
    public $clientName    = '';    // review: optional client first name
    public $serviceType   = '';    // review: type of work done
    public $workCategory  = '';    // project: type of works
    public $jobDuration   = '';    // project: duration
    public $faqCategory   = '';    // faq: category
    public $season        = 'auto'; // conseil_saisonnier: season
    public $targetSlides  = '5';   // guide_pedagogique: desired slide count
    public $customNote      = '';    // any template: free-form extra context for AI
    public $customTemplateId = null; // ID of the selected CustomTemplate when template='custom'
    public $isAnalyzing     = false;
    public $analysisSuccess  = false;

    // Carousel templates
    const CAROUSEL_TEMPLATES  = ['chantier_detail', 'step_by_step', 'guide_pedagogique'];
    // Templates requiring photos
    const PHOTO_TEMPLATES     = ['project', 'lifestyle', 'quality_focus', 'chantier_detail', 'step_by_step'];
    // Templates requiring before+after
    const BEFORE_AFTER_TEMPLATES = ['project'];

    public function updatedAudio()
    {
        if (!$this->audio) return;

        $this->isAnalyzing = true;
        $this->analysisSuccess = false;

        try {
            // 1. Store temporarily to have a local path
            $path = $this->audio->store('temp_memos', 'public');

            // 2. Transcribe
            $transcriber = app(\App\Services\PostGeneration\TranscriptionService::class);
            $transcript = $transcriber->transcribe($path);

            // 3. Extract fields
            $refiner = app(\App\Services\PostGeneration\ContentRefinerService::class);
            $extracted = $refiner->extractFields($transcript);

            // 4. Update properties (only if not null)
            if (!empty($extracted['clientName']))    $this->clientName    = $extracted['clientName'];
            if (!empty($extracted['serviceType']))   $this->serviceType   = $extracted['serviceType'];
            if (!empty($extracted['workCategory']))  $this->workCategory  = $extracted['workCategory'];
            if (!empty($extracted['jobDuration']))   $this->jobDuration   = $extracted['jobDuration'];
            if (!empty($extracted['faqCategory']))   $this->faqCategory   = $extracted['faqCategory'];
            if (!empty($extracted['season']))        $this->season        = $extracted['season'];
            if (!empty($extracted['tone']))          $this->tone          = $extracted['tone'];

            $this->analysisSuccess = true;

            // Optional: cleanup temp file
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Audio analysis failed: ' . $e->getMessage());
        } finally {
            $this->isAnalyzing = false;
        }
    }

    protected function rules()
    {
        $needsPhoto = in_array($this->template, self::PHOTO_TEMPLATES);
        $needsAfter = in_array($this->template, self::BEFORE_AFTER_TEMPLATES);
        $needsExtra = in_array($this->template, ['chantier_detail', 'step_by_step']);

        // For custom templates, derive photo requirements from the saved photo_mode
        if ($this->template === 'custom' && $this->customTemplateId) {
            $ct = CustomTemplate::where('id', $this->customTemplateId)
                                ->where('user_id', Auth::id())
                                ->first();
            $photoMode = $ct?->photo_mode ?? 'none';
            $needsPhoto = $photoMode === 'single';
            $needsExtra = $photoMode === 'carousel';
        }

        return [
            'audio'         => 'required|max:10240',
            'template'      => 'required|in:review,project,faq,lifestyle,quality_focus,conseil_saisonnier,erreur_a_eviter,chantier_detail,step_by_step,guide_pedagogique,custom',
            'imageBefore'   => ($needsPhoto && !$needsExtra) ? 'required|image|max:5120' : 'nullable|image|max:5120',
            'imageAfter'    => $needsAfter ? 'required|image|max:5120' : 'nullable',
            'extraImages'   => $needsExtra ? 'required|array|min:2|max:5' : 'nullable|array',
            'extraImages.*' => 'image|max:5120',
            'scheduleType'  => 'required|in:now,scheduled',
            'scheduledAt'   => $this->scheduleType === 'scheduled' ? 'required|date|after:now' : 'nullable',
            'tone'          => 'required|in:professionnel,chaleureux,expert,dynamique',
            'customNote'    => 'nullable|string|max:300',
        ];
    }

    public function selectCustomTemplate(int $id): void
    {
        $this->template         = 'custom';
        $this->customTemplateId = $id;
    }

    public function generate()
    {
        $this->validate();

        $this->status = 'processing';

        // Store files
        $audioPath       = $this->audio->store('memos', 'public');
        $imageBeforePath = $this->imageBefore ? $this->imageBefore->store('projects', 'public') : null;
        $imageAfterPath  = $this->imageAfter  ? $this->imageAfter->store('projects', 'public')  : null;

        $extraImagePaths = [];
        foreach ($this->extraImages as $img) {
            $extraImagePaths[] = $img->store('projects', 'public');
        }

        // For custom templates, inject the template's ai_prompt and visual style
        $customTemplatePrompt = null;
        $customVisualStyle    = null;
        $customTemplateName   = null;
        if ($this->template === 'custom' && $this->customTemplateId) {
            $ct = CustomTemplate::where('id', $this->customTemplateId)
                                ->where('user_id', Auth::id())
                                ->first();
            if ($ct) {
                $customTemplatePrompt = $ct->ai_prompt;
                $customVisualStyle    = $ct->visual_style;
                $customTemplateName   = $ct->name;
            }
        }

        $templateOptions = [
            'tone'                   => $this->tone,
            'includeHashtags'        => $this->includeHashtags,
            'clientName'             => $this->clientName ?: null,
            'serviceType'            => $this->serviceType ?: null,
            'workCategory'           => $this->workCategory ?: null,
            'jobDuration'            => $this->jobDuration ?: null,
            'faqCategory'            => $this->faqCategory ?: null,
            'season'                 => $this->season !== 'auto' ? $this->season : null,
            'targetSlides'           => (int) $this->targetSlides,
            'customNote'             => $this->customNote ?: null,
            'custom_template_id'     => $this->customTemplateId ?: null,
            'custom_template_prompt' => $customTemplatePrompt,
            'custom_visual_style'    => $customVisualStyle,
            'custom_template_name'   => $customTemplateName,
        ];

        $post = Post::create([
            'user_id'           => Auth::id(),
            'company_id'        => Auth::user()->company->id,
            'audio_path'        => $audioPath,
            'image_before_path' => $imageBeforePath,
            'image_after_path'  => $imageAfterPath,
            'extra_images'      => count($extraImagePaths) ? $extraImagePaths : null,
            'template'          => $this->template,
            'template_options'  => $templateOptions,
            'status'            => 'pending',
            'scheduled_at'      => ($this->scheduleType === 'scheduled' && $this->scheduledAt)
                                    ? \Carbon\Carbon::parse($this->scheduledAt)
                                    : null,
        ]);

        $this->generatedPostId = $post->id;

        try {
            ProcessPostGeneration::dispatchSync($post);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PostGenerator: job failed', [
                'post_id' => $post->id,
                'error'   => $e->getMessage(),
            ]);
            $this->errorMessage = $e->getMessage();
            $this->status = 'failed';
            return;
        }

        $post->refresh();

        if ($post->status === 'review_pending') {
            $this->previewContent = $post->caption;

            if ($post->slides && count($post->slides) > 0) {
                $this->previewSlides  = array_map(
                    fn($p) => \Illuminate\Support\Facades\Storage::url($p),
                    $post->slides
                );
                $this->previewImageUrl = $this->previewSlides[0];
            } else {
                $this->previewImageUrl = $post->image_path
                    ? \Illuminate\Support\Facades\Storage::url($post->image_path)
                    : null;
                $this->previewSlides = [];
            }

            $this->status = 'review_pending';
        } else {
            $this->status = 'failed';
        }
    }

    public function validatePost()
    {
        $post = Post::find($this->generatedPostId);

        if ($post && $post->user_id === Auth::id()) {
            $post->update(['status' => 'completed']);

            $company = Auth::user()->company;
            if ($company && $company->instagram_connected) {
                PublishToInstagramJob::dispatch($post->id);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Votre post a été validé et ajouté à vos créations !');
    }

    public function discardPost()
    {
        $post = Post::find($this->generatedPostId);

        if ($post && $post->user_id === Auth::id()) {
            $allPaths = array_filter(array_merge(
                [$post->audio_path, $post->image_path, $post->image_before_path, $post->image_after_path],
                $post->slides       ?? [],
                $post->extra_images ?? []
            ));
            foreach ($allPaths as $path) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            }
            $post->delete();
        }

        $this->status            = 'idle';
        $this->errorMessage      = null;
        $this->generatedPostId   = null;
        $this->previewImageUrl   = null;
        $this->previewContent    = null;
        $this->previewSlides     = [];
    }

    public function render()
    {
        $customTemplates = CustomTemplate::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        $customPhotoMode = 'none';
        if ($this->template === 'custom' && $this->customTemplateId) {
            $ct = $customTemplates->firstWhere('id', $this->customTemplateId);
            $customPhotoMode = $ct?->photo_mode ?? 'none';
        }

        return view('livewire.pages.post-generator', compact('customTemplates', 'customPhotoMode'));
    }
}
