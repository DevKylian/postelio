<?php

use App\Models\Company;
use App\Jobs\TranscribeBioAudio;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{layout, state};

layout('layouts.onboarding');

state([
    'step'              => 1,
    'trade'             => '',
    'name'              => '',
    'city'              => '',
    'postal_code'       => '',
    'city_context'      => '',
    'nearby_cities'     => [],
    'phone'             => '',
    'email'             => '',
    'website'           => '',
    'brand_color'       => '#F59E0B',
    'weekly_posts_goal' => 3,
    // Step 5: bio
    'bio_mode'          => 'audio',   // 'audio' | 'text'
    'bio_text'          => '',
    'bio_audio_path'    => '',        // temp path after upload
    'bio_transcribing'  => false,
    'trades'      => [
        ['key' => 'plombier',      'label' => 'Plombier',       'icon' => '🔧'],
        ['key' => 'electricien',   'label' => 'Électricien',    'icon' => '⚡'],
        ['key' => 'menuisier',     'label' => 'Menuisier',      'icon' => '🔨'],
        ['key' => 'macon',         'label' => 'Maçon',          'icon' => '🧱'],
        ['key' => 'peintre',       'label' => 'Peintre',        'icon' => '🎨'],
        ['key' => 'couvreur',      'label' => 'Couvreur',       'icon' => '🏠'],
        ['key' => 'carreleur',     'label' => 'Carreleur',      'icon' => '📐'],
        ['key' => 'chauffagiste',  'label' => 'Chauffagiste',   'icon' => '🌡️'],
        ['key' => 'serrurier',     'label' => 'Serrurier',      'icon' => '🔑'],
        ['key' => 'paysagiste',    'label' => 'Paysagiste',     'icon' => '🌿'],
        ['key' => 'multiservices', 'label' => 'Multi-services', 'icon' => '🛠️'],
        ['key' => 'autre',         'label' => 'Autre',          'icon' => '⚙️'],
    ],
    'presetColors' => ['#F59E0B', '#3B82F6', '#10B981', '#EF4444', '#8B5CF6', '#EC4899', '#F97316', '#1E293B'],
]);

$selectTrade = function (string $trade) {
    $this->trade = $trade;
};

$nextStep = function () {
    if ($this->step === 2) {
        $this->validate([
            'name'  => ['required', 'string', 'min:2', 'max:255'],
            'city'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255', 'regex:/\..+/'],
        ], [
            'website.regex' => 'Le site internet doit être au format domaine.fr (contenir un point).',
        ]);
        
        if ($this->website) {
            $this->website = preg_replace('#^https?://#', '', $this->website);
            $this->website = preg_replace('#^www\.#', '', $this->website);
            $this->website = rtrim($this->website, '/');
        }
    }

    if ($this->step === 3) {
        $this->validate([
            'brand_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);
    }

    $this->step++;
};

$prevStep = function () {
    $this->step--;
};

$setBioAudioPath = function (string $path) {
    $this->bio_audio_path = $path;
};

$complete = function () {
    $this->validate([
        'name'        => ['required', 'string', 'min:2', 'max:255'],
        'city'        => ['required', 'string', 'max:255'],
        'phone'       => ['nullable', 'string', 'max:30'],
        'email'       => ['nullable', 'email', 'max:255'],
        'website'     => ['nullable', 'string', 'max:255', 'regex:/\..+/'],
        'brand_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
    ], [
        'website.regex' => 'Le site internet doit être au format domaine.fr (contenir un point).',
    ]);

    if ($this->website) {
        $this->website = preg_replace('#^https?://#', '', $this->website);
        $this->website = preg_replace('#^www\.#', '', $this->website);
        $this->website = rtrim($this->website, '/');
    }

    $user    = Auth::user();
    $company = $user->company;

    $data = [
        'name'              => $this->name,
        'city'              => $this->city,
        'postal_code'       => $this->postal_code ?: null,
        'city_context'      => $this->city_context ?: null,
        'nearby_cities'     => !empty($this->nearby_cities) ? $this->nearby_cities : null,
        'phone'             => $this->phone,
        'email'             => $this->email,
        'website'           => $this->website,
        'trade'             => $this->trade,
        'brand_color'       => $this->brand_color,
        'weekly_posts_goal' => $this->weekly_posts_goal,
        'bio_text'          => $this->bio_mode === 'text' ? trim($this->bio_text) : null,
        'bio_audio_path'    => $this->bio_audio_path ?: null,
    ];

    if ($company) {
        $company->update($data);
    } else {
        $company = Company::create(array_merge($data, ['user_id' => $user->id]));
    }

    // If bio via audio, dispatch async transcription job
    if ($this->bio_audio_path && $this->bio_mode === 'audio') {
        TranscribeBioAudio::dispatch($company->id, $this->bio_audio_path);
    }

    $user->update(['onboarding_completed' => true]);

    $this->redirect(route('dashboard', absolute: false), navigate: true);
};

?>

<div class="min-h-screen flex items-center justify-center p-4 sm:p-6">

    <div class="w-full max-w-2xl">

        {{-- Logo --}}
        <div class="text-center mb-8 flex justify-center">
            <x-logo size="lg" textClass="text-[1.7rem] text-white" />
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

            {{-- Progress bar --}}
            <div class="h-1.5 bg-gray-100">
                <div class="h-full bg-amber-400 transition-all duration-500 ease-out"
                     style="width: {{ ($step / 6) * 100 }}%"></div>
            </div>

            {{-- Step indicators --}}
            <div class="px-4 sm:px-8 pt-5 pb-3">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] sm:text-xs text-gray-400 font-bold uppercase tracking-wider">Progression</span>
                    <span class="text-xs text-gray-400 font-medium">Étape {{ $step }} / 6</span>
                </div>
                <div class="flex items-center w-full">
                    @foreach(range(1, 6) as $i)
                        <div class="flex items-center {{ $i < 6 ? 'flex-1' : 'flex-none' }}">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-300 flex-shrink-0
                                {{ $step > $i ? 'bg-amber-400 text-white' : ($step === $i ? 'bg-slate-800 text-white' : 'bg-gray-100 text-gray-400') }}">
                                @if($step > $i)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    {{ $i }}
                                @endif
                            </div>
                            @if($i < 6)
                                <div class="flex-1 h-px mx-1.5 sm:mx-2 {{ $step > $i ? 'bg-amber-400' : 'bg-gray-200' }} transition-colors duration-300"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="px-4 sm:px-8 pb-6 sm:pb-8 pt-4">

                {{-- ===== STEP 1 : Votre métier ===== --}}
                @if($step === 1)
                <div>
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-slate-800">Votre métier</h1>
                        <p class="text-gray-500 mt-1 text-sm">Sélectionnez votre corps de métier pour personnaliser votre expérience.</p>
                    </div>

                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        @foreach($trades as $t)
                            <button wire:click="selectTrade('{{ $t['key'] }}')"
                                    type="button"
                                    class="relative flex flex-col items-center gap-2 p-3 rounded-2xl border-2 transition-all duration-200 text-center
                                           {{ $trade === $t['key']
                                              ? 'border-amber-400 bg-amber-50 shadow-md scale-[1.03]'
                                              : 'border-gray-100 bg-gray-50 hover:border-gray-300 hover:bg-white hover:shadow-sm' }}">
                                @if($trade === $t['key'])
                                    <div class="absolute top-1.5 right-1.5 w-4 h-4 bg-amber-400 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @endif
                                <span class="text-2xl leading-none">{{ $t['icon'] }}</span>
                                <span class="text-xs font-semibold leading-tight {{ $trade === $t['key'] ? 'text-amber-700' : 'text-gray-600' }}">{{ $t['label'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button wire:click="nextStep"
                                type="button"
                                @if(!$trade) disabled @endif
                                class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-200
                                       {{ $trade
                                          ? 'bg-copper text-white hover:bg-amber-600 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-copper/20 hover:shadow-lg hover:shadow-copper/30 cursor-pointer'
                                          : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                            <svg wire:loading wire:target="nextStep" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="nextStep">Continuer</span>
                            <span wire:loading wire:target="nextStep">Chargement...</span>
                            <svg wire:loading.remove wire:target="nextStep" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- ===== STEP 2 : Votre entreprise ===== --}}
                @if($step === 2)
                <div>
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-slate-800">Votre entreprise</h1>
                        <p class="text-gray-500 mt-1 text-sm">Ces informations apparaîtront sur tous vos visuels générés.</p>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Nom de l'entreprise <span class="text-amber-400">*</span>
                            </label>
                            <input wire:model="name"
                                   type="text"
                                   placeholder="Ex : Dupont Plomberie"
                                   autofocus
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-slate-800 placeholder-gray-400 font-medium text-sm
                                          focus:outline-none focus:border-amber-400 focus:bg-white transition-all duration-200" />
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Ville <span class="text-amber-400">*</span>
                            </label>
                            <x-city-autocomplete
                                wire-city="city"
                                wire-postal="postal_code"
                                wire-context="city_context"
                                wire-nearby="nearby_cities"
                                :current-city="$city"
                                placeholder="Ex : Lannion, Lyon, Bordeaux..."
                                :required="true"
                                input-class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-slate-800 placeholder-gray-400 font-medium text-sm focus:outline-none focus:border-amber-400 focus:bg-white transition-all duration-200"
                            />
                            @if($city && $postal_code)
                                <p class="mt-1.5 text-xs text-green-600 font-medium">{{ $city }} — {{ $postal_code }}</p>
                            @endif
                            @error('city')
                                <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Téléphone (optionnel)
                                </label>
                                <input wire:model="phone"
                                       type="tel"
                                       placeholder="Ex : 06 01 02 03 04"
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-slate-800 placeholder-gray-400 font-medium text-sm
                                              focus:outline-none focus:border-amber-400 focus:bg-white transition-all duration-200" />
                                @error('phone')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Email (optionnel)
                                </label>
                                <input wire:model="email"
                                       type="email"
                                       placeholder="Ex : contact@artisan.fr"
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-slate-800 placeholder-gray-400 font-medium text-sm
                                              focus:outline-none focus:border-amber-400 focus:bg-white transition-all duration-200" />
                                @error('email')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Site internet (optionnel)
                            </label>
                            <input wire:model="website"
                                   type="text"
                                   placeholder="Ex : mon-entreprise.fr"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-slate-800 placeholder-gray-400 font-medium text-sm
                                          focus:outline-none focus:border-amber-400 focus:bg-white transition-all duration-200" />
                            @error('website')
                                <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if($name)
                    <div class="mt-5 p-4 rounded-2xl border border-gray-100 bg-gray-50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0"
                             style="background-color: {{ $brand_color }}">
                            {{ strtoupper(substr($name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate">{{ $name }}</p>
                            @if($city)<p class="text-xs text-gray-400">{{ $city }}</p>@endif
                        </div>
                    </div>
                    @endif

                    <div class="mt-8 flex items-center justify-between">
                        <button wire:click="prevStep" type="button"
                                class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 bg-white border-2 border-gray-100 hover:text-slate-800 hover:bg-gray-50 hover:border-gray-200 active:scale-[0.98] transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="nextStep" type="button"
                                class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm bg-copper text-white hover:bg-amber-600 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-copper/20 hover:shadow-lg hover:shadow-copper/30 transition-all duration-200">
                            <svg wire:loading wire:target="nextStep" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="nextStep">Continuer</span>
                            <span wire:loading wire:target="nextStep">Suivant...</span>
                            <svg wire:loading.remove wire:target="nextStep" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- ===== STEP 3 : Identité visuelle ===== --}}
                @if($step === 3)
                <div>
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-slate-800">Identité visuelle</h1>
                        <p class="text-gray-500 mt-1 text-sm">Choisissez votre couleur pour personnaliser vos visuels. Votre logo sera récupéré depuis votre compte Instagram.</p>
                    </div>

                    <div class="space-y-6">

                        {{-- Couleur de marque --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Couleur de marque</label>
                            <div class="flex flex-wrap gap-2.5 mb-3">
                                @foreach($presetColors as $color)
                                    <button wire:click="$set('brand_color', '{{ $color }}')"
                                            type="button"
                                            title="{{ $color }}"
                                            class="w-9 h-9 rounded-xl transition-all duration-200 border-2 shadow-sm
                                                   {{ $brand_color === $color ? 'scale-110 border-slate-800 shadow-md' : 'border-transparent hover:scale-105 hover:border-gray-300' }}"
                                            style="background-color: {{ $color }}">
                                    </button>
                                @endforeach
                                <label class="w-9 h-9 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center cursor-pointer hover:border-gray-400 transition-colors relative overflow-hidden" title="Couleur personnalisée">
                                    <span class="text-gray-400 text-lg leading-none">+</span>
                                    <input wire:model="brand_color" type="color" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                                </label>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg border border-gray-200 shadow-sm transition-all duration-200 flex-shrink-0"
                                     style="background-color: {{ $brand_color }}"></div>
                                <input wire:model="brand_color"
                                       type="text"
                                       placeholder="#F59E0B"
                                       class="w-32 px-3 py-2 rounded-lg border-2 border-gray-200 bg-gray-50 text-slate-800 text-sm font-mono uppercase
                                              focus:outline-none focus:border-amber-400 focus:bg-white transition-all duration-200" />
                            </div>
                            @error('brand_color')
                                <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Aperçu --}}
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Aperçu de votre marque</p>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-sm overflow-hidden transition-all duration-300"
                                     style="background-color: {{ $brand_color }}">
                                    {{ $name ? strtoupper(substr($name, 0, 1)) : '?' }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ $name ?: 'Votre entreprise' }}</p>
                                    @if($city)<p class="text-xs text-gray-400">{{ $city }}</p>@endif
                                </div>
                                <div class="ml-auto flex items-center gap-1.5">
                                    <div class="w-4 h-4 rounded-full border border-white/50 shadow-sm transition-all duration-300"
                                         style="background-color: {{ $brand_color }}"></div>
                                    <span class="text-xs font-mono text-gray-500 uppercase">{{ $brand_color }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between">
                        <button wire:click="prevStep" type="button"
                                class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 bg-white border-2 border-gray-100 hover:text-slate-800 hover:bg-gray-50 hover:border-gray-200 active:scale-[0.98] transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="nextStep" type="button"
                                class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm bg-copper text-white hover:bg-amber-600 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-copper/20 hover:shadow-lg hover:shadow-copper/30 transition-all duration-200">
                            <svg wire:loading wire:target="nextStep" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="nextStep">Continuer</span>
                            <span wire:loading wire:target="nextStep">Suivant...</span>
                            <svg wire:loading.remove wire:target="nextStep" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- ===== STEP 4 : Objectif de posts ===== --}}
                @if($step === 4)
                <div>
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-slate-800">Objectif hebdomadaire</h1>
                        <p class="text-gray-500 mt-1 text-sm">Combien de posts souhaitez-vous publier par semaine sur vos réseaux ?</p>
                    </div>

                    <div class="space-y-6">
                        <div class="relative pt-6 pb-2">
                            <input wire:model.live="weekly_posts_goal"
                                   type="range"
                                   min="0"
                                   max="7"
                                   step="1"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-amber-500" />
                            
                            <div class="flex justify-between mt-4 text-xs font-bold text-gray-400">
                                @for($i = 0; $i <= 7; $i++)
                                    <span class="{{ $weekly_posts_goal == $i ? 'text-amber-500 scale-125' : '' }} transition-all">{{ $i }}</span>
                                @endfor
                            </div>
                        </div>

                        <div class="p-5 rounded-2xl flex items-center gap-4 transition-colors duration-300 border
                                    {{ $weekly_posts_goal == 0 ? 'bg-red-50 border-red-100 text-red-800' : '' }}
                                    {{ $weekly_posts_goal >= 1 && $weekly_posts_goal <= 2 ? 'bg-orange-50 border-orange-100 text-orange-800' : '' }}
                                    {{ $weekly_posts_goal >= 3 && $weekly_posts_goal <= 5 ? 'bg-green-50 border-green-100 text-green-800' : '' }}
                                    {{ $weekly_posts_goal >= 6 ? 'bg-blue-50 border-blue-100 text-blue-800' : '' }}">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border shadow-sm bg-white
                                        {{ $weekly_posts_goal == 0 ? 'border-red-200' : '' }}
                                        {{ $weekly_posts_goal >= 1 && $weekly_posts_goal <= 2 ? 'border-orange-200' : '' }}
                                        {{ $weekly_posts_goal >= 3 && $weekly_posts_goal <= 5 ? 'border-green-200' : '' }}
                                        {{ $weekly_posts_goal >= 6 ? 'border-blue-200' : '' }}">
                                <span class="text-2xl font-black">{{ $weekly_posts_goal }}</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm">
                                    @if($weekly_posts_goal == 0)
                                        Non recommandé
                                    @elseif($weekly_posts_goal >= 1 && $weekly_posts_goal <= 2)
                                        Un peu juste
                                    @elseif($weekly_posts_goal >= 3 && $weekly_posts_goal <= 5)
                                        Recommandé
                                    @else
                                        Excellent 🚀
                                    @endif
                                </h3>
                                <p class="text-xs pt-1 opacity-80 leading-relaxed">
                                    @if($weekly_posts_goal == 0)
                                        Il est important d'être régulier sur les réseaux. Viser au moins 3 posts par semaine.
                                    @elseif($weekly_posts_goal >= 1 && $weekly_posts_goal <= 2)
                                        C'est un bon début, mais 3 à 4 posts par semaine créent plus d'engagement.
                                    @elseif($weekly_posts_goal >= 3 && $weekly_posts_goal <= 5)
                                        Vous avez le bon tempo pour construire et engager votre communauté.
                                    @else
                                        Une omniprésence parfaite pour maximiser votre visibilité !
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between">
                        <button wire:click="prevStep" type="button"
                                class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 bg-white border-2 border-gray-100 hover:text-slate-800 hover:bg-gray-50 hover:border-gray-200 active:scale-[0.98] transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="nextStep" type="button"
                                class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm bg-copper text-white hover:bg-amber-600 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-copper/20 hover:shadow-lg hover:shadow-copper/30 transition-all duration-200">
                            <svg wire:loading wire:target="nextStep" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="nextStep">Continuer</span>
                            <span wire:loading wire:target="nextStep">Suivant...</span>
                            <svg wire:loading.remove wire:target="nextStep" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- ===== STEP 5 : Votre présentation ===== --}}
                @if($step === 5)
                <div
                    x-data="{
                        mode: @entangle('bio_mode'),
                        recording: false,
                        recorded: false,
                        audioUrl: null,
                        mediaRecorder: null,
                        chunks: [],
                        stream: null,
                        timer: 0,
                        timerInterval: null,
                        canvasCtx: null,
                        animFrame: null,
                        async startRecording() {
                            try {
                                this.stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                                const canvas = this.$refs.waveCanvas;
                                if (canvas) {
                                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                                    const source = audioCtx.createMediaStreamSource(this.stream);
                                    const analyser = audioCtx.createAnalyser();
                                    analyser.fftSize = 256;
                                    source.connect(analyser);
                                    const bufferLength = analyser.frequencyBinCount;
                                    const dataArray = new Uint8Array(bufferLength);
                                    this.canvasCtx = canvas.getContext('2d');
                                    const draw = () => {
                                        this.animFrame = requestAnimationFrame(draw);
                                        analyser.getByteFrequencyData(dataArray);
                                        const ctx = this.canvasCtx;
                                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                                        const barWidth = (canvas.width / bufferLength) * 2.5;
                                        let x = 0;
                                        for (let i = 0; i < bufferLength; i++) {
                                            const barHeight = (dataArray[i] / 255) * canvas.height;
                                            ctx.fillStyle = '#F59E0B';
                                            ctx.beginPath();
                                            ctx.roundRect(x, canvas.height - barHeight, barWidth - 2, barHeight, 2);
                                            ctx.fill();
                                            x += barWidth;
                                        }
                                    };
                                    draw();
                                }
                                this.chunks = [];
                                this.mediaRecorder = new MediaRecorder(this.stream);
                                this.mediaRecorder.ondataavailable = e => { if (e.data.size > 0) this.chunks.push(e.data); };
                                this.mediaRecorder.onstop = () => this.handleRecordingStop();
                                this.mediaRecorder.start();
                                this.recording = true;
                                this.recorded = false;
                                this.timer = 0;
                                this.timerInterval = setInterval(() => this.timer++, 1000);
                            } catch(e) {
                                alert('Microphone non disponible. Vérifiez les permissions ou utilisez la saisie texte.');
                                this.mode = 'text';
                            }
                        },
                        stopRecording() {
                            if (this.mediaRecorder && this.recording) {
                                this.mediaRecorder.stop();
                                this.stream?.getTracks().forEach(t => t.stop());
                                this.recording = false;
                                clearInterval(this.timerInterval);
                                if (this.animFrame) cancelAnimationFrame(this.animFrame);
                            }
                        },
                        async handleRecordingStop() {
                            const blob = new Blob(this.chunks, { type: 'audio/webm' });
                            this.audioUrl = URL.createObjectURL(blob);
                            this.recorded = true;
                            // Upload to server
                            const formData = new FormData();
                            formData.append('audio', blob, 'bio.webm');
                            formData.append('_token', document.querySelector('meta[name=csrf-token]').content);
                            try {
                                const res = await fetch('/onboarding/upload-bio-audio', { method: 'POST', body: formData });
                                const data = await res.json();
                                if (data.path) $wire.setBioAudioPath(data.path);
                            } catch(e) { console.warn('Bio audio upload failed', e); }
                        },
                        resetRecording() {
                            this.recorded = false;
                            this.audioUrl = null;
                            this.chunks = [];
                            this.timer = 0;
                            $wire.setBioAudioPath('');
                        },
                        formatTime(s) {
                            const m = Math.floor(s / 60);
                            return (m > 0 ? m + 'm ' : '') + (s % 60) + 's';
                        }
                    }"
                >
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-slate-800">Présentez-vous</h1>
                        <p class="text-gray-500 mt-1 text-sm">Cette présentation permettra à Postelio de mieux personnaliser vos posts. Parlez de vous, vos services, votre zone d'intervention…</p>
                    </div>

                    {{-- Mode toggle --}}
                    <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl mb-6">
                        <button type="button"
                                @click="mode = 'audio'"
                                :class="mode === 'audio' ? 'bg-white shadow text-slate-800' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                            Mémo vocal
                        </button>
                        <button type="button"
                                @click="mode = 'text'"
                                :class="mode === 'text' ? 'bg-white shadow text-slate-800' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Saisie texte
                        </button>
                    </div>

                    {{-- Audio mode --}}
                    <div x-show="mode === 'audio'" x-cloak>

                        {{-- Not yet recorded --}}
                        <div x-show="!recorded">

                            {{-- Waveform canvas (visible during recording) --}}
                            <div x-show="recording" class="mb-4 rounded-2xl bg-slate-800 p-6 flex flex-col items-center gap-4 animate-in fade-in zoom-in duration-300">
                                <canvas x-ref="waveCanvas" width="400" height="80" class="w-full max-w-sm h-16 rounded-lg opacity-80"></canvas>
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex items-center gap-3 bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm border border-white/10">
                                        <span class="flex h-3 w-3 relative">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                        </span>
                                        <span class="text-white text-xl font-bold font-mono tracking-tighter" x-text="formatTime(timer)"></span>
                                    </div>
                                    <p class="text-white/40 text-[10px] font-bold uppercase tracking-widest">Enregistrement en cours…</p>

                                    {{-- Stop button (overlay/inside during record) --}}
                                    <button type="button"
                                            @click="stopRecording()"
                                            class="mt-2 bg-white text-red-600 hover:bg-red-50 px-6 py-2.5 rounded-xl text-sm font-black shadow-xl transition-all active:scale-[0.98] flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                                        Arrêter
                                    </button>
                                </div>
                            </div>

                            {{-- Idle state : Clickable area --}}
                            <div x-show="!recording" 
                                 @click="startRecording()"
                                 class="group mb-4 p-8 rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 hover:border-amber-400 hover:bg-amber-50/30 active:scale-[0.98] transition-all duration-300 flex flex-col items-center gap-5 cursor-pointer relative overflow-hidden">
                                
                                {{-- Hover background effect --}}
                                <div class="absolute inset-0 bg-gradient-to-br from-amber-400/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>

                                <div class="w-16 h-16 bg-copper rounded-full flex items-center justify-center text-white shadow-xl shadow-copper/25 group-hover:scale-105 group-hover:shadow-copper/40 transition-all duration-300 relative z-10">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </div>
                                <div class="text-center relative z-10">
                                    <p class="text-sm font-bold text-slate-800 tracking-tight">Appuyez pour vous présenter</p>
                                    <p class="text-[11px] text-gray-500 mt-1 max-w-[280px] leading-relaxed mx-auto">Parlez de vos services, de vos chantiers et de ce qui vous passionne !</p>
                                </div>
                            </div>
                        </div>

                        {{-- Recorded state --}}
                        <div x-show="recorded" class="space-y-4">
                            <div class="p-4 rounded-2xl bg-green-50 border border-green-200 flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-green-800">Présentation enregistrée !</p>
                                    <p class="text-xs text-green-600 mt-0.5">Durée : <span x-text="formatTime(timer)"></span> • Elle sera transcrite en arrière-plan</p>
                                </div>
                            </div>

                            <audio :src="audioUrl" controls class="w-full rounded-xl"></audio>

                            <button type="button" @click="resetRecording()"
                                    class="flex items-center gap-2 text-sm text-gray-500 hover:text-red-500 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Recommencer l'enregistrement
                            </button>
                        </div>
                    </div>

                    {{-- Text mode --}}
                    <div x-show="mode === 'text'" x-cloak>
                        <textarea
                            wire:model="bio_text"
                            rows="6"
                            placeholder="Ex : Je m'appelle Jean Dupont, je suis plombier depuis 15 ans à Lyon et environs. Je propose des services de plomberie générale, pose de sanitaires, recherche de fuites, dépannage urgent 24h/24…"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-slate-800 placeholder-gray-400 text-sm leading-relaxed resize-none
                                   focus:outline-none focus:border-amber-400 focus:bg-white transition-all duration-200"
                        ></textarea>
                        <p class="mt-2 text-xs text-gray-400">Plus vous êtes précis, plus Postelio pourra personnaliser vos posts.</p>
                    </div>

                    {{-- Skip link --}}
                    <p class="text-center mt-4 text-xs text-gray-400">
                        <span class="text-slate-400">Vous pouvez</span>
                        <button wire:click="nextStep" type="button" class="underline underline-offset-2 text-gray-500 hover:text-slate-700 transition-colors ml-1">
                            passer cette étape
                        </button>
                        <span class="text-slate-400">et compléter votre profil plus tard.</span>
                    </p>

                    <div class="mt-6 flex items-center justify-between">
                        <button wire:click="prevStep" type="button"
                                class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 bg-white border-2 border-gray-100 hover:text-slate-800 hover:bg-gray-50 hover:border-gray-200 active:scale-[0.98] transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="nextStep" type="button"
                                class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm bg-copper text-white hover:bg-amber-600 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-copper/20 hover:shadow-lg hover:shadow-copper/30 transition-all duration-200">
                            <svg wire:loading wire:target="nextStep" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="nextStep">Continuer</span>
                            <span wire:loading wire:target="nextStep">Suivant...</span>
                            <svg wire:loading.remove wire:target="nextStep" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- ===== STEP 6 : C'est parti ! ===== --}}
                @if($step === 6)
                <div x-data="{ showConfetti: false }" x-init="showConfetti = true">
                    {{-- Confetti Burst (Exact same as Welcome Hero) --}}
                    <template x-if="showConfetti">
                        <div class="absolute inset-x-0 top-1/2 flex items-center justify-center pointer-events-none z-[100]" x-cloak>
                            <div class="md-c md-c-1 burst"></div>
                            <div class="md-c md-c-2 burst"></div>
                            <div class="md-c md-c-3 burst"></div>
                            <div class="md-c md-c-4 burst"></div>
                            <div class="md-c md-c-5 burst"></div>
                            <div class="md-c md-c-6 burst"></div>
                            <div class="md-c md-c-7 burst"></div>
                            <div class="md-c md-c-8 burst"></div>
                            <div class="md-c md-c-9 burst"></div>
                            <div class="md-c md-c-10 burst"></div>
                            <div class="md-c md-c-11 burst"></div>
                            <div class="md-c md-c-12 burst"></div>
                            <div class="md-c md-c-13 burst"></div>
                            <div class="md-c md-c-14 burst"></div>
                            <div class="md-c md-c-15 burst"></div>
                            <div class="md-c md-c-16 burst"></div>
                            <div class="md-c md-c-17 burst"></div>
                            <div class="md-c md-c-18 burst"></div>
                        </div>
                    </template>
                    <div class="mb-6 text-center">
                        <div class="w-16 h-16 bg-amber-400 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-slate-800">Tout est prêt !</h1>
                        <p class="text-gray-500 mt-1 text-sm">Voici un résumé de votre profil. Vous pourrez tout modifier dans les paramètres.</p>
                    </div>

                    {{-- Récap --}}
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-xl flex-shrink-0 shadow-sm overflow-hidden"
                                 style="background-color: {{ $brand_color }}">
                                {{ $name ? strtoupper(substr($name, 0, 1)) : '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-800 text-sm">{{ $name }}</p>
                                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                    @if($city)
                                        <span class="text-xs text-gray-400">{{ $city }}</span>
                                        @if($trade)<span class="text-gray-300">·</span>@endif
                                    @endif
                                    @if($trade)
                                        @php
                                            $tradeLabel = collect($trades)->firstWhere('key', $trade)['label'] ?? $trade;
                                            $tradeIcon  = collect($trades)->firstWhere('key', $trade)['icon'] ?? '';
                                        @endphp
                                        <span class="text-xs text-gray-400">{{ $tradeIcon }} {{ $tradeLabel }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 border-white shadow-sm flex-shrink-0"
                                 style="background-color: {{ $brand_color }}"></div>
                        </div>

                        {{-- Bio recap --}}
                        @if($bio_text || $bio_audio_path)
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                @if($bio_audio_path)
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-700">Présentation ajoutée ✓</p>
                                <p class="text-xs text-slate-400 truncate">
                                    {{ $bio_audio_path ? 'Mémo vocal enregistré — transcription en cours' : Str::limit($bio_text, 60) }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-100">
                            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-2.5">Ce qui vous attend</p>
                            <ul class="space-y-2">
                                @foreach([
                                    'Créez votre premier post depuis un mémo vocal',
                                    'Des templates professionnels pour votre activité',
                                    'Publication directe sur Instagram',
                                    'Calendrier éditorial pour organiser vos posts',
                                ] as $item)
                                    <li class="flex items-start gap-2 text-xs text-amber-800">
                                        <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 mt-8">
                        <button wire:click="prevStep" type="button"
                                class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 bg-white border-2 border-gray-100 hover:text-slate-800 hover:bg-gray-50 hover:border-gray-200 active:scale-[0.98] transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="complete"
                                wire:loading.attr="disabled"
                                type="button"
                                class="flex items-center justify-center gap-2 w-full sm:w-auto px-7 py-3 rounded-xl font-bold text-sm bg-copper text-white hover:bg-amber-600 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-copper/20 hover:shadow-lg hover:shadow-copper/30 transition-all duration-300 disabled:opacity-60">
                            <svg wire:loading wire:target="complete" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="complete">Accéder à mon espace</span>
                            <span wire:loading wire:target="complete">Finalisation...</span>
                            <svg wire:loading.remove wire:target="complete" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            Vous pourrez modifier ces informations à tout moment dans les paramètres.
        </p>
    </div>

    <style>
        .md-c{position:absolute;width:8px;height:16px;border-radius:4px;opacity:0;left:50%;top:50%;margin-left:-4px;margin-top:-8px;z-index:99;box-shadow:0 0 10px rgba(0,0,0,0.1)}
        .md-c.burst{animation:mdCBurst 2.5s ease-in-out forwards}
        @keyframes mdCBurst{0%{opacity:1;transform:translate(0,0) rotate(0deg) scale(0)}40%{opacity:1;transform:translate(var(--tx),var(--ty)) rotate(var(--rot)) scale(1.5)}100%{opacity:0;transform:translate(calc(var(--tx)*1.2),calc(var(--ty) + 400px)) rotate(calc(var(--rot) + 400deg)) scale(1)}}
        .md-c-1{background:#ef4444;--tx:-180px;--ty:-220px;--rot:120deg;}
        .md-c-2{background:#f59e0b;--tx:180px;--ty:-240px;--rot:240deg; animation-delay:0.05s;}
        .md-c-3{background:#10b981;--tx:-120px;--ty:-280px;--rot:400deg;}
        .md-c-4{background:#3b82f6;--tx:120px;--ty:-250px;--rot:180deg; animation-delay:0.02s;}
        .md-c-5{background:#a855f7;--tx:-220px;--ty:-100px;--rot:310deg;}
        .md-c-6{background:#ef4444;--tx:220px;--ty:-80px;--rot:90deg; animation-delay:0.08s;}
        .md-c-7{background:#f59e0b;--tx:-150px;--ty:-150px;--rot:420deg;}
        .md-c-8{background:#10b981;--tx:150px;--ty:-150px;--rot:270deg; animation-delay:0.04s;}
        .md-c-9{background:#3b82f6;--tx:-80px;--ty:-320px;--rot:200deg;}
        .md-c-10{background:#a855f7;--tx:80px;--ty:-320px;--rot:250deg; animation-delay:0.06s;}
        .md-c-11{background:#ef4444;--tx:-250px;--ty:-180px;--rot:150deg; animation-delay:0.02s;}
        .md-c-12{background:#f59e0b;--tx:260px;--ty:-160px;--rot:320deg; animation-delay:0.09s;}
        .md-c-13{background:#10b981;--tx:-100px;--ty:-380px;--rot:410deg; animation-delay:0.05s;}
        .md-c-14{background:#3b82f6;--tx:140px;--ty:-360px;--rot:130deg; animation-delay:0.11s;}
        .md-c-15{background:#a855f7;--tx:-190px;--ty:-290px;--rot:340deg; animation-delay:0.07s;}
        .md-c-16{background:#ef4444;--tx:210px;--ty:-260px;--rot:110deg; animation-delay:0.03s;}
        .md-c-17{background:#f59e0b;--tx:-50px;--ty:-220px;--rot:460deg; animation-delay:0.06s;}
        .md-c-18{background:#10b981;--tx:40px;--ty:-250px;--rot:210deg; animation-delay:0.04s;}
    </style>
</div>
