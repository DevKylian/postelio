<?php

use App\Models\CustomTemplate;
use App\Services\TemplateGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use function Livewire\Volt\{state, uses};
uses(\Livewire\WithFileUploads::class);

state([
    'showModal'         => false,
    'isSaving'          => false,
    'generatedTemplate' => null,
    'errorMessage'      => null,
    'editingTemplateId' => null,
    'adjustmentRequest' => '',
    'isRefining'        => false,
    'hasImageText'      => true,
    'activeTab'         => 'standard', // 'standard', 'glass', 'blueprint', 'kraft' or 'beton'
]);

$openModal = function () {
    $this->showModal         = true;
    $this->userRequest       = '';
    $this->photoMode         = 'none';
    $this->generatedTemplate = null;
    $this->errorMessage      = null;
    $this->audioRequest      = null;
    $this->isTranscribing    = false;
    $this->editingTemplateId = null;
    $this->adjustmentRequest = '';
    $this->isRefining        = false;
};

$transcribeAudioRequest = function (
    \App\Services\PostGeneration\TranscriptionService $transcriber
) {
    if (!$this->audioRequest) return;

    $this->isTranscribing = true;
    $this->errorMessage   = null;

    try {
        $path = $this->audioRequest->store('temp_memos', 'public');
        $transcript = $transcriber->transcribe($path);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        $this->userRequest   = mb_substr(trim($transcript), 0, 300);
        $this->audioRequest  = null;
        // Basculer l'onglet vers "Décrire" avec le texte retranscrit
        $this->dispatch('modal-switch-to-text');
    } catch (\Throwable $e) {
        $this->errorMessage = 'La transcription a échoué. Réessayez ou utilisez le mode texte.';
        \Illuminate\Support\Facades\Log::error('TemplateModal transcription failed: ' . $e->getMessage());
    } finally {
        $this->isTranscribing = false;
    }
};

$closeModal = function () {
    $this->showModal         = false;
    $this->generatedTemplate = null;
    $this->errorMessage      = null;
    $this->isSaving          = false;
    $this->editingTemplateId = null;
    $this->adjustmentRequest = '';
    $this->isRefining        = false;
};


$startEditingTemplate = function (int $id) {
    $template = CustomTemplate::find($id);
    if (!$template || $template->user_id !== Auth::id()) return;

    $this->editingTemplateId = $template->id;
    $this->generatedTemplate = [
        'name'         => $template->name,
        'description'  => $template->description,
        'ai_prompt'    => $template->ai_prompt,
        'visual_style' => $template->visual_style,
        'layout_type'  => $template->layout_type,
        'photo_mode'   => $template->photo_mode,
        'has_image_text' => (bool) $template->has_image_text,
        'dynamic_content' => $template->dynamic_content,
    ];
    $this->photoMode   = $template->photo_mode;
    $this->hasImageText = (bool) $template->has_image_text;
    $this->showModal   = true;
    $this->adjustmentRequest = '';
    $this->errorMessage = null;
};

$refineTemplate = function () {
    if (!$this->editingTemplateId || !$this->adjustmentRequest) return;

    $this->isRefining = true;
    $this->errorMessage = null;

    try {
        $service = app(TemplateGeneratorService::class);
        $this->generatedTemplate = $service->refine($this->generatedTemplate, $this->adjustmentRequest);
        $this->hasImageText = (bool) ($this->generatedTemplate['has_image_text'] ?? true);
        // Sync photoMode in case the AI changed it
        $this->photoMode = $this->generatedTemplate['photo_mode'] ?? 'none';
    } catch (\Throwable $e) {
        $this->errorMessage = 'Une erreur est survenue lors de l\'ajustement. Réessayez.';
        \Illuminate\Support\Facades\Log::error('TemplateRefiner failed: ' . $e->getMessage());
    } finally {
        $this->isRefining = false;
    }
};

$saveRefinedTemplate = function () {
    if (!$this->editingTemplateId || !$this->generatedTemplate) return;

    $this->isSaving = true;

    try {
        $template = CustomTemplate::find($this->editingTemplateId);
        if ($template && $template->user_id === Auth::id()) {
            $template->update([
                'name'         => $this->generatedTemplate['name'],
                'description'  => $this->generatedTemplate['description'],
                'ai_prompt'    => $this->generatedTemplate['ai_prompt'],
                'visual_style' => $this->generatedTemplate['visual_style'],
                'layout_type'  => $this->generatedTemplate['layout_type'],
                'photo_mode'   => $this->photoMode,
                'has_image_text' => (bool) ($this->generatedTemplate['has_image_text'] ?? true),
                'dynamic_content' => $this->generatedTemplate['dynamic_content'] ?? null,
            ]);
        }

        $this->closeModal();
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('CustomTemplate update failed: ' . $e->getMessage());
        $this->errorMessage = 'Impossible de mettre à jour le modèle.';
    } finally {
        $this->isSaving = false;
    }
};

$deleteTemplate = function (int $id) {
    $template = CustomTemplate::find($id);
    if ($template && $template->user_id === Auth::id()) {
        $template->delete();
    }
};

?>

<div>

    @php
        $company         = auth()->user()->company;
        $brandColor      = $company?->brand_color ?? '#F59E0B';
        $companyName     = $company?->name ?? 'Votre Société';
        $companyCity     = $company?->city ?? '';
        $companyLabel    = $companyName;
        $hasLogo         = $company?->logo_path && Storage::disk('public')->exists($company->logo_path);
        
        // Custom templates: only active ones for regular users
        // (Wait, if it's THEIR template, should it be hidden if admin deactivated it? Yes, per user request)
        $customTemplates = \App\Models\CustomTemplate::where('user_id', auth()->id())
                            ->where('is_active', true)
                            ->orderByDesc('created_at')
                            ->get()
                            ->toArray();

        // Native templates: fetch all settings
        $nativeSettings = \App\Models\NativeTemplateSetting::all()->pluck('is_active', 'slug');
        $isNativeActive = fn($slug) => $nativeSettings[$slug] ?? true;

        $allNativeTemplates = [
            'project', 'review', 'faq', 'lifestyle', 'quality_focus', 
            'chantier_detail', 'step_by_step', 'conseil_saisonnier', 
            'erreur_a_eviter', 'guide_pedagogique'
        ];
        $activeNativeCount = collect($allNativeTemplates)->filter(fn($s) => $isNativeActive($s))->count();
    @endphp

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- Style Tabs --}}
            <div class="flex items-center justify-between mb-8">
                <div class="flex p-1.5 bg-gray-100 rounded-2xl w-full sm:w-auto shadow-inner ring-1 ring-gray-100">
                    <button wire:click="$set('activeTab', 'standard')"
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300
                                {{ $activeTab === 'standard' ? 'bg-white text-midnight shadow-lg shadow-gray-200 ring-1 ring-gray-100' : 'text-gray-400 hover:text-gray-600' }}">
                        Style Classique
                    </button>
                    <button wire:click="$set('activeTab', 'glass')"
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2
                                {{ $activeTab === 'glass' ? 'bg-midnight text-white shadow-lg shadow-midnight/20' : 'text-gray-400 hover:text-gray-600' }}">
                        <span>Style Glassmorphism</span>
                    </button>
                    <button wire:click="$set('activeTab', 'blueprint')"
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2
                                {{ $activeTab === 'blueprint' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-gray-600' }}">
                        <span>Style Blueprint</span>
                    </button>
                    <button wire:click="$set('activeTab', 'kraft')"
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2
                                {{ $activeTab === 'kraft' ? 'bg-[#BC9A7E] text-slate-900 shadow-lg shadow-[#BC9A7E]/20' : 'text-gray-400 hover:text-gray-600' }}">
                        <span>Style Kraft</span>
                    </button>
                    <button wire:click="$set('activeTab', 'beton')"
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2
                                {{ $activeTab === 'beton' ? 'bg-slate-600 text-white shadow-lg shadow-slate-600/20' : 'text-gray-400 hover:text-gray-600' }}">
                        <span>Style Béton</span>
                    </button>
                </div>

            </div>

            {{-- Topbar avec bouton créer --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    @if (!$company || !$company->name)
                    <div class="flex items-center gap-3 bg-amber-50 border border-amber-100 text-amber-800 rounded-xl px-4 py-3.5 text-sm">
                        <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Les aperçus utilisent des valeurs par défaut.
                            <a href="{{ route('post-settings') }}" wire:navigate class="font-semibold underline hover:text-amber-900 ml-1">Configurez votre branding</a>
                            pour voir vos vraies couleurs.
                        </span>
                    </div>
                    @else
                    <div></div>
                    @endif
                </div>
            </div>

            @if($activeTab === 'standard')

            {{-- ── Gabarits natifs ── --}}
            <div class="flex items-center gap-3 mb-6">
                <h2 class="font-bold text-midnight text-base">Gabarits inclus</h2>
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400 font-medium">{{ $activeNativeCount }} style{{ $activeNativeCount > 1 ? 's' : '' }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- ── Avant/Après ── --}}
                @if($isNativeActive('project'))
                <div class="space-y-4 group">
                    <x-post-templates.project :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Avant / Après</h4><p class="text-gray-500 text-sm">Parfait pour valoriser vos rénovations et finitions.</p></div>
                </div>
                @endif

                {{-- ── Avis Client ── --}}
                @if($isNativeActive('review'))
                <div class="space-y-4 group">
                    <x-post-templates.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$hasLogo ? Storage::url($company->logo_path) : null" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Avis Client</h4><p class="text-gray-500 text-sm">Transformez vos témoignages en preuves sociales fortes.</p></div>
                </div>
                @endif

                {{-- ── FAQ Artisan ── --}}
                @if($isNativeActive('faq'))
                <div class="space-y-4 group">
                    <x-post-templates.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$hasLogo ? Storage::url($company->logo_path) : null" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">FAQ Artisan</h4><p class="text-gray-500 text-sm">Éduquez vos clients et affirmez votre professionnalisme.</p></div>
                </div>
                @endif

                {{-- ── Lifestyle ── --}}
                @if($isNativeActive('lifestyle'))
                <div class="space-y-4 group">
                    <x-post-templates.lifestyle :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$hasLogo ? Storage::url($company->logo_path) : null" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Lifestyle</h4><p class="text-gray-500 text-sm">Une immersion brute dans votre métier, sans artifices.</p></div>
                </div>
                @endif

                {{-- ── Focus Qualité ── --}}
                @if($isNativeActive('quality_focus'))
                <div class="space-y-4 group">
                    <x-post-templates.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$hasLogo ? Storage::url($company->logo_path) : null" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Focus Qualité</h4><p class="text-gray-500 text-sm">Valorisez la qualité de vos matériaux et équipements.</p></div>
                </div>
                @endif

                {{-- ── Chantier en Détail ── --}}
                @if($isNativeActive('chantier_detail'))
                <div class="space-y-4 group">
                    <x-post-templates.chantier-detail :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$hasLogo ? Storage::url($company->logo_path) : null" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Chantier en Détail</h4><p class="text-gray-500 text-sm">Une immersion complète avec plusieurs photos d'un même projet.</p></div>
                </div>
                @endif

                {{-- ── Step-by-Step ── --}}
                @if($isNativeActive('step_by_step'))
                <div class="space-y-4 group">
                    <x-post-templates.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Step-by-Step</h4><p class="text-gray-500 text-sm">Expliquez votre expertise étape par étape, didactique et rassurant.</p></div>
                </div>
                @endif

                {{-- ── Conseil Saisonnier ── --}}
                @if($isNativeActive('conseil_saisonnier'))
                <div class="space-y-4 group">
                    <x-post-templates.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$hasLogo ? Storage::url($company->logo_path) : null" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Conseil Saisonnier</h4><p class="text-gray-500 text-sm">Anticipez les besoins de vos clients selon la saison.</p></div>
                </div>
                @endif

                {{-- ── Erreur à Éviter ── --}}
                @if($isNativeActive('erreur_a_eviter'))
                <div class="space-y-4 group">
                    <x-post-templates.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :logoUrl="$hasLogo ? Storage::url($company->logo_path) : null" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Erreur à Éviter</h4><p class="text-gray-500 text-sm">Un format alerte très engageant pour éduquer vos clients.</p></div>
                </div>
                @endif

                {{-- ── Guide Pédagogique ── --}}
                @if($isNativeActive('guide_pedagogique'))
                <div class="space-y-4 group">
                    <x-post-templates.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                    <div><h4 class="font-bold text-midnight text-lg">Guide Pédagogique</h4><p class="text-gray-500 text-sm">Apportez de la valeur et positionnez-vous en expert conseil.</p></div>
                </div>
                @endif

            </div>
            @endif

            @if($activeTab === 'glass')
            <div class="mb-10 animate-fade-in">
            <div class="flex items-center gap-3 mb-6">
                <h2 class="font-bold text-midnight text-base">Gabarits inclus</h2>
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400 font-medium">{{ $activeNativeCount }} style{{ $activeNativeCount > 1 ? 's' : '' }}</span>
            </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {{-- Glass Avant/Après --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.project :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Avant / Après</h4>
                            <p class="text-gray-500 text-sm italic">Un style futuriste et épuré pour vos réalisations.</p>
                        </div>
                    </div>

                    {{-- Glass Review --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Avis Client</h4>
                            <p class="text-gray-500 text-sm italic">Mettez en valeur vos retours clients avec élégance.</p>
                        </div>
                    </div>

                    {{-- Glass FAQ --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass FAQ</h4>
                            <p class="text-gray-500 text-sm italic">Répondez aux questions avec un design premium.</p>
                        </div>
                    </div>

                    {{-- Glass Lifestyle --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.lifestyle :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :imageUrl="'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=400'" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Lifestyle</h4>
                            <p class="text-gray-500 text-sm italic">Une immersion brute et moderne dans votre métier.</p>
                        </div>
                    </div>

                    {{-- Glass Quality Focus --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Focus Qualité</h4>
                            <p class="text-gray-500 text-sm italic">Valorisez vos matériaux avec précision.</p>
                        </div>
                    </div>

                    {{-- Glass Chantier Détail --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.chantier-detail :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Chantier Détail</h4>
                            <p class="text-gray-500 text-sm italic">Explorez chaque recoin de vos projets.</p>
                        </div>
                    </div>

                    {{-- Glass Step-by-Step --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Step-by-Step</h4>
                            <p class="text-gray-500 text-sm italic">Partagez votre expertise étape par étape.</p>
                        </div>
                    </div>

                    {{-- Glass Conseil Saisonnier --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Conseil Saisonnier</h4>
                            <p class="text-gray-500 text-sm italic">Le bon conseil, pour le bon moment.</p>
                        </div>
                    </div>

                    {{-- Glass Erreur à Éviter --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Erreur à Éviter</h4>
                            <p class="text-gray-500 text-sm italic">Éduquez avec un design d'alerte premium.</p>
                        </div>
                    </div>

                    {{-- Glass Guide Pédagogique --}}
                    <div class="space-y-4 group">
                        <x-post-templates.glass.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Glass Guide Pédagogique</h4>
                            <p class="text-gray-500 text-sm italic">Devenez la référence conseil de votre secteur.</p>
                        </div>
                    </div>
                </div>

            </div>
            @endif
            
            @if($activeTab === 'blueprint')
            <div class="mb-10 animate-fade-in space-y-12">
            <div class="flex items-center gap-3 mb-6">
                <h2 class="font-bold text-midnight text-base">Gabarits inclus</h2>
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400 font-medium">{{ $activeNativeCount }} style{{ $activeNativeCount > 1 ? 's' : '' }}</span>
            </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {{-- Blueprint Project --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.project :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Avant / Après</h4>
                            <p class="text-gray-500 text-sm italic">Le chantier vu comme un projet d'architecte.</p>
                        </div>
                    </div>

                    {{-- Blueprint Review --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Avis</h4>
                            <p class="text-gray-500 text-sm italic">La validation technique de vos clients.</p>
                        </div>
                    </div>

                    {{-- Blueprint FAQ --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint FAQ</h4>
                            <p class="text-gray-500 text-sm italic">Des réponses claires et documentées.</p>
                        </div>
                    </div>

                    {{-- Blueprint Lifestyle --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.lifestyle :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :imageUrl="'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=400'" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Lifestyle</h4>
                            <p class="text-gray-500 text-sm italic">L'immersion brute dans vos réalisations.</p>
                        </div>
                    </div>

                    {{-- Blueprint Quality --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Focus Qualité</h4>
                            <p class="text-gray-500 text-sm italic">L'analyse détaillée de vos matériaux.</p>
                        </div>
                    </div>

                    {{-- Blueprint Chantier --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.chantier-detail :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Zoom Chantier</h4>
                            <p class="text-gray-500 text-sm italic">Le dossier technique de vos chantiers.</p>
                        </div>
                    </div>

                    {{-- Blueprint Steps --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Étapes</h4>
                            <p class="text-gray-500 text-sm italic">Votre méthodologie, étape par étape.</p>
                        </div>
                    </div>

                    {{-- Blueprint Seasonal --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Saison</h4>
                            <p class="text-gray-500 text-sm italic">Le conseil expert, archivé et précis.</p>
                        </div>
                    </div>

                    {{-- Blueprint Alert --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Alerte</h4>
                            <p class="text-gray-500 text-sm italic">Éduquez avec la rigueur d'un expert.</p>
                        </div>
                    </div>

                    {{-- Blueprint Guide --}}
                    <div class="space-y-4 group">
                        <x-post-templates.blueprint.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Blueprint Guide</h4>
                            <p class="text-gray-500 text-sm italic">Votre base de connaissance technique.</p>
                        </div>
                    </div>
                </div>

            </div>
            @endif

            @if($activeTab === 'kraft')
            <div class="mb-10 animate-fade-in space-y-12">
            <div class="flex items-center gap-3 mb-6">
                <h2 class="font-bold text-midnight text-base">Gabarits inclus</h2>
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400 font-medium">{{ $activeNativeCount }} style{{ $activeNativeCount > 1 ? 's' : '' }}</span>
            </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {{-- Kraft Project --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.project :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Avant / Après</h4>
                            <p class="text-gray-500 text-sm italic">L'authenticité du chantier, étape par étape.</p>
                        </div>
                    </div>

                    {{-- Kraft Review --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Avis Client</h4>
                            <p class="text-gray-500 text-sm italic">La validation brute de vos clients satisfaits.</p>
                        </div>
                    </div>

                    {{-- Kraft FAQ --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft FAQ</h4>
                            <p class="text-gray-500 text-sm italic">Des réponses franches à vos clients.</p>
                        </div>
                    </div>

                    {{-- Kraft Lifestyle --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.lifestyle :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :imageUrl="'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=400'" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Lifestyle</h4>
                            <p class="text-gray-500 text-sm italic">Votre métier au naturel, sans filtre.</p>
                        </div>
                    </div>

                    {{-- Kraft Quality --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Focus Qualité</h4>
                            <p class="text-gray-500 text-sm italic">La précision technique mise en avant.</p>
                        </div>
                    </div>

                    {{-- Kraft Chantier --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.chantier-detail :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Zoom Chantier</h4>
                            <p class="text-gray-500 text-sm italic">Le rapport de chantier visuel et efficace.</p>
                        </div>
                    </div>

                    {{-- Kraft Steps --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Étapes</h4>
                            <p class="text-gray-500 text-sm italic">Une méthodologie claire et industrielle.</p>
                        </div>
                    </div>

                    {{-- Kraft Seasonal --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Saison</h4>
                            <p class="text-gray-500 text-sm italic">Le mémo technique pour chaque saison.</p>
                        </div>
                    </div>

                    {{-- Kraft Alert --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Alerte</h4>
                            <p class="text-gray-500 text-sm italic">Évitez les erreurs avec un design impactant.</p>
                        </div>
                    </div>

                    {{-- Kraft Guide --}}
                    <div class="space-y-4 group">
                        <x-post-templates.kraft.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Kraft Guide</h4>
                            <p class="text-gray-500 text-sm italic">Le guide pratique de votre savoir-faire.</p>
                        </div>
                    </div>
                </div>

            </div>
            @endif

            @if($activeTab === 'beton')
            <div class="mb-10 animate-fade-in space-y-12">
                <div class="flex items-center gap-3 mb-6">
                    <h2 class="font-bold text-midnight text-base">Gabarits inclus</h2>
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <span class="text-xs text-gray-400 font-medium">{{ $activeNativeCount }} style{{ $activeNativeCount > 1 ? 's' : '' }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {{-- Béton Project --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.project :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Avant / Après</h4>
                            <p class="text-gray-500 text-sm italic">Modernité et solidité visuelle.</p>
                        </div>
                    </div>

                    {{-- Béton Review --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.review :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Avis Client</h4>
                            <p class="text-gray-500 text-sm italic">Le témoignage de confiance ancré dans le réel.</p>
                        </div>
                    </div>

                    {{-- Béton FAQ --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.faq :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton FAQ</h4>
                            <p class="text-gray-500 text-sm italic">Réponses précises et professionnelles.</p>
                        </div>
                    </div>

                    {{-- Béton Lifestyle --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.lifestyle :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Lifestyle</h4>
                            <p class="text-gray-500 text-sm italic">L'immersion moderne sur vos chantiers.</p>
                        </div>
                    </div>

                    {{-- Béton Quality --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.quality-focus :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Focus Qualité</h4>
                            <p class="text-gray-500 text-sm italic">Le détail qui fait la différence.</p>
                        </div>
                    </div>

                    {{-- Béton Chantier --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.chantier-detail :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Zoom Chantier</h4>
                            <p class="text-gray-500 text-sm italic">Vue technique et multi-angle.</p>
                        </div>
                    </div>

                    {{-- Béton Steps --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.step-by-step :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Étapes Pro</h4>
                            <p class="text-gray-500 text-sm italic">Votre méthodologie en 3 étapes claires.</p>
                        </div>
                    </div>

                    {{-- Béton Seasonal --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.conseil-saisonnier :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Dossier</h4>
                            <p class="text-gray-500 text-sm italic">Le mémo technique indispensable.</p>
                        </div>
                    </div>

                    {{-- Béton Alert --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.erreur-a-eviter :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Alerte Erreur</h4>
                            <p class="text-gray-500 text-sm italic">Prévention et conseils d'expert.</p>
                        </div>
                    </div>

                    {{-- Béton Guide --}}
                    <div class="space-y-4 group">
                        <x-post-templates.beton.guide-pedagogique :brandColor="$brandColor" :companyLabel="$companyLabel" :city="$companyCity" :preview="true" />
                        <div>
                            <h4 class="font-bold text-midnight text-lg">Béton Guide Pratique</h4>
                            <p class="text-gray-500 text-sm italic">L'accompagnement client par l'artisan.</p>
                        </div>
                    </div>
                </div>

            </div>
            @endif

            {{-- CTA branding --}}
            <div class="mt-12 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white shadow ring-1 ring-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl shrink-0 shadow-md" style="background-color: {{ $brandColor }}"></div>
                    <div>
                        <p class="font-semibold text-midnight">Votre branding est appliqué à tous vos posts</p>
                        <p class="text-sm text-gray-500">Couleur, nom de société et logo apparaissent sur chaque visuel généré.</p>
                    </div>
                </div>
                <a href="{{ route('post-settings') }}" wire:navigate
                   class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                   style="background-color: {{ $brandColor }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Modifier le branding
                </a>
            </div>

        </div>
    </div>

    {{-- ── Modal Création de Modèle ── --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-midnight/60 backdrop-blur-sm" wire:click="closeModal"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
             x-data x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="flex items-start justify-between p-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-copper">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-midnight text-lg">
                            Ajuster le modèle
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Décrivez les modifications à apporter
                        </p>
                    </div>
                </div>
                <button wire:click="closeModal" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">

                {{-- ÉDITION OU RÉSULTAT --}}
                <div class="space-y-6">
                    
                    @if($editingTemplateId)
                    {{-- Barre d'ajustement en haut quand on édite --}}
                    <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 shadow-sm" x-data="{ focus: false }">
                        <label class="block text-xs font-black text-amber-800 uppercase tracking-widest mb-3">Que souhaitez-vous modifier ?</label>
                        <div class="flex gap-2">
                            <textarea wire:model="adjustmentRequest"
                                   placeholder="Ex: Change le style en clair, mets une mise en page citation, ou rends le titre plus accrocheur..."
                                   rows="2"
                                   @focus="focus = true" @blur="focus = false"
                                   class="flex-1 px-4 py-3 rounded-xl border border-amber-200 text-sm focus:ring-2 focus:ring-copper focus:border-transparent outline-none resize-none transition-shadow"
                                   :class="focus ? 'shadow-md' : ''"></textarea>
                            <button wire:click="refineTemplate"
                                    wire:loading.attr="disabled"
                                    class="px-5 bg-copper text-white text-xs font-bold rounded-xl hover:bg-amber-500 transition-all flex items-center justify-center shrink-0 shadow-sm">
                                <span wire:loading.remove wire:target="refineTemplate">Ajuster</span>
                                <svg wire:loading wire:target="refineTemplate" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </button>
                        </div>
                        <p class="text-[10px] text-amber-600/70 mt-2 italic">L'IA détectera automatiquement si vous voulez changer le style, la mise en page ou le mode photo.</p>
                    </div>
                    @endif

                    @if($errorMessage)
                    <div class="flex items-center gap-2 bg-red-50 border border-red-100 text-red-700 rounded-xl px-4 py-3 text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ $errorMessage }}
                    </div>
                    @endif

                    {{-- Aperçu réel --}}
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Aperçu du modèle</p>
                        <div class="relative group max-w-[420px] mx-auto shadow-2xl rounded-2xl overflow-hidden ring-4 ring-gray-50 bg-white">
                            <x-post-templates.custom 
                                :template="$generatedTemplate" 
                                :brandColor="$brandColor" 
                                :companyLabel="$companyLabel" 
                                :logoUrl="$hasLogo ? Storage::disk('public')->url($company->logo_path) : null" 
                                :preview="true"
                                :hasImageText="$hasImageText"
                                imageBeforeUrl="https://images.unsplash.com/photo-1581094794329-c8112a89af12?q=80&w=400"
                                imageAfterUrl="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=400"
                                imageUrl="https://images.unsplash.com/photo-1621905235292-057491552730?q=80&w=400"
                            />
                            
                            {{-- Badge info --}}
                            @php
                                $pm = $generatedTemplate['photo_mode'] ?? 'none';
                            @endphp
                            <div class="absolute bottom-16 right-4 z-10">
                                <div class="bg-black/40 backdrop-blur-md px-2 py-1 rounded-lg text-[8px] font-bold text-white/90 uppercase tracking-tighter">
                                    {{ $pm === 'none' ? 'Texte seul' : ($pm === 'carousel' ? 'Carrousel' : ($pm === 'multi' ? 'Multi-ph' : '1 Photo')) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button wire:click="saveRefinedTemplate"
                                wire:loading.attr="disabled"
                                class="flex-1 py-3 rounded-xl text-sm font-bold text-white bg-copper transition-all hover:bg-amber-500 shadow-lg shadow-copper/25 flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="saveRefinedTemplate">Valider les modifications</span>
                            <svg wire:loading wire:target="saveRefinedTemplate" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span wire:loading wire:target="saveRefinedTemplate">Mise à jour...</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif

    @push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@800;900&family=Space+Mono:wght@400;700&display=swap');
        .font-montserrat { font-family: 'Montserrat', sans-serif; }
        .font-mono-technical { font-family: 'Space Mono', monospace; }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @endpush

</div>
