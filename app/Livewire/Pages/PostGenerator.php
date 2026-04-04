<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Models\CustomTemplate;
use App\Jobs\ProcessPostGeneration;
use Illuminate\Support\Facades\Auth;

class PostGenerator extends Component
{
    use WithFileUploads;

    public $audio;
    public $imageBefore;
    public $imageAfter;
    public $extraImages = [];        // for chantier_detail & step_by_step (up to 5)
    public $template = 'review';
    public $platforms = [];                  // dynamically populated in mount()
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
    public $textContent     = '';    // Free-text alternative to audio
    public $isAnalyzing     = false;
    public $analysisSuccess  = false;
    public $visualStyle     = 'standard'; // standard | glass | blueprint | kraft | beton
    public $aspectRatio     = '4:5';      // 1:1 | 4:5 (default 4:5)
    public bool $isFeatureDisabled = false;
    public int $currentStep = 1;

    // Carousel templates
    const CAROUSEL_TEMPLATES  = ['chantier_detail', 'step_by_step', 'guide_pedagogique', 'project', 'faq'];
    // Templates requiring photos
    const PHOTO_TEMPLATES     = ['project', 'lifestyle', 'quality_focus', 'chantier_detail', 'step_by_step'];
    // Templates requiring before+after
    const BEFORE_AFTER_TEMPLATES = ['project'];

    public function mount()
    {
        $this->isFeatureDisabled = !\App\Models\Setting::get('post_generation_enabled', true);

        $company = Auth::user()->company;
        $this->platforms = [];
        if ($company?->instagram_connected) $this->platforms[] = 'instagram';
        if ($company?->facebook_connected) $this->platforms[] = 'facebook';
    }

    public function updatedAudio()
    {
        \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] updatedAudio triggered', [
            'has_audio' => (bool)$this->audio,
            'audio_name' => $this->audio?->getClientOriginalName()
        ]);
        
        $this->analyzeAudio();
    }

    public function analyzeAudio()
    {
        if (!$this->audio) return;

        $this->isAnalyzing = true;
        $this->analysisSuccess = false;

        try {
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] analyzeAudio started');
            // 1. Store temporarily to have a local path
            $path = $this->audio->store('temp_memos', 'public');
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] File stored', ['path' => $path]);

            // 2. Transcribe
            $transcriber = app(\App\Services\PostGeneration\TranscriptionService::class);
            $transcript = $transcriber->transcribe($path);
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] Transcription finished', ['length' => strlen($transcript)]);

            // 3. Pré-remplir le champ texte avec la transcription brute
            $this->textContent = $transcript;

            // 4. Extract fields
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] Starting field extraction');
            $refiner = app(\App\Services\PostGeneration\ContentRefinerService::class);
            $extracted = $refiner->extractFields($transcript);
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] Field extraction finished', ['extracted' => $extracted]);

            // 5. Update properties (only if not null)
            if (!empty($extracted['clientName']))    $this->clientName    = $extracted['clientName'];
            if (!empty($extracted['serviceType']))   $this->serviceType   = $extracted['serviceType'];
            if (!empty($extracted['workCategory']))  $this->workCategory  = $extracted['workCategory'];
            if (!empty($extracted['jobDuration']))   $this->jobDuration   = $extracted['jobDuration'];
            if (!empty($extracted['faqCategory']))   $this->faqCategory   = $extracted['faqCategory'];
            if (!empty($extracted['season']))        $this->season        = $extracted['season'];
            if (!empty($extracted['tone']))          $this->tone          = $extracted['tone'];

            $this->analysisSuccess = true;

            // 6. Basculer l'onglet vers "Écrire" avec le texte retranscrit
            $this->dispatch('switch-to-text-mode');

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

        $nativeSettings = \App\Models\NativeTemplateSetting::all()->pluck('is_active', 'slug');
        $activeNativeSlugs = collect($nativeSettings)->filter(fn($active) => $active)->keys()->toArray();
        $baseSlugs = ['review','project','faq','lifestyle','quality_focus','conseil_saisonnier','erreur_a_eviter','chantier_detail','step_by_step','guide_pedagogique'];
        // Fallback to defaults if table is empty
        $finalNativeSlugs = count($nativeSettings) > 0 ? $activeNativeSlugs : $baseSlugs;
        $allowedTemplates = array_merge($finalNativeSlugs, ['custom']);

        $hasInput = $this->audio || !empty(trim($this->textContent));

        return [
            'audio'         => (!$hasInput || $this->audio) ? 'nullable|max:10240' : 'nullable',
            'textContent'   => (!$this->audio) ? 'required_without:audio|string|max:2000' : 'nullable|string|max:2000',
            'template'      => 'required|in:' . implode(',', $allowedTemplates),
            'imageBefore'   => ($needsPhoto && !$needsExtra) ? 'required|image|max:5120' : 'nullable|image|max:5120',
            'imageAfter'    => $needsAfter ? 'required|image|max:5120' : 'nullable',
            'extraImages'   => $needsExtra ? 'required|array|min:2|max:5' : 'nullable|array',
            'extraImages.*' => 'image|max:5120',
            'platforms'     => 'nullable|array',
            'platforms.*'   => 'string',
            'scheduleType'  => 'required|in:now,scheduled',
            'scheduledAt'   => $this->scheduleType === 'scheduled' ? 'required|date|after:now' : 'nullable',
            'tone'          => 'required|in:professionnel,chaleureux,expert,dynamique',
            'visualStyle'   => 'required|in:' . (Auth::user()->hasFullTemplateAccess() ? 'standard,glass,blueprint,kraft,beton' : 'standard'),
            'aspectRatio'   => 'required|in:1:1,4:5',
        ];
    }

    public function nextStep()
    {
        $user = Auth::user();
        $user->checkAndResetQuota();

        if ($this->currentStep === 1) {
            $this->validateOnly('template');
            if ($user->remaining_credits <= 0) {
                $this->addError('template', "Votre quota est épuisé. (0 post restant).");
                return;
            }
        } elseif ($this->currentStep === 2) {
            // Either audio or text is required
            if (!$this->audio && empty(trim($this->textContent))) {
                $this->addError('textContent', 'Enregistrez un mémo, importez un fichier audio, ou saisissez votre texte.');
                return;
            }
            $this->validateOnly('textContent');
            $this->validateOnly('tone');
        } elseif ($this->currentStep === 3) {
            $this->validateOnly('visualStyle');
            
            $needsPhoto = in_array($this->template, self::PHOTO_TEMPLATES);
            if ($this->template === 'custom' && $this->customTemplateId) {
                $ct = CustomTemplate::query()->find($this->customTemplateId);
                if ($ct && ($ct->photo_mode === 'single' || $ct->photo_mode === 'carousel')) {
                    $needsPhoto = true;
                }
            }
            
            if ($needsPhoto) {
                if (in_array($this->template, self::BEFORE_AFTER_TEMPLATES)) {
                    $this->validateOnly('imageBefore');
                    $this->validateOnly('imageAfter');
                } elseif (in_array($this->template, ['chantier_detail', 'step_by_step'])) {
                    $this->validateOnly('extraImages');
                } else {
                    $this->validateOnly('imageBefore');
                }
            }
        }

        $this->currentStep++;
        $this->dispatch('scroll-to-top');
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('scroll-to-top');
        }
    }

    public function goToStep(int $step)
    {
        if ($step < $this->currentStep) {
            $this->currentStep = $step;
            $this->dispatch('scroll-to-top');
        }
    }

    public function selectCustomTemplate(int $id): void
    {
        $this->template         = 'custom';
        $this->customTemplateId = $id;
    }

    public function selectNativeTemplate(string $id): void
    {
        $this->template         = $id;
        $this->customTemplateId = null;
    }

    public function updatedVisualStyle($value)
    {
        if (!Auth::user()->hasFullTemplateAccess() && $value !== 'standard') {
            $this->visualStyle = 'standard';
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Ce style est réservé aux membres Premium.']);
        }
    }

    public function updatedPlatforms()
    {
        if (!auth()->user()->canPublishToSocialMedia()) {
            $platforms = is_array($this->platforms) ? $this->platforms : (is_string($this->platforms) && $this->platforms ? [$this->platforms] : []);
            $this->platforms = array_values(array_diff($platforms, ['instagram', 'facebook']));
        }
    }

    public function generate()
    {
        \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] PostGenerator::generate() started');
        
        if (!\App\Models\Setting::get('post_generation_enabled', true)) {
            $this->addError('quota', "La génération de posts est temporairement désactivée pour maintenance.");
            return;
        }

        try {
            $this->validate();
            \Illuminate\Support\Facades\Log::info('[AUDIO DEBUG] Validation PASSED');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning('[AUDIO DEBUG] Validation FAILED', ['errors' => $e->errors()]);
            throw $e;
        }

        $user = Auth::user();
        $user->checkAndResetQuota();

        // Generate 1 post = 1 unit from quota
        $cost = 1;

        if (!$user->hasCredits($cost)) {
            $this->addError('quota', "Vous avez atteint votre quota. (Restant : {$user->remaining_credits} post(s)). Veuillez mettre à niveau votre abonnement.");
            return;
        }

        // Require at least audio OR text
        if (!$this->audio && empty(trim($this->textContent))) {
            $this->addError('audio', 'Enregistrez un mémo, importez un fichier audio, ou saisissez votre texte.');
            return;
        }

        // Store files
        $audioPath       = $this->audio ? $this->audio->store('memos', 'public') : null;
        $imageBeforePath = $this->imageBefore ? $this->imageBefore->store('projects', 'public') : null;
        $imageAfterPath  = $this->imageAfter  ? $this->imageAfter->store('projects', 'public')  : null;

        $extraImagePaths = [];
        foreach ($this->extraImages as $img) {
            $extraImagePaths[] = $img->store('projects', 'public');
        }

        // For custom templates, inject the template's ai_prompt, visual style and photo_mode
        $customTemplatePrompt = null;
        $customVisualStyle    = null;
        $customLayoutType     = 'standard';
        $customTemplateName   = null;
        $customPhotoModeOpt   = 'none';
        if ($this->template === 'custom' && $this->customTemplateId) {
            $ct = CustomTemplate::where('id', $this->customTemplateId)
                                ->where('user_id', Auth::id())
                                ->first();
            if ($ct) {
                $customTemplatePrompt = $ct->ai_prompt;
                $customVisualStyle    = $ct->visual_style;
                $customLayoutType     = $ct->layout_type ?? 'standard';
                $customTemplateName   = $ct->name;
                $customPhotoModeOpt   = $ct->photo_mode ?? 'none';
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
            'custom_layout_type'     => $customLayoutType,
            'custom_template_name'   => $customTemplateName,
            'custom_photo_mode'      => $customPhotoModeOpt,
            'text_content'           => !empty(trim($this->textContent)) ? trim($this->textContent) : null,
            'visual_style'           => $this->visualStyle,
            'company_phone'          => Auth::user()->company?->phone,
            'company_email'          => Auth::user()->company?->email,
            'company_website'        => Auth::user()->company?->website,
            'company_city'           => Auth::user()->company?->city,
            'company_trade'          => Auth::user()->company?->trade,
            'aspect_ratio'           => $this->aspectRatio,
        ];

        // Sanitize platforms array (livewire might cast to string or bool if only one checkbox exists)
        $selectedPlatforms = is_array($this->platforms) ? $this->platforms : (is_string($this->platforms) && $this->platforms ? [$this->platforms] : []);
        if ($this->platforms === true) {
            $company = Auth::user()->company;
            $selectedPlatforms = $company?->instagram_connected ? ['instagram'] : ($company?->facebook_connected ? ['facebook'] : []);
        }

        // Premium security: filter out social platforms for free users
        if (!auth()->user()->canPublishToSocialMedia()) {
            $selectedPlatforms = array_values(array_diff($selectedPlatforms, ['instagram', 'facebook']));
        }
        
        $this->platforms = $selectedPlatforms;

        $post = Post::create([
            'user_id'           => Auth::id(),
            'company_id'        => Auth::user()->company->id,
            'audio_path'        => $audioPath,
            'image_path'        => in_array($this->template, ['lifestyle', 'quality_focus']) ? $imageBeforePath : null,
            'image_before_path' => $imageBeforePath,
            'image_after_path'  => $imageAfterPath,
            'extra_images'      => count($extraImagePaths) ? $extraImagePaths : null,
            'template'          => $this->template,
            'template_options'  => $templateOptions,
            'platforms'         => $selectedPlatforms,
            'status'            => Post::STATUS_PENDING,
            'scheduled_at'      => ($this->scheduleType === 'scheduled' && $this->scheduledAt)
                                    ? \Carbon\Carbon::parse($this->scheduledAt)
                                    : null,
        ]);

        $user->deductCredits($cost);

        $this->generatedPostId = $post->id;

        try {
            ProcessPostGeneration::dispatch($post);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('PostGenerator: dispatch failed', [
                'post_id' => $post->id,
                'error'   => $e->getMessage(),
            ]);
            $this->errorMessage = $e->getMessage();
            $this->status = Post::STATUS_FAILED;
            return;
        }

        session()->flash('success', "Génération en cours ! La page se mettra à jour automatiquement.");
        $this->redirect(route('posts.index'), navigate: true);
    }

    /**
     * Periodically called via wire:poll while status is 'processing'
     */
    public function checkProcessingStatus(): void
    {
        if ($this->status !== Post::STATUS_PROCESSING || !$this->generatedPostId) {
            return;
        }

        $post = Post::find($this->generatedPostId);
        if (!$post) return;

        if ($post->status === Post::STATUS_REVIEW_PENDING) {
            $this->status = Post::STATUS_REVIEW_PENDING;
            $this->previewImageUrl = $post->image_path ? asset(\Illuminate\Support\Facades\Storage::url($post->image_path)) : null;
            $this->previewSlides   = $post->slides ? array_map(fn($s) => asset(\Illuminate\Support\Facades\Storage::url($s)), $post->slides) : [];
            $this->previewContent  = $post->refined_caption ?: $post->transcription; // Fallback to transcription if caption not yet ready
            
            // If it's a JSON refined content, extract the caption
            $contentData = json_decode($post->refined_content, true);
            if (is_array($contentData)) {
                $this->previewContent = $contentData['caption'] ?? $this->previewContent;
            }
        } elseif ($post->status === Post::STATUS_COMPLETED) {
            $this->status = Post::STATUS_COMPLETED;
        } elseif ($post->status === Post::STATUS_FAILED) {
            $this->status = Post::STATUS_FAILED;
            $this->errorMessage = "La génération a échoué. Veuillez réessayer.";
        }
    }

    public function regeneratePost(bool $simplified = false): void
    {
        $post = Post::where('id', $this->generatedPostId)
                    ->where('user_id', Auth::id())
                    ->first();

        if (!$post || !$post->transcription) return;

        $this->status = Post::STATUS_PROCESSING;
        
        $templateOptions = $post->template_options ?? [];
        if ($simplified) {
            $templateOptions['simplified'] = true;
        }

        $post->update([
            'status' => Post::STATUS_PROCESSING,
            'template_options' => $templateOptions,
        ]);

        try {
            ProcessPostGeneration::dispatch($post);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('regeneratePost dispatch failed', ['error' => $e->getMessage()]);
            $this->errorMessage = $e->getMessage();
            $this->status = Post::STATUS_FAILED;
            $post->update(['status' => Post::STATUS_FAILED]);
        }
    }

    public function validatePost()
    {
        $post = Post::find($this->generatedPostId);

        if (!$post || $post->user_id !== Auth::id()) return;

        // Mark as completed (publishing is already handled by ProcessPostGeneration)
        $post->update(['status' => Post::STATUS_COMPLETED]);

        $platforms   = $post->platforms ?? ['instagram'];
        $scheduledAt = $post->scheduled_at;

        $platformNames = [];
        if (in_array('instagram', $platforms)) $platformNames[] = 'Instagram';
        if (in_array('facebook', $platforms))  $platformNames[] = 'Facebook';
        $platformLabel = implode(' et ', $platformNames);

        if (empty($platformNames)) {
            $message = "Votre post a été généré et sauvegardé dans vos créations !";
        } else {
            $message = ($scheduledAt && $scheduledAt->isFuture())
                ? 'Post planifié pour le ' . $scheduledAt->translatedFormat('d/m/Y à H\hi') . " sur {$platformLabel} !"
                : "Votre post a été validé et publié sur {$platformLabel} !";
        }

        session()->flash('success', $message);
        $this->redirect(route('posts.index'));
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
        Auth::user()->checkAndResetQuota();

        $customTemplates = CustomTemplate::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        $customPhotoMode = 'none';
        if ($this->template === 'custom' && $this->customTemplateId) {
            $ct = $customTemplates->firstWhere('id', $this->customTemplateId);
            $customPhotoMode = $ct?->photo_mode ?? 'none';
        }

        $company          = Auth::user()->company;
        $brandColor       = $company?->brand_color ?? '#D97706';
        $igUsername       = $company?->instagram_username
            ? $company->instagram_username
            : strtolower(preg_replace('/\s+/', '_', $company?->name ?? 'votre_entreprise'));
        $igCity           = $company?->city ?? 'France';
        $hasLogo          = $company && $company->logo_path;
        $igProfilePicture = $company?->instagram_profile_picture_url;
        $igConnected      = $company?->instagram_connected ?? false;
        $fbConnected      = $company?->facebook_connected ?? false;

        // Rafraîchit la photo de profil depuis l'API Instagram :
        // - systématiquement quand le preview est affiché (les URLs CDN IG expirent)
        // - une fois au premier passage si elle n'est pas encore enregistrée
        if ($company?->instagram_connected && ($this->status === 'review_pending' || !$igProfilePicture)) {
            try {
                $stats = app(\App\Services\InstagramService::class)
                    ->getAccountStats($company->instagram_access_token, $company->instagram_business_account_id);
                if (!empty($stats['profile_picture_url'])) {
                    $company->update(['instagram_profile_picture_url' => $stats['profile_picture_url']]);
                    $igProfilePicture = $stats['profile_picture_url'];
                }
            } catch (\Throwable) {
                // Non-critique : on affiche le logo entreprise en fallback
            }
        }

        return view('livewire.pages.post-generator', compact(
            'customTemplates', 'customPhotoMode',
            'company', 'brandColor', 'igUsername', 'igCity', 'hasLogo', 'igProfilePicture',
            'igConnected', 'fbConnected'
        ));
    }

    public function isNativeActive($slug): bool
    {
        static $nativeSettings = null;
        if ($nativeSettings === null) {
            $nativeSettings = \App\Models\NativeTemplateSetting::all()->pluck('is_active', 'slug');
        }
        return $nativeSettings[$slug] ?? true;
    }
}
