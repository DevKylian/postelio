<div>
    @if ($status === 'idle')
        <form wire:submit.prevent="generate" class="space-y-6">

            @php
            $templateGroups = [
                [
                    'key' => 'single',
                    'category' => 'Posts uniques',
                    'badge' => null,
                    'templates' => [
                        ['id' => 'review',        'label' => 'Avis Client',       'desc' => 'Un témoignage client transformé en citation premium.',        'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                        ['id' => 'project',       'label' => 'Avant / Après',     'desc' => 'Valorisez vos réalisations avec un split visuel percutant.',   'icon' => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['id' => 'faq',           'label' => 'FAQ Artisan',       'desc' => 'Répondez aux questions fréquentes de vos clients.',            'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['id' => 'lifestyle',     'label' => 'Lifestyle',         'desc' => 'Partagez l\'ambiance et le quotidien de votre métier.',        'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['id' => 'quality_focus',       'label' => 'Focus Qualité',        'desc' => 'Mettez en lumière vos matériaux ou équipements.',                          'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['id' => 'conseil_saisonnier',  'label' => 'Conseil Saisonnier',   'desc' => 'Un tip utile et saisonnier pour anticiper les besoins de vos clients.',    'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['id' => 'erreur_a_eviter',     'label' => 'Erreur à Éviter',      'desc' => 'Une erreur fréquente de vos clients — format alerte très engageant.',       'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    ],
                ],
                [
                    'key' => 'carousel',
                    'category' => 'Carrousels multi-slides',
                    'badge' => 'Multi-slides',
                    'templates' => [
                        ['id' => 'chantier_detail',   'label' => 'Chantier en Détail',  'desc' => 'Un chantier sous tous ses angles, slide par slide.',          'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                        ['id' => 'step_by_step',      'label' => 'Step-by-Step',        'desc' => 'Votre process de travail, étape par étape, rassurant.',       'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ['id' => 'guide_pedagogique', 'label' => 'Guide Pédagogique',   'desc' => 'Carrousel éducatif — positionnez-vous expert de votre domaine.', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ],
                ],
            ];

            $selectedTemplateInfo = null;
            foreach ($templateGroups as $group) {
                foreach ($group['templates'] as $tpl) {
                    if ($tpl['id'] === $template) {
                        $selectedTemplateInfo = $tpl;
                        break 2;
                    }
                }
            }
            @endphp

            {{-- ── STEP 1 : Template ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">1</div>
                    <div>
                        <h3 class="font-semibold text-midnight text-base">Choisissez votre gabarit</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Quel type de post voulez-vous créer ?</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    @foreach($templateGroups as $group)
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $group['category'] }}</span>
                            @if($group['badge'])
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    {{ $group['badge'] }}
                                </span>
                            @endif
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 {{ $group['key'] === 'single' ? 'lg:grid-cols-3' : 'lg:grid-cols-3' }} gap-3">
                            @foreach($group['templates'] as $tpl)
                            <button type="button"
                                    wire:click="$set('template', '{{ $tpl['id'] }}')"
                                    class="group relative flex flex-col gap-3 p-4 rounded-xl border-2 text-left transition-all duration-200
                                        {{ $template === $tpl['id']
                                            ? 'border-copper bg-copper/[0.04] shadow-sm'
                                            : 'border-gray-100 bg-white hover:border-gray-200 hover:bg-gray-50/50' }}">

                                <div class="flex items-start justify-between">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200
                                        {{ $template === $tpl['id']
                                            ? 'bg-copper text-white'
                                            : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $tpl['icon'] }}" />
                                        </svg>
                                    </div>
                                    @if($template === $tpl['id'])
                                    <div class="w-5 h-5 rounded-full bg-copper flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    @endif
                                </div>

                                <div>
                                    <p class="font-semibold text-sm {{ $template === $tpl['id'] ? 'text-copper' : 'text-midnight' }} leading-tight">
                                        {{ $tpl['label'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1 leading-relaxed">{{ $tpl['desc'] }}</p>
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── STEP 2 : Mémo Vocal ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="voiceRecorder">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">2</div>
                    <div>
                        <h3 class="font-semibold text-midnight text-base">Votre mémo vocal</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Décrivez votre projet — l'IA s'occupe du reste.</p>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Tab Switcher --}}
                    <div class="flex bg-gray-100 rounded-xl p-1 gap-1">
                        <button type="button" @click="mode = 'record'"
                                :class="mode === 'record' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-sm transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            Enregistrer
                        </button>
                        <button type="button" @click="mode = 'upload'"
                                :class="mode === 'upload' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-sm transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Importer un fichier
                            <span class="text-[9px] font-bold bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded-full leading-none">test</span>
                        </button>
                    </div>

                    {{-- Record Mode --}}
                    <div x-show="mode === 'record'">

                        {{-- Idle --}}
                        <div x-show="recState === 'idle'"
                             class="flex flex-col items-center justify-center min-h-[220px] rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 gap-5 transition-all">
                            <button type="button" @click="startRecording()"
                                    class="w-20 h-20 bg-copper rounded-full flex items-center justify-center text-white shadow-xl shadow-copper/25 hover:scale-105 hover:shadow-copper/40 transition-all duration-300 active:scale-95">
                                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                </svg>
                            </button>
                            <div class="text-center">
                                <p class="font-semibold text-midnight">Appuyez pour enregistrer</p>
                                <p class="text-sm text-gray-400 mt-1">Accès microphone requis · Max 5 minutes</p>
                            </div>
                        </div>

                        {{-- Recording --}}
                        <div x-show="recState === 'recording'"
                             class="flex flex-col items-center justify-center min-h-[220px] rounded-2xl bg-red-50 border-2 border-dashed border-red-200 gap-5 transition-all">
                            <div class="flex items-center gap-3">
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                <span class="text-red-600 font-bold text-3xl tabular-nums" x-text="formatTime(elapsed)">00:00</span>
                            </div>
                            <p class="text-red-400 text-sm">Enregistrement en cours...</p>
                            <button type="button" @click="stopRecording()"
                                    class="bg-red-500 text-white font-semibold px-8 py-3 rounded-xl hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20">
                                Arrêter l'enregistrement
                            </button>
                        </div>

                        {{-- Done / Preview --}}
                        <div x-show="recState === 'done'"
                             class="flex flex-col gap-4 p-5 rounded-2xl bg-amber-50 border-2 border-dashed border-amber-200 transition-all">
                            <audio :src="audioUrl" controls class="w-full rounded-xl h-12"></audio>
                            <div class="flex gap-3">
                                <button type="button" @click="confirmRecording()"
                                        class="flex-1 bg-copper text-white font-semibold py-3 rounded-xl hover:bg-amber-500 transition-colors text-sm shadow-md shadow-copper/20">
                                    Utiliser cet enregistrement
                                </button>
                                <button type="button" @click="resetRecording()"
                                        class="px-5 py-3 border-2 border-gray-200 text-gray-500 font-semibold rounded-xl hover:border-red-200 hover:text-red-500 hover:bg-red-50 transition-all text-sm bg-white">
                                    Recommencer
                                </button>
                            </div>
                        </div>

                        {{-- Uploading --}}
                        <div x-show="recState === 'uploading'"
                             class="flex flex-col items-center justify-center min-h-[220px] rounded-2xl bg-copper/5 border-2 border-dashed border-copper/20 gap-4 transition-all">
                            <svg class="animate-spin h-10 w-10 text-copper" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-midnight font-semibold">Chargement de l'enregistrement...</p>
                        </div>

                        {{-- Confirmed --}}
                        <div x-show="recState === 'confirmed'"
                             class="flex flex-col items-center justify-center min-h-[220px] rounded-2xl bg-green-50 border-2 border-dashed border-green-300 gap-4 transition-all">
                            <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-green-500/20">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="font-semibold text-green-800">Mémo enregistré !</p>
                                <button type="button" @click="resetRecording()"
                                        class="text-xs text-green-600 hover:text-green-800 underline mt-1">
                                    Recommencer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Mode --}}
                    <div x-show="mode === 'upload'">
                        <div x-data="{ uploadError: false }"
                             x-on:livewire-upload-error.window="uploadError = true"
                             class="relative">
                            <input type="file" wire:model="audio" id="audio-input" class="hidden" accept="audio/*"
                                   x-on:change="uploadError = false">
                            <label for="audio-input"
                                   @dragover.prevent
                                   @drop.prevent="uploadError = false"
                                   :class="uploadError ? 'border-red-300 bg-red-50' : ''"
                                   class="flex flex-col items-center justify-center w-full min-h-[220px] border-2 border-dashed rounded-2xl transition-all duration-200 cursor-pointer
                                        {{ $audio ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:border-copper hover:bg-copper/5 bg-gray-50' }}">
                                @if ($audio)
                                    <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-green-500/20 mb-3">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-green-800">Fichier chargé avec succès</p>
                                @else
                                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-copper shadow-md mb-4" x-show="!uploadError">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-midnight" x-show="!uploadError">Cliquez ou glissez votre fichier audio</p>
                                    <p class="text-sm text-gray-400 mt-1" x-show="!uploadError">MP3, WAV, M4A · Maximum 20 Mo</p>
                                    <p class="font-semibold text-red-600" x-show="uploadError">Fichier non supporté ou trop volumineux</p>
                                    <p class="text-sm text-red-400 mt-1" x-show="uploadError">Maximum 20 Mo — MP3, WAV ou M4A</p>
                                @endif
                            </label>
                            <div wire:loading wire:target="audio" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-2xl">
                                <svg class="animate-spin w-8 h-8 text-copper" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    @error('audio')
                        <p class="text-red-500 text-sm font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- ── STEP 3 : Photos (conditionnel) ── --}}
            @if(in_array($template, ['project', 'lifestyle', 'quality_focus']))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">3</div>
                    <div>
                        <h3 class="font-semibold text-midnight text-base">
                            {{ $template === 'project' ? 'Photos Avant & Après' : ($template === 'quality_focus' ? 'Photo du matériel' : 'Photo Lifestyle') }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $template === 'project' ? 'Uploadez les deux photos pour le split Avant / Après.' : 'Uploadez la photo principale du post.' }}
                        </p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 {{ $template === 'project' ? 'sm:grid-cols-2' : '' }} gap-4">

                        {{-- Image Avant / unique --}}
                        <div class="space-y-2">
                            @if($template === 'project')
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avant</span>
                            @endif
                            <div class="relative group h-52">
                                <input type="file" wire:model="imageBefore" id="before-input" class="hidden" accept="image/*">
                                <label for="before-input"
                                       class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-xl cursor-pointer transition-all overflow-hidden
                                            {{ $imageBefore ? 'border-green-400' : 'border-gray-200 hover:border-copper hover:bg-copper/5 bg-gray-50' }}">
                                    @if($imageBefore)
                                        <img src="{{ $imageBefore->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="text-white text-sm font-semibold bg-black/30 px-3 py-1.5 rounded-lg backdrop-blur-sm">Changer la photo</span>
                                        </div>
                                    @else
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-400">
                                            {{ $template === 'project' ? 'Photo Avant' : 'Choisir une photo' }}
                                        </span>
                                        <span class="text-xs text-gray-300 mt-1">JPG, PNG, WEBP</span>
                                    @endif
                                </label>
                                <div wire:loading wire:target="imageBefore" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-xl">
                                    <svg class="animate-spin w-6 h-6 text-copper" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                </div>
                            </div>
                            @error('imageBefore') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                        </div>

                        {{-- Image Après (project only) --}}
                        @if($template === 'project')
                        <div class="space-y-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Après</span>
                            <div class="relative group h-52">
                                <input type="file" wire:model="imageAfter" id="after-input" class="hidden" accept="image/*">
                                <label for="after-input"
                                       class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-xl cursor-pointer transition-all overflow-hidden
                                            {{ $imageAfter ? 'border-green-400' : 'border-gray-200 hover:border-copper hover:bg-copper/5 bg-gray-50' }}">
                                    @if($imageAfter)
                                        <img src="{{ $imageAfter->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="text-white text-sm font-semibold bg-black/30 px-3 py-1.5 rounded-lg backdrop-blur-sm">Changer la photo</span>
                                        </div>
                                    @else
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-400">Photo Après</span>
                                        <span class="text-xs text-gray-300 mt-1">JPG, PNG, WEBP</span>
                                    @endif
                                </label>
                                <div wire:loading wire:target="imageAfter" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-xl">
                                    <svg class="animate-spin w-6 h-6 text-copper" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                </div>
                            </div>
                            @error('imageAfter') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endif

            {{-- ── STEP 3 : Photos carrousel ── --}}
            @if(in_array($template, ['chantier_detail', 'step_by_step']))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">3</div>
                    <div class="flex items-center justify-between flex-1">
                        <div>
                            <h3 class="font-semibold text-midnight text-base">Photos du chantier</h3>
                            <p class="text-xs text-gray-400 mt-0.5">La 1ère photo sera la couverture du carrousel.</p>
                        </div>
                        <span class="text-xs font-bold text-copper bg-copper/10 px-2.5 py-1 rounded-full">2 à 5 photos</span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        @for($i = 0; $i < 5; $i++)
                        <div class="space-y-1.5">
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block">
                                {{ $i === 0 ? '★ Couverture' : 'Photo ' . $i }}
                            </span>
                            <div class="relative group aspect-square">
                                <input type="file" wire:model="extraImages.{{ $i }}" id="extra-{{ $i }}" class="hidden" accept="image/*">
                                <label for="extra-{{ $i }}"
                                       class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-xl cursor-pointer transition-all overflow-hidden
                                            {{ isset($extraImages[$i]) ? 'border-green-400' : 'border-gray-200 hover:border-copper hover:bg-copper/5 bg-gray-50' }}">
                                    @if(isset($extraImages[$i]))
                                        <img src="{{ $extraImages[$i]->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="text-white text-[10px] font-bold">Changer</span>
                                        </div>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span class="text-[10px] text-gray-300 font-medium">Ajouter</span>
                                    @endif
                                </label>
                                <div wire:loading wire:target="extraImages.{{ $i }}" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-xl">
                                    <svg class="animate-spin w-5 h-5 text-copper" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    @error('extraImages') <p class="text-red-500 text-xs font-medium mt-2">{{ $message }}</p> @enderror
                    @error('extraImages.*') <p class="text-red-500 text-xs font-medium mt-2">{{ $message }}</p> @enderror
                </div>
            </div>
            @endif

            {{-- Guide pédagogique : pas de photo --}}
            @if($template === 'guide_pedagogique')
            <div class="flex items-start gap-4 bg-blue-50 border border-blue-100 rounded-2xl p-5">
                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5 w-[18px] h-[18px] text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-blue-900 text-sm">Aucune photo requise</p>
                    <p class="text-blue-600 text-sm mt-0.5 leading-relaxed">Le Guide Pédagogique est un carrousel 100% textuel. L'IA génère toutes les slides à partir de votre mémo vocal uniquement.</p>
                </div>
            </div>
            @endif

            {{-- ── Planification ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-midnight text-base">Planification</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Publiez maintenant ou programmez pour plus tard.</p>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Toggle now / scheduled --}}
                    <div class="flex bg-gray-100 rounded-xl p-1 gap-1">
                        <button type="button"
                                wire:click="$set('scheduleType', 'now')"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-sm font-medium transition-all duration-200
                                    {{ $scheduleType === 'now' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Maintenant
                        </button>
                        <button type="button"
                                wire:click="$set('scheduleType', 'scheduled')"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-sm font-medium transition-all duration-200
                                    {{ $scheduleType === 'scheduled' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Planifier
                        </button>
                    </div>

                    @if($scheduleType === 'now')
                        <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Le post sera généré et ajouté à vos créations immédiatement.
                        </div>
                    @else
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Date & heure de publication</label>
                            <input type="datetime-local"
                                   wire:model="scheduledAt"
                                   min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-midnight bg-white focus:outline-none focus:border-copper focus:ring-2 focus:ring-copper/10 transition-all">
                            @error('scheduledAt')
                                <p class="text-red-500 text-xs font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-xs text-gray-400">Le post sera visible dans votre calendrier à la date choisie.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Submit ── --}}
            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="generate"
                    class="w-full relative group overflow-hidden bg-copper text-white font-bold text-sm py-5 rounded-2xl shadow-lg shadow-copper/25 hover:shadow-xl hover:shadow-copper/30 hover:bg-amber-500 transition-all duration-300 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed">
                <div class="flex items-center justify-center gap-3">
                    <span wire:loading.remove wire:target="generate" class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Lancer la génération
                    </span>
                    <span wire:loading wire:target="generate" class="flex items-center gap-3 animate-pulse">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Analyse en cours...
                    </span>
                </div>
            </button>

        </form>

    {{-- ── PROCESSING ── --}}
    @elseif($status === 'processing')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-24 flex flex-col items-center justify-center text-center space-y-6">
            <div class="relative">
                <div class="w-20 h-20 border-4 border-gray-100 border-t-copper rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-xl font-bold text-midnight">Génération en cours...</h3>
                <p class="text-gray-400 mt-1.5 text-sm">Whisper transcrit, GPT-4 rédige, l'image est en cours de création.</p>
            </div>
            <p class="text-xs text-gray-300">Cela prend généralement 30 à 60 secondes.</p>
        </div>

    {{-- ── REVIEW PENDING ── --}}
    @elseif($status === 'review_pending')
        <div class="space-y-5">

            {{-- Header --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 flex items-center gap-4">
                <div class="w-10 h-10 bg-copper/10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-midnight text-base">Vérifiez votre post</h3>
                    <p class="text-sm text-gray-400 mt-0.5">Relisez le contenu avant de valider et publier.</p>
                </div>
            </div>

            {{-- Preview grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">

                {{-- Image --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    @if(count($previewSlides) > 1)
                        <div x-data="{ current: 0, total: {{ count($previewSlides) }} }"
                             class="relative aspect-square select-none">
                            <div class="relative w-full h-full overflow-hidden"
                                 x-on:touchstart="$el._tx = $event.changedTouches[0].clientX"
                                 x-on:touchend="
                                    let dx = $event.changedTouches[0].clientX - $el._tx;
                                    if (dx < -40 && current < total - 1) current++;
                                    if (dx >  40 && current > 0) current--;
                                 ">
                                @foreach($previewSlides as $idx => $slideUrl)
                                <div x-show="current === {{ $idx }}"
                                     x-transition:enter="transition-opacity duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="absolute inset-0">
                                    <img src="{{ $slideUrl }}" alt="Slide {{ $idx + 1 }}" class="w-full h-full object-cover">
                                </div>
                                @endforeach
                            </div>

                            <button x-show="current > 0" @click="current--"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition-colors z-10 backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button x-show="current < total - 1" @click="current++"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition-colors z-10 backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                            </button>

                            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-10">
                                @foreach($previewSlides as $idx => $slideUrl)
                                <button @click="current = {{ $idx }}"
                                        :class="current === {{ $idx }} ? 'bg-white w-5' : 'bg-white/40 w-2'"
                                        class="h-1.5 rounded-full transition-all duration-300"></button>
                                @endforeach
                            </div>

                            <div class="absolute top-3 right-3 bg-black/50 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-full z-10"
                                 x-text="(current + 1) + ' / ' + total"></div>
                        </div>
                    @elseif($previewImageUrl)
                        <img src="{{ $previewImageUrl }}" alt="Post généré" class="w-full aspect-square object-cover">
                    @endif
                </div>

                {{-- Caption --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col gap-4">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Texte du post</p>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 min-h-[200px] max-h-[320px] overflow-y-auto">
                            <p class="text-midnight text-sm leading-relaxed whitespace-pre-line">{{ $previewContent }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 leading-relaxed">Copiez ce texte pour le modifier selon vos envies avant de le poster sur vos réseaux.</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <button wire:click="validatePost" wire:loading.attr="disabled"
                        class="flex-1 flex items-center justify-center gap-2.5 bg-copper text-white font-bold py-4 rounded-2xl shadow-lg shadow-copper/20 hover:bg-amber-500 hover:shadow-xl hover:shadow-copper/30 transition-all duration-200 active:scale-[0.99] disabled:opacity-60 text-sm">
                    <span wire:loading.remove wire:target="validatePost" class="flex items-center gap-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Valider et publier
                    </span>
                    <span wire:loading wire:target="validatePost" class="flex items-center gap-2.5 animate-pulse">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Validation...
                    </span>
                </button>

                <button wire:click="discardPost" wire:loading.attr="disabled"
                        wire:confirm="Ce post sera supprimé définitivement. Recommencer depuis zéro ?"
                        class="sm:w-auto flex items-center justify-center gap-2.5 border-2 border-gray-200 text-gray-500 font-semibold py-4 px-7 rounded-2xl hover:border-red-200 hover:text-red-500 hover:bg-red-50 bg-white transition-all duration-200 text-sm disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Rejeter et recommencer
                </button>
            </div>
        </div>

    {{-- ── FAILED ── --}}
    @elseif($status === 'failed')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-24 flex flex-col items-center justify-center text-center space-y-6">
            <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-500 animate-scale-in">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-midnight">Échec de la génération</h3>
                <p class="text-gray-400 mt-1.5 text-sm max-w-sm">Une erreur est survenue lors de la création de votre post. Vérifiez vos fichiers et réessayez.</p>
            </div>
            <button wire:click="$set('status', 'idle')"
                    class="bg-copper text-white font-semibold py-3 px-8 rounded-xl hover:bg-amber-500 transition-all shadow-md shadow-copper/20">
                Réessayer
            </button>
        </div>

    {{-- ── COMPLETED ── --}}
    @elseif($status === 'completed')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-24 flex flex-col items-center justify-center text-center space-y-6">
            <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 animate-scale-in">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-midnight">Post créé avec succès !</h3>
                <p class="text-gray-400 mt-1.5 text-sm">Votre visuel a été généré et ajouté à vos créations.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('posts.index') }}" wire:navigate
                   class="inline-flex items-center justify-center gap-2 bg-midnight text-white font-semibold py-3 px-7 rounded-xl hover:bg-slate-700 transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Voir mes créations
                </a>
                <button wire:click="$set('status', 'idle')"
                        class="inline-flex items-center justify-center gap-2 bg-gray-100 text-midnight font-semibold py-3 px-7 rounded-xl hover:bg-gray-200 transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Générer un autre post
                </button>
            </div>
        </div>
    @endif
</div>

@script
<script>
    Alpine.data('voiceRecorder', () => ({
        mode: 'record',
        recState: 'idle',
        mediaRecorder: null,
        chunks: [],
        audioUrl: null,
        audioBlob: null,
        elapsed: 0,
        timer: null,

        async startRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const mimeType = MediaRecorder.isTypeSupported('audio/mp4')
                    ? 'audio/mp4'
                    : MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                        ? 'audio/webm;codecs=opus'
                        : 'audio/webm';
                this.mediaRecorder = new MediaRecorder(stream, { mimeType });
                this.chunks = [];
                this.elapsed = 0;
                this.mediaRecorder.ondataavailable = (e) => { if (e.data.size > 0) this.chunks.push(e.data); };
                this.mediaRecorder.onstop = () => this.onStop();
                this.mediaRecorder.start(100);
                this.recState = 'recording';
                this.timer = setInterval(() => this.elapsed++, 1000);
            } catch (err) {
                alert('Impossible d\'accéder au microphone. Vérifiez les autorisations du navigateur.');
            }
        },

        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
                this.mediaRecorder.stream.getTracks().forEach(t => t.stop());
            }
            clearInterval(this.timer);
        },

        onStop() {
            const type = this.mediaRecorder.mimeType || 'audio/webm';
            this.audioBlob = new Blob(this.chunks, { type });
            if (this.audioUrl) URL.revokeObjectURL(this.audioUrl);
            this.audioUrl = URL.createObjectURL(this.audioBlob);
            this.recState = 'done';
        },

        confirmRecording() {
            this.recState = 'uploading';
            const type = this.audioBlob.type;
            const ext = type.includes('mp4') ? 'm4a' : type.includes('ogg') ? 'ogg' : 'webm';
            const file = new File([this.audioBlob], `recording.${ext}`, { type });
            this.$wire.upload(
                'audio',
                file,
                () => { this.recState = 'confirmed'; },
                () => { this.recState = 'done'; alert('Erreur lors de l\'envoi. Réessayez.'); }
            );
        },

        resetRecording() {
            this.recState = 'idle';
            if (this.audioUrl) URL.revokeObjectURL(this.audioUrl);
            this.audioUrl = null;
            this.audioBlob = null;
        },

        formatTime(s) {
            return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
        },
    }));
</script>
@endscript
