<?php

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{layout, state};

layout('layouts.onboarding');

state([
    'step'              => 1,
    'trade'             => '',
    'name'              => '',
    'city'              => '',
    'brand_color'       => '#F59E0B',
    'weekly_posts_goal' => 3,
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
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
        ]);
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

$complete = function () {
    $this->validate([
        'name'        => ['required', 'string', 'min:2', 'max:255'],
        'city'        => ['required', 'string', 'max:255'],
        'brand_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
    ]);

    $user    = Auth::user();
    $company = $user->company;

    $data = [
        'name'              => $this->name,
        'city'              => $this->city,
        'trade'             => $this->trade,
        'brand_color'       => $this->brand_color,
        'weekly_posts_goal' => $this->weekly_posts_goal,
    ];

    if ($company) {
        $company->update($data);
    } else {
        Company::create(array_merge($data, ['user_id' => $user->id]));
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
                     style="width: {{ ($step / 5) * 100 }}%"></div>
            </div>

            {{-- Step indicators --}}
            <div class="px-4 sm:px-8 pt-5 pb-3">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] sm:text-xs text-gray-400 font-bold uppercase tracking-wider">Progression</span>
                    <span class="text-xs text-gray-400 font-medium">Étape {{ $step }} / 5</span>
                </div>
                <div class="flex items-center w-full">
                    @foreach(range(1, 5) as $i)
                        <div class="flex items-center {{ $i < 5 ? 'flex-1' : 'flex-none' }}">
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
                            @if($i < 5)
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
                                class="flex items-center gap-2 px-7 py-3 rounded-xl font-semibold text-sm transition-all duration-200
                                       {{ $trade
                                          ? 'bg-slate-800 text-white hover:bg-slate-700 shadow-lg hover:shadow-xl cursor-pointer'
                                          : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                            Continuer
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <input wire:model="city"
                                   type="text"
                                   placeholder="Ex : Lyon"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-slate-800 placeholder-gray-400 font-medium text-sm
                                          focus:outline-none focus:border-amber-400 focus:bg-white transition-all duration-200" />
                            @error('city')
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
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:text-slate-800 hover:bg-gray-100 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="nextStep" type="button"
                                class="flex items-center gap-2 px-7 py-3 rounded-xl font-semibold text-sm bg-slate-800 text-white hover:bg-slate-700 shadow-lg hover:shadow-xl transition-all duration-200">
                            Continuer
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:text-slate-800 hover:bg-gray-100 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="nextStep" type="button"
                                class="flex items-center gap-2 px-7 py-3 rounded-xl font-semibold text-sm bg-slate-800 text-white hover:bg-slate-700 shadow-lg hover:shadow-xl transition-all duration-200">
                            Continuer
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:text-slate-800 hover:bg-gray-100 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="nextStep" type="button"
                                class="flex items-center gap-2 px-7 py-3 rounded-xl font-semibold text-sm bg-slate-800 text-white hover:bg-slate-700 shadow-lg hover:shadow-xl transition-all duration-200">
                            Continuer
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif

                {{-- ===== STEP 5 : C'est parti ! ===== --}}
                @if($step === 5)
                <div>
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
                                class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:text-slate-800 hover:bg-gray-100 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Retour
                        </button>
                        <button wire:click="complete"
                                wire:loading.attr="disabled"
                                type="button"
                                class="flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm bg-amber-400 text-slate-900 hover:bg-amber-500 shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-60">
                            <span wire:loading.remove wire:target="complete">Accéder à mon espace</span>
                            <span wire:loading wire:target="complete">Enregistrement…</span>
                            <svg wire:loading.remove wire:target="complete" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
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
</div>
