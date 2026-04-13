<?php

use App\Models\CustomTemplate;
use App\Services\TemplateGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use function Livewire\Volt\{state, uses, computed, mount, updated};
uses(\Livewire\WithFileUploads::class);

state([
    'showModal'         => false,
    'isSaving'          => false,
    'generatedTemplate' => null,
    'errorMessage'      => null,
    'editingTemplateId' => null,
    'userRequest'       => '',
    'photoMode'         => 'none',
    'adjustmentRequest' => '',
    'isRefining'        => false,
    'hasImageText'      => true,
    'activeTab'         => 'standard', 
    'brandColor'      => null,
    'accentColor'     => null,
    'companyLabel'    => fn() => auth()->user()->company?->name ?? 'Votre Société',
    'companyCity'     => fn() => auth()->user()->company?->city ?? '',
    'logoUrl' => function() {
        $company = auth()->user()->company;
        $hasLogo = $company?->logo_path && Storage::disk('public')->exists($company->logo_path);
        return $hasLogo ? Storage::disk('public')->url($company->logo_path) : null;
    }
]);

mount(function() {
    $this->brandColor = auth()->user()->company?->brand_color ?? '#F59E0B';
    $this->accentColor = auth()->user()->company?->secondary_color ?? '#0F172A';
});

updated(['brandColor' => function ($value) {
    $company = auth()->user()->company;
    if ($company) {
        $company->update(['brand_color' => $value]);
    }
}]);

updated(['accentColor' => function ($value) {
    $company = auth()->user()->company;
    if ($company) {
        $company->update(['secondary_color' => $value]);
    }
}]);

$brandTextColor = computed(function() {
    $hex = str_replace('#', '', $this->brandColor ?: '#F59E0B');
    if (strlen($hex) == 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 186) ? '#1E293B' : '#FFFFFF';
});

$accentTextColor = computed(function() {
    $hex = str_replace('#', '', $this->accentColor ?: '#0F172A');
    if (strlen($hex) == 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 150) ? '#1E293B' : '#FFFFFF';
});

$company = computed(fn() => auth()->user()->company);

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

$visualStylesEnabled = computed(fn() => (bool) \App\Models\Setting::get('enable_visual_styles', true));

$switchTab = function ($tab) {
    if (!$this->visualStylesEnabled && $tab !== 'standard') {
        return;
    }
    $this->activeTab = $tab;
};

?>

<div>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- Style Tabs --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                    <div class="flex p-1.5 bg-gray-100 rounded-2xl w-full sm:w-auto shadow-inner ring-1 ring-gray-100 relative overflow-hidden">
                        {{-- Progress bar overlay --}}
                        <div wire:loading class="absolute top-0 left-0 right-0 h-0.5 bg-copper/30 animate-pulse pointer-events-none"></div>

                        <button wire:click="switchTab('standard')"
                                wire:key="tab-standard"
                                wire:loading.attr="disabled"
                                class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 relative
                                    {{ $activeTab === 'standard' ? 'bg-white text-midnight shadow-lg shadow-gray-200 ring-1 ring-gray-100' : 'text-gray-400 hover:text-gray-600' }}">
                            Style Classique
                        </button>

                        @if($this->visualStylesEnabled)
                            <button wire:click="switchTab('glass')"
                                    wire:key="tab-glass"
                                    wire:loading.attr="disabled"
                                    class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2 relative
                                        {{ $activeTab === 'glass' ? 'bg-midnight text-white shadow-lg shadow-midnight/20' : 'text-gray-400 hover:text-gray-600' }}">
                                Style Glassmorphism
                            </button>

                            <button wire:click="switchTab('blueprint')"
                                    wire:key="tab-blueprint"
                                    wire:loading.attr="disabled"
                                    class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2 relative
                                        {{ $activeTab === 'blueprint' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:text-gray-600' }}">
                                Style Blueprint
                            </button>

                            <button wire:click="switchTab('kraft')"
                                    wire:key="tab-kraft"
                                    wire:loading.attr="disabled"
                                    class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2 relative
                                        {{ $activeTab === 'kraft' ? 'bg-[#BC9A7E] text-slate-900 shadow-lg shadow-[#BC9A7E]/20' : 'text-gray-400 hover:text-gray-600' }}">
                                Style Kraft
                            </button>

                            <button wire:click="switchTab('beton')"
                                    wire:key="tab-beton"
                                    wire:loading.attr="disabled"
                                    class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2 relative
                                        {{ $activeTab === 'beton' ? 'bg-slate-600 text-white shadow-lg shadow-slate-600/20' : 'text-gray-400 hover:text-gray-600' }}">
                                Style Béton
                            </button>
                        @endif
                    </div>

                    {{-- Dynamic Color Pickers --}}
                    <div class="flex items-center gap-4 bg-white p-2.5 rounded-2xl shadow-sm ring-1 ring-gray-100 animate-fade-in text-left">
                        <div class="flex items-center gap-4 px-3 border-r border-gray-100">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 whitespace-nowrap">Couleur Principale</label>
                            <x-custom-color-picker wire:model.live="brandColor" />
                        </div>
                        <div class="flex items-center gap-4 px-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 whitespace-nowrap">Couleur Bandeaux</label>
                            <x-custom-color-picker wire:model.live="accentColor" />
                        </div>
                    </div>
                </div>
            
            {{-- Info alert: plus de modèles arrivent --}}
            <div class="mb-8 animate-fade-in">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 shadow-sm">
                    <div class="w-1.5 h-1.5 rounded-full bg-copper animate-pulse"></div>
                    <span class="text-xs font-medium">De nouveaux gabarits arrivent très prochainement pour encore plus de choix.</span>
                </div>
            </div>


            {{-- Topbar avec bouton créer --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    @if (!$this->company || !$this->company->name)
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

            {{-- Contenu Dynamique avec Lazy Loading --}}
            <div class="min-h-[400px] relative">
                {{-- Loader discret au dessus de la grille --}}
                <div wire:loading class="absolute top-0 left-0 right-0 h-1 z-20 overflow-hidden rounded-full">
                    <div class="h-full bg-copper/40 animate-[loading_1.5s_infinite_linear] w-[40%] rounded-full shadow-[0_0_10px_rgba(245,158,11,0.5)]"></div>
                </div>

                <div wire:loading.class="opacity-50 blur-[1px] transition-all duration-300" class="w-full">

                {{-- Composant StyleGrid avec Lazy Loading - Séparé par onglet pour une réactivité maximale --}}
                <livewire:components.style-grid 
                    wire:key="grid-{{ $activeTab }}-{{ $brandColor }}-{{ $accentColor }}"
                    :activeTab="$activeTab" 
                    :brandColor="$brandColor"
                    :companyLabel="$companyLabel"
                    :companyCity="$companyCity"
                    :brandTextColor="$this->brandTextColor"
                    :logoUrl="$logoUrl"
                    :accentColor="$accentColor"
                    :accentTextColor="$this->accentTextColor"
                />
            </div>
        </div>

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
                                    class="px-5 bg-copper text-white text-xs font-bold rounded-xl hover:bg-amber-500 transition-all flex items-center justify-center shrink-0 shadow-sm gap-2">
                                <svg wire:loading wire:target="refineTemplate" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span wire:loading.remove wire:target="refineTemplate">Ajuster</span>
                                <span wire:loading wire:target="refineTemplate">Ajustement...</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-amber-600/70 mt-2 italic">Postelio détectera automatiquement si vous voulez changer le style, la mise en page ou le mode photo.</p>
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
                                :brandTextColor="$this->brandTextColor"
                                :companyLabel="$companyLabel" 
                                :logoUrl="$logoUrl" 
                                :preview="true"
                                :hasImageText="$hasImageText"
                                :accentColor="$accentColor"
                                :accentTextColor="$this->accentTextColor"
                                imageBeforeUrl="{{ asset('images/templates/avant_postelio.png') }}"
                                imageAfterUrl="{{ asset('images/templates/apres_postelio.png') }}"
                                imageUrl="{{ asset('images/templates/lifestyle_postelio.png') }}"
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

        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(300%); }
        }
    </style>
    @endpush

</div>
