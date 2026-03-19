<?php

use App\Models\CustomTemplate;
use App\Services\TemplateGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use function Livewire\Volt\{state};

state([
    'showModal'         => false,
    'userRequest'       => '',
    'photoMode'         => 'none',   // none | single | carousel
    'isGenerating'      => false,
    'isSaving'          => false,
    'generatedTemplate' => null,
    'errorMessage'      => null,
]);

$openModal = function () {
    $this->showModal         = true;
    $this->userRequest       = '';
    $this->photoMode         = 'none';
    $this->generatedTemplate = null;
    $this->errorMessage      = null;
};

$closeModal = function () {
    $this->showModal         = false;
    $this->userRequest       = '';
    $this->photoMode         = 'none';
    $this->generatedTemplate = null;
    $this->errorMessage      = null;
    $this->isGenerating      = false;
    $this->isSaving          = false;
};

$generateTemplate = function () {
    $this->validate(['userRequest' => 'required|string|min:10|max:300']);

    $this->isGenerating      = true;
    $this->errorMessage      = null;
    $this->generatedTemplate = null;

    try {
        $company = Auth::user()->company;
        $service = app(TemplateGeneratorService::class);
        $this->generatedTemplate = $service->generate($this->userRequest, $company?->trade ?? '', $this->photoMode);
    } catch (\Throwable $e) {
        $this->errorMessage = 'Une erreur est survenue lors de la génération. Réessayez.';
        \Illuminate\Support\Facades\Log::error('TemplateGenerator failed: ' . $e->getMessage());
    } finally {
        $this->isGenerating = false;
    }
};

$saveTemplate = function () {
    if (!$this->generatedTemplate) return;

    $this->isSaving = true;

    try {
        $company = Auth::user()->company;

        CustomTemplate::create([
            'user_id'      => Auth::id(),
            'company_id'   => $company?->id,
            'name'         => $this->generatedTemplate['name'],
            'description'  => $this->generatedTemplate['description'],
            'ai_prompt'    => $this->generatedTemplate['ai_prompt'],
            'visual_style' => $this->generatedTemplate['visual_style'],
            'photo_mode'   => $this->photoMode,
        ]);

        $this->showModal         = false;
        $this->userRequest       = '';
        $this->generatedTemplate = null;
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('CustomTemplate save failed: ' . $e->getMessage(), [
            'trace'      => $e->getTraceAsString(),
            'photo_mode' => $this->photoMode,
            'generated'  => $this->generatedTemplate,
        ]);
        $this->errorMessage = 'Impossible de sauvegarder le modèle. Réessayez.';
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
        $companyLabel    = $companyName . ($companyCity ? '  ·  ' . strtoupper($companyCity) : '');
        $hasLogo         = $company?->logo_path && Storage::disk('public')->exists($company->logo_path);
        $customTemplates = \App\Models\CustomTemplate::where('user_id', auth()->id())
                            ->orderByDesc('created_at')
                            ->get()
                            ->toArray();
    @endphp

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

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
                <button wire:click="openModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-copper shadow-sm shadow-copper/25 transition-all hover:bg-amber-500 hover:-translate-y-0.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Créer un modèle
                </button>
            </div>

            {{-- ── Modèles personnalisés ── --}}
            @if(count($customTemplates) > 0)
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center bg-copper">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h2 class="font-bold text-midnight text-base">Mes modèles personnalisés</h2>
                    </div>
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <span class="text-xs text-gray-400 font-medium">{{ count($customTemplates) }} modèle{{ count($customTemplates) > 1 ? 's' : '' }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($customTemplates as $ct)
                    <div class="space-y-4 group relative">
                        <div class="aspect-square rounded-2xl shadow-lg overflow-hidden relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2"
                            style="
                                @if($ct['visual_style'] === 'brand_color') background-color: {{ $brandColor }};
                                @elseif($ct['visual_style'] === 'light_branded') background-color: #F8FAFC;
                                @else background-color: #0B1426;
                                @endif
                                border-top: 8px solid {{ $brandColor }};
                            ">
                            @if($ct['visual_style'] !== 'light_branded')
                            <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full pointer-events-none" style="background: rgba(255,255,255,0.04)"></div>
                            <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full pointer-events-none" style="background: rgba(0,0,0,0.15)"></div>
                            @endif

                            {{-- Badge Personnalisé --}}
                            <div class="absolute top-4 left-4 z-10">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm bg-copper text-white">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    Personnalisé
                                </span>
                            </div>

                            {{-- Bouton supprimer --}}
                            <button wire:click="deleteTemplate({{ $ct['id'] }})"
                                    wire:confirm="Supprimer ce modèle personnalisé ?"
                                    class="absolute top-4 right-4 z-10 w-7 h-7 rounded-full bg-black/30 backdrop-blur-sm text-white/70 hover:bg-red-500 hover:text-white transition-all opacity-0 group-hover:opacity-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            {{-- Contenu central --}}
                            <div class="absolute inset-0 flex flex-col justify-center px-8 pt-14 pb-16">
                                <span class="inline-block w-10 h-1 mb-5 rounded-full"
                                      style="background-color: {{ $ct['visual_style'] === 'brand_color' ? 'rgba(255,255,255,0.5)' : $brandColor }}"></span>
                                <p class="font-black uppercase tracking-widest text-sm mb-3 font-montserrat"
                                   style="color: {{ $ct['visual_style'] === 'brand_color' ? 'rgba(255,255,255,0.85)' : $brandColor }}">
                                    {{ strtoupper($ct['name']) }}
                                </p>
                                <p class="font-black uppercase tracking-tight text-base leading-snug font-montserrat"
                                   style="color: {{ $ct['visual_style'] === 'light_branded' ? '#1E293B' : '#FFFFFF' }}">
                                    {{ $ct['description'] ?? 'Modèle généré par IA.' }}
                                </p>
                            </div>

                            {{-- Company badge --}}
                            <div class="absolute bottom-0 inset-x-0 border-t px-8 py-3"
                                 style="border-color: {{ $ct['visual_style'] === 'light_branded' ? 'rgba(0,0,0,0.08)' : 'rgba(255,255,255,0.1)' }}">
                                <div class="flex items-center gap-2">
                                    @if ($hasLogo)
                                        <img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-4 w-4 object-contain rounded">
                                    @endif
                                    <p class="font-bold text-[10px] uppercase tracking-widest font-montserrat"
                                       style="color: {{ $ct['visual_style'] === 'light_branded' ? $brandColor : ($ct['visual_style'] === 'brand_color' ? 'rgba(255,255,255,0.7)' : $brandColor) }}">
                                        {{ $companyLabel }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-midnight text-lg">{{ $ct['name'] }}</h4>
                                <span class="text-[10px] font-bold text-copper bg-amber-50 px-2 py-0.5 rounded-full">IA</span>
                            </div>
                            <p class="text-gray-500 text-sm">{{ $ct['description'] ?? 'Modèle personnalisé généré par IA.' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── Gabarits natifs ── --}}
            <div class="flex items-center gap-3 mb-6">
                <h2 class="font-bold text-midnight text-base">Gabarits inclus</h2>
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-xs text-gray-400 font-medium">9 styles</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- ── Avant/Après ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 overflow-hidden flex flex-col relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2">
                        <div class="flex-1 flex min-h-0">
                            <div class="w-1/2 relative overflow-hidden border-r-2 border-white bg-slate-300 flex items-center justify-center group-hover:bg-slate-200 transition-colors duration-500">
                                <div class="text-center text-slate-500 select-none pointer-events-none">
                                    <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <p class="text-xs font-bold uppercase tracking-widest">Avant</p>
                                </div>
                            </div>
                            <div class="w-1/2 relative overflow-hidden bg-slate-400 flex items-center justify-center group-hover:bg-slate-300 transition-colors duration-500">
                                <div class="text-center text-slate-100 select-none pointer-events-none">
                                    <svg class="w-10 h-10 mx-auto mb-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <p class="text-xs font-bold uppercase tracking-widest">Après</p>
                                </div>
                            </div>
                        </div>
                        <div class="shrink-0 flex flex-col items-center justify-center px-6 py-2 gap-0.5" style="background-color: {{ $brandColor }}">
                            <p class="text-white/80 font-bold uppercase tracking-widest text-[10px] font-montserrat">{{ $companyLabel }}</p>
                            <div class="flex w-full items-center justify-between">
                                <span class="text-white font-black uppercase tracking-widest text-base font-montserrat">Avant</span>
                                <span class="text-white font-black uppercase tracking-widest text-base font-montserrat">Après</span>
                            </div>
                        </div>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">Avant / Après</h4><p class="text-gray-500 text-sm">Parfait pour valoriser vos rénovations et finitions.</p></div>
                </div>

                {{-- ── Avis Client ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square rounded-2xl shadow-lg flex flex-col items-center justify-center p-10 text-center relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2 overflow-hidden" style="background-color: {{ $brandColor }}">
                        <div class="flex space-x-2 mb-6">@for ($i = 0; $i < 5; $i++)<svg class="w-7 h-7 text-yellow-300 fill-current drop-shadow-md" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>@endfor</div>
                        <p class="text-white font-black uppercase tracking-tight text-lg leading-tight font-montserrat drop-shadow-sm flex-1 flex items-center">"Travail impeccable ! L'équipe a été ponctuelle, propre et le résultat dépasse nos attentes."</p>
                        <div class="mt-6 pt-4 border-t border-white/20 w-full"><div class="flex items-center justify-center gap-2">@if ($hasLogo)<img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-6 w-6 object-contain rounded">@endif<p class="text-white/80 font-bold text-xs uppercase tracking-widest font-montserrat">{{ $companyLabel }}</p></div></div>
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-black/10 rounded-full blur-2xl pointer-events-none"></div>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">Avis Client</h4><p class="text-gray-500 text-sm">Transformez vos témoignages en preuves sociales fortes.</p></div>
                </div>

                {{-- ── FAQ Artisan ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square bg-midnight rounded-2xl shadow-lg flex flex-col p-10 relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2 overflow-hidden" style="border-top: 8px solid {{ $brandColor }}">
                        <div class="flex-1 flex flex-col justify-center">
                            <span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span>
                            <h3 class="font-black uppercase tracking-widest text-sm mb-4" style="color: {{ $brandColor }}">FAQ Artisan</h3>
                            <p class="text-white font-black uppercase tracking-tight text-xl leading-snug font-montserrat">Pourquoi choisir une isolation par l'extérieur ?</p>
                        </div>
                        <div class="pt-4 border-t border-white/10"><div class="flex items-center gap-2">@if ($hasLogo)<img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-5 w-5 object-contain rounded">@endif<p class="font-bold text-xs uppercase tracking-widest font-montserrat" style="color: {{ $brandColor }}">{{ $companyLabel }}</p></div></div>
                        <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-white/5 pointer-events-none" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">FAQ Artisan</h4><p class="text-gray-500 text-sm">Éduquez vos clients et affirmez votre professionnalisme.</p></div>
                </div>

                {{-- ── Lifestyle ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square bg-slate-700 rounded-2xl shadow-lg overflow-hidden relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2 flex items-center justify-center">
                        <div class="text-center text-slate-400 select-none pointer-events-none"><svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg><p class="text-xs font-bold uppercase tracking-widest text-slate-500">Votre photo</p></div>
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent pt-10 pb-4 px-5"><div class="w-full" style="border-top: 3px solid {{ $brandColor }}; padding-top: 8px;"><div class="flex items-center gap-2">@if ($hasLogo)<img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-5 w-5 object-contain rounded">@endif<p class="text-white font-bold text-xs uppercase tracking-widest font-montserrat">{{ $companyLabel }}</p></div></div></div>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">Lifestyle</h4><p class="text-gray-500 text-sm">Une immersion brute dans votre métier, sans artifices.</p></div>
                </div>

                {{-- ── Chantier en Détail ── --}}
                <div class="space-y-4 group" x-data="{ current: 0, total: 3 }">
                    <div class="aspect-square bg-slate-800 rounded-2xl shadow-lg ring-1 ring-gray-200 overflow-hidden relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2">
                        <div class="absolute inset-0" x-show="current === 0"><div class="w-full h-full bg-slate-600 flex items-center justify-center"><p class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em]">Couverture du projet</p></div><div class="absolute inset-x-0 bottom-10 bg-black/60 backdrop-blur-sm p-6"><p class="text-white font-black text-sm uppercase leading-tight font-montserrat">Rénovation complète d'une toiture à Nantes</p></div></div>
                        <div class="absolute inset-0" x-show="current === 1"><div class="w-full h-full bg-slate-500 flex items-center justify-center font-bold text-white/20 select-none">PHOTO DÉTAIL 1</div></div>
                        <div class="absolute inset-0" x-show="current === 2"><div class="w-full h-full bg-slate-400 flex items-center justify-center font-bold text-white/20 select-none">PHOTO DÉTAIL 2</div></div>
                        <div class="absolute inset-x-0 bottom-0 py-2.5 px-6 flex items-center justify-center" style="background-color: rgba(0,0,0,0.8)">@if ($hasLogo)<img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-5 w-5 object-contain rounded absolute right-6">@endif<p class="text-white font-bold uppercase tracking-widest text-[10px] font-montserrat">{{ $companyLabel }}</p></div>
                        <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity"><button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button><button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button></div>
                        <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg" x-text="(current + 1) + ' / ' + total"></div>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">Chantier en Détail</h4><p class="text-gray-500 text-sm">Une immersion complète avec plusieurs photos d'un même projet.</p></div>
                </div>

                {{-- ── Step-by-Step ── --}}
                <div class="space-y-4 group" x-data="{ current: 0, total: 3 }">
                    <div class="aspect-square bg-slate-100 rounded-2xl shadow-lg ring-1 ring-gray-200 overflow-hidden relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2" style="border-left: 6px solid {{ $brandColor }}">
                        <div class="absolute inset-0 p-10 flex flex-col justify-center" x-show="current === 0" style="background-color: {{ $brandColor }}"><p class="text-white/30 font-black text-4xl mb-2 select-none tracking-tighter">ÉTAPES</p><div class="space-y-2"><div class="h-1.5 w-full bg-white/40 rounded-full"></div><div class="h-1.5 w-2/3 bg-white/40 rounded-full"></div></div></div>
                        <div class="absolute inset-0" x-show="current > 0"><div class="w-full h-full bg-slate-300 flex items-center justify-center font-bold text-slate-500/30 select-none">PHOTO ÉTAPE <span x-text="current"></span></div><div class="absolute top-6 left-6 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg" style="background-color: {{ $brandColor }}" x-text="current"></div><div class="absolute bottom-0 inset-x-0 h-16 bg-black/70 backdrop-blur-md p-4 flex items-center"><div class="h-1 w-2/3 bg-white/30 rounded-full"></div></div></div>
                        <div class="absolute bottom-0 inset-x-0 h-10 flex items-center justify-center px-4" :style="current === 0 ? 'background-color: rgba(0,0,0,0.2)' : 'background-color: transparent'"><p class="font-bold text-[9px] uppercase tracking-widest font-montserrat" :class="current === 0 ? 'text-white/60' : 'text-white'">{{ $companyLabel }}</p></div>
                        <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity"><button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button><button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button></div>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">Step-by-Step</h4><p class="text-gray-500 text-sm">Expliquez votre expertise étape par étape, didactique et rassurant.</p></div>
                </div>

                {{-- ── Conseil Saisonnier ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square rounded-2xl shadow-lg overflow-hidden relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2" style="background-color: #0B1426; border-top: 8px solid {{ $brandColor }}">
                        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full pointer-events-none" style="background: rgba(255,255,255,0.03)"></div>
                        <div class="absolute -top-5 -right-5 w-[104px] h-[104px] rounded-full pointer-events-none" style="background: rgba(255,255,255,0.04)"></div>
                        <div class="absolute inset-0 p-10 flex flex-col justify-center"><span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span><h3 class="font-black uppercase tracking-widest text-sm mb-5 font-montserrat" style="color: {{ $brandColor }}">Conseil Saisonnier</h3><p class="text-white font-black uppercase tracking-tight text-lg leading-snug font-montserrat">Vérifiez vos canalisations avant les premiers grands froids.</p></div>
                        <div class="absolute bottom-0 inset-x-0 border-t px-10 py-4" style="border-color: rgba(255,255,255,0.1)"><div class="flex items-center gap-2">@if ($hasLogo)<img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-5 w-5 object-contain rounded">@endif<p class="font-bold text-xs uppercase tracking-widest font-montserrat" style="color: {{ $brandColor }}">{{ $companyLabel }}</p></div></div>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">Conseil Saisonnier</h4><p class="text-gray-500 text-sm">Anticipez les besoins de vos clients selon la saison.</p></div>
                </div>

                {{-- ── Erreur à Éviter ── --}}
                <div class="space-y-4 group">
                    <div class="aspect-square rounded-2xl shadow-lg overflow-hidden relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2" style="background-color: #111827; border-top: 8px solid {{ $brandColor }}; border-left: 5px solid {{ $brandColor }}">
                        <div class="absolute inset-0 overflow-hidden pointer-events-none" style="opacity:0.045"><div class="absolute bg-white w-full h-3 top-1/2 left-0" style="transform: rotate(45deg) scaleX(1.6) translateY(-50%)"></div><div class="absolute bg-white w-full h-3 top-1/2 left-0" style="transform: rotate(-45deg) scaleX(1.6) translateY(-50%)"></div></div>
                        <div class="absolute inset-0 p-10 flex flex-col justify-center"><span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span><h3 class="font-black uppercase tracking-widest text-sm mb-5 font-montserrat" style="color: {{ $brandColor }}">Erreur à Éviter</h3><p class="text-white font-black uppercase tracking-tight text-lg leading-snug font-montserrat">Attendre que la fuite empire avant d'appeler un professionnel.</p></div>
                        <div class="absolute bottom-0 inset-x-0 border-t px-10 py-4" style="border-color: rgba(255,255,255,0.1)"><div class="flex items-center gap-2">@if ($hasLogo)<img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-5 w-5 object-contain rounded">@endif<p class="font-bold text-xs uppercase tracking-widest font-montserrat" style="color: {{ $brandColor }}">{{ $companyLabel }}</p></div></div>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">Erreur à Éviter</h4><p class="text-gray-500 text-sm">Un format alerte très engageant pour éduquer vos clients.</p></div>
                </div>

                {{-- ── Guide Pédagogique ── --}}
                <div class="space-y-4 group" x-data="{ current: 0, total: 3 }">
                    <div class="aspect-square rounded-2xl shadow-lg ring-1 ring-gray-200 overflow-hidden relative transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2 bg-slate-50">
                        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 2px 2px, black 1px, transparent 0); background-size: 24px 24px;"></div>
                        <div class="absolute inset-0 p-10 flex flex-col justify-center text-center" x-show="current === 0" style="background-color: {{ $brandColor }}"><p class="text-white/40 font-bold text-[10px] uppercase tracking-[0.2em] mb-6 select-none">CONSEIL D'EXPERT</p><div class="space-y-3"><div class="h-2 w-full bg-white/30 rounded-full"></div><div class="h-2 w-5/6 mx-auto bg-white/30 rounded-full"></div><div class="h-2 w-4/6 mx-auto bg-white/30 rounded-full"></div></div></div>
                        <div class="absolute inset-x-0 top-0 h-1.5" style="background-color: {{ $brandColor }}"></div>
                        <div class="absolute inset-0 p-12 flex flex-col justify-center" x-show="current > 0"><div class="w-10 h-10 rounded-lg flex items-center justify-center text-white mb-6 font-bold text-lg shadow-sm" style="background-color: {{ $brandColor }}" x-text="current + 1"></div><div class="space-y-4"><div class="h-1.5 w-full bg-slate-200 rounded-full"></div><div class="h-1.5 w-full bg-slate-200 rounded-full"></div><div class="h-1.5 w-3/4 bg-slate-200 rounded-full"></div></div></div>
                        <div class="absolute bottom-0 inset-x-0 h-12 flex items-center justify-center px-6" style="background-color: #1E293B"><p class="text-white/60 font-bold uppercase tracking-widest text-[9px] font-montserrat">{{ $companyLabel }}</p></div>
                        <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity"><button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button><button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button></div>
                    </div>
                    <div><h4 class="font-bold text-midnight text-lg">Guide Pédagogique</h4><p class="text-gray-500 text-sm">Apportez de la valeur et positionnez-vous en expert conseil.</p></div>
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
                        <h2 class="font-bold text-midnight text-lg">Créer un modèle avec IA</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Décrivez le type de post que vous souhaitez</p>
                    </div>
                </div>
                <button wire:click="closeModal" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-5">

                @if(!$generatedTemplate)
                {{-- Étape 1 : Demande --}}
                <div>
                    <label class="block text-sm font-semibold text-midnight mb-2">Décrivez votre modèle</label>
                    <textarea wire:model="userRequest"
                              placeholder="Ex: Je veux un modèle pour annoncer des promotions saisonnières, présenter un nouveau membre de mon équipe, partager une astuce technique..."
                              rows="4"
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-copper focus:border-transparent resize-none transition-all"
                              maxlength="300"></textarea>
                    <div class="flex items-center justify-between mt-1.5">
                        @error('userRequest')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400">Soyez précis — l'IA s'adaptera à votre description.</p>
                        @enderror
                        <span class="text-xs text-gray-400 tabular-nums">{{ strlen($userRequest) }}/300</span>
                    </div>
                </div>

                {{-- Photo mode --}}
                <div>
                    <label class="block text-sm font-semibold text-midnight mb-2">Ce modèle nécessite des photos ?</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['value' => 'none',     'label' => 'Aucune photo',     'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636', 'desc' => 'Post 100% textuel'],
                            ['value' => 'single',   'label' => '1 photo',          'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'desc' => 'Photo simple'],
                            ['value' => 'carousel', 'label' => 'Carrousel',        'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'desc' => '2 à 5 photos'],
                        ] as $pm)
                        <button type="button" wire:click="$set('photoMode', '{{ $pm['value'] }}')"
                                class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 text-center transition-all duration-150
                                    {{ $photoMode === $pm['value'] ? 'border-copper bg-amber-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                            <svg class="w-5 h-5 {{ $photoMode === $pm['value'] ? 'text-copper' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $pm['icon'] }}"/>
                            </svg>
                            <span class="text-xs font-bold {{ $photoMode === $pm['value'] ? 'text-amber-700' : 'text-gray-600' }}">{{ $pm['label'] }}</span>
                            <span class="text-[10px] text-gray-400">{{ $pm['desc'] }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Suggestions --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Idées d'inspiration</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Promotion saisonnière', 'Nouveau membre d\'équipe', 'Astuce technique du jour', 'Offre parrainage', 'Présentation d\'un outil', 'Chiffre clé de l\'entreprise'] as $example)
                        <button type="button"
                                wire:click="$set('userRequest', 'Modèle pour : {{ $example }}')"
                                class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-amber-50 hover:text-copper transition-colors">
                            {{ $example }}
                        </button>
                        @endforeach
                    </div>
                </div>

                @if($errorMessage)
                <div class="flex items-center gap-2 bg-red-50 border border-red-100 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ $errorMessage }}
                </div>
                @endif

                <button wire:click="generateTemplate"
                        wire:loading.attr="disabled"
                        class="w-full py-3 rounded-xl text-sm font-bold text-white bg-copper transition-all hover:bg-amber-500 hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-y-0 flex items-center justify-center gap-2 shadow-lg shadow-copper/25">
                    <span wire:loading.remove wire:target="generateTemplate">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <svg wire:loading wire:target="generateTemplate" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="generateTemplate">Générer avec IA</span>
                    <span wire:loading wire:target="generateTemplate">Génération en cours...</span>
                </button>

                @else
                {{-- Étape 2 : Résultat --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 bg-green-50 border border-green-100 text-green-700 rounded-xl px-4 py-3 text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Modèle généré avec succès par l'IA !
                    </div>

                    {{-- Aperçu --}}
                    <div class="rounded-xl border border-gray-100 overflow-hidden">
                        <div class="h-32 flex items-center justify-center relative overflow-hidden"
                             style="
                                @if($generatedTemplate['visual_style'] === 'brand_color') background-color: {{ $brandColor }};
                                @elseif($generatedTemplate['visual_style'] === 'light_branded') background-color: #F8FAFC;
                                @else background-color: #0B1426;
                                @endif
                             ">
                            <div class="absolute inset-x-0 top-0 h-1.5" style="background-color: {{ $brandColor }}"></div>
                            <div class="text-center px-6">
                                <p class="font-black uppercase tracking-widest text-xs mb-1 font-montserrat"
                                   style="color: {{ $generatedTemplate['visual_style'] === 'light_branded' ? $brandColor : ($generatedTemplate['visual_style'] === 'brand_color' ? 'rgba(255,255,255,0.8)' : $brandColor) }}">
                                    {{ strtoupper($generatedTemplate['name']) }}
                                </p>
                                <p class="font-bold text-xs"
                                   style="color: {{ $generatedTemplate['visual_style'] === 'light_branded' ? '#1E293B' : 'rgba(255,255,255,0.7)' }}">
                                    {{ $generatedTemplate['description'] }}
                                </p>
                            </div>
                            <div class="absolute bottom-2 right-2">
                                <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                                      style="background: rgba(255,255,255,0.15); color: {{ $generatedTemplate['visual_style'] === 'light_branded' ? '#64748B' : 'rgba(255,255,255,0.6)' }}">
                                    @if($generatedTemplate['visual_style'] === 'dark_branded') Fond sombre
                                    @elseif($generatedTemplate['visual_style'] === 'light_branded') Fond clair
                                    @else Couleur de marque
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-4 space-y-2 bg-gray-50">
                            <div><p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Nom</p><p class="text-sm font-bold text-midnight">{{ $generatedTemplate['name'] }}</p></div>
                            <div><p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Description</p><p class="text-sm text-gray-600">{{ $generatedTemplate['description'] }}</p></div>
                        </div>
                    </div>

                    @if($errorMessage)
                    <div class="flex items-center gap-2 bg-red-50 border border-red-100 text-red-700 rounded-xl px-4 py-3 text-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ $errorMessage }}
                    </div>
                    @endif

                    <div class="flex gap-3">
                        <button wire:click="$set('generatedTemplate', null)"
                                class="flex-1 py-2.5 rounded-xl border-2 border-gray-200 text-sm font-semibold text-gray-600 hover:border-gray-300 hover:bg-gray-50 transition-all">
                            Recommencer
                        </button>
                        <button wire:click="saveTemplate"
                                wire:loading.attr="disabled"
                                class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white bg-copper transition-all hover:bg-amber-500 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md shadow-copper/25">
                            <svg wire:loading.remove wire:target="saveTemplate" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg wire:loading wire:target="saveTemplate" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span wire:loading.remove wire:target="saveTemplate">Sauvegarder ce modèle</span>
                            <span wire:loading wire:target="saveTemplate">Sauvegarde...</span>
                        </button>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif

    @push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@800;900&display=swap');
        .font-montserrat { font-family: 'Montserrat', sans-serif; }
    </style>
    @endpush

</div>
