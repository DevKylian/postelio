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

            // Inject custom templates as a separate group if any exist
            if ($customTemplates && count($customTemplates) > 0) {
                $customGroup = [
                    'key'       => 'custom',
                    'category'  => 'Mes modèles personnalisés',
                    'badge'     => 'IA',
                    'templates' => [],
                ];
                foreach ($customTemplates as $ct) {
                    $customGroup['templates'][] = [
                        'id'              => 'custom_' . $ct->id,
                        'custom_id'       => $ct->id,
                        'label'           => $ct->name,
                        'desc'            => $ct->description ?? 'Modèle personnalisé généré par IA.',
                        'icon'            => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                        'visual_style'    => $ct->visual_style,
                    ];
                }
                $templateGroups[] = $customGroup;
            }

            $selectedTemplateInfo = null;
            foreach ($templateGroups as $group) {
                foreach ($group['templates'] as $tpl) {
                    $tplId = $tpl['id'];
                    // For custom templates, match on 'custom' with the right custom_id
                    if ($group['key'] === 'custom') {
                        if ($template === 'custom' && isset($tpl['custom_id']) && $customTemplateId == $tpl['custom_id']) {
                            $selectedTemplateInfo = $tpl;
                            break 2;
                        }
                    } elseif ($tplId === $template) {
                        $selectedTemplateInfo = $tpl;
                        break 2;
                    }
                }
            }
            @endphp
            {{-- ── STEP 1 : Votre mémo vocal ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="voiceRecorder">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">1</div>
                    <div>
                        <h3 class="font-semibold text-midnight text-base">Votre mémo vocal</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Décrivez votre projet — l'IA pré-remplira les champs suivants.</p>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Tab Switcher --}}
                    <div class="flex bg-gray-100 rounded-xl p-1 gap-1">
                        <button type="button" @click="mode = 'record'"
                                :class="mode === 'record' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-sm transition-all duration-200">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            <span class="hidden sm:inline">Enregistrer</span>
                            <span class="sm:hidden">Vocal</span>
                        </button>
                        <button type="button" @click="mode = 'upload'"
                                :class="mode === 'upload' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-sm transition-all duration-200">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span class="hidden sm:inline">Importer</span>
                            <span class="sm:hidden">MP3</span>
                        </button>
                        <button type="button" @click="mode = 'text'"
                                :class="mode === 'text' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-sm transition-all duration-200">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Écrire
                        </button>
                    </div>

                    {{-- Record Mode --}}
                    <div x-show="mode === 'record'">
                        <div x-show="recState === 'idle'"
                             class="flex flex-col items-center justify-center min-h-[200px] rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 gap-5 transition-all">
                            <button type="button" @click="startRecording()"
                                    class="w-16 h-16 bg-copper rounded-full flex items-center justify-center text-white shadow-xl shadow-copper/25 hover:scale-105 hover:shadow-copper/40 transition-all duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                </svg>
                            </button>
                            <div class="text-center">
                                <p class="text-xs font-semibold text-midnight">Appuyez pour enregistrer</p>
                            </div>
                        </div>

                        <div x-show="recState === 'recording'"
                             class="flex flex-col items-center justify-center min-h-[200px] rounded-2xl bg-red-50 border-2 border-dashed border-red-200 gap-5 transition-all">
                            <div class="flex items-center gap-3">
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                <span class="text-red-600 font-bold text-2xl tabular-nums" x-text="formatTime(elapsed)">00:00</span>
                            </div>
                            <button type="button" @click="stopRecording()"
                                    class="bg-red-500 text-white font-semibold px-6 py-2 rounded-xl hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20 text-sm">
                                Arrêter
                            </button>
                        </div>

                        <div x-show="recState === 'done'"
                             class="flex flex-col gap-4 p-5 rounded-2xl bg-amber-50 border-2 border-dashed border-amber-200 transition-all">
                            <audio :src="audioUrl" controls class="w-full rounded-xl h-10"></audio>
                            <div class="flex gap-2">
                                <button type="button" @click="confirmRecording()"
                                        class="flex-1 bg-copper text-white font-semibold py-2.5 rounded-xl hover:bg-amber-500 transition-colors text-xs shadow-md shadow-copper/20">
                                    Analyser ce mémo
                                </button>
                                <button type="button" @click="resetRecording()"
                                        class="px-4 py-2.5 border-2 border-gray-200 text-gray-500 font-semibold rounded-xl hover:border-red-200 hover:text-red-500 hover:bg-red-50 transition-all text-xs bg-white">
                                    Recommencer
                                </button>
                            </div>
                        </div>

                        <div x-show="recState === 'uploading' || $wire.isAnalyzing"
                             class="flex flex-col items-center justify-center min-h-[200px] rounded-2xl bg-copper/[0.02] border-2 border-dashed border-copper/10 gap-6 transition-all">
                            <div class="flex items-center gap-1 h-8">
                                <div class="w-1 bg-copper rounded-full animate-[pulse_1s_infinite_0ms]" style="height: 40%"></div>
                                <div class="w-1 bg-copper rounded-full animate-[pulse_1s_infinite_100ms]" style="height: 70%"></div>
                                <div class="w-1 bg-copper rounded-full animate-[pulse_1s_infinite_200ms]" style="height: 100%"></div>
                                <div class="w-1 bg-copper rounded-full animate-[pulse_1s_infinite_300ms]" style="height: 60%"></div>
                                <div class="w-1 bg-copper rounded-full animate-[pulse_1s_infinite_400ms]" style="height: 80%"></div>
                            </div>
                            <div class="text-center space-y-1">
                                <p class="text-sm text-midnight font-bold">Analyse intelligente en cours</p>
                                <p class="text-[11px] text-gray-400">L'IA extrait les détails de votre projet...</p>
                            </div>
                        </div>

                        <div x-show="recState === 'confirmed' && !$wire.isAnalyzing"
                             class="flex flex-col items-center justify-center min-h-[200px] rounded-2xl bg-green-50 border-2 border-dashed border-green-300 gap-4 transition-all">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-green-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-semibold text-green-800">Mémo analysé avec succès !</p>
                                <button type="button" @click="resetRecording()"
                                        class="text-[10px] text-green-600 hover:text-green-800 underline mt-1">
                                    Enregistrer un autre vocal
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Mode --}}
                    <div x-show="mode === 'upload'">
                        <div x-data="{ uploadError: false }"
                             x-on:livewire-upload-error.window="uploadError = true"
                             class="relative">
                            <input type="file" wire:model.live="audio" id="audio-input-step1" class="hidden" accept="audio/*"
                                   x-on:change="uploadError = false">
                            <label for="audio-input-step1"
                                   class="flex flex-col items-center justify-center w-full min-h-[200px] border-2 border-dashed rounded-2xl transition-all duration-200 cursor-pointer
                                        {{ $audio ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:border-copper hover:bg-copper/5 bg-gray-50' }}">
                                @if ($audio && !$isAnalyzing)
                                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-green-500/20 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-green-800">Fichier chargé et analysé</p>
                                @elseif($isAnalyzing)
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="flex items-center gap-1 h-6">
                                            <div class="w-1 bg-copper rounded-full animate-[pulse_1s_infinite_0ms]" style="height: 60%"></div>
                                            <div class="w-1 bg-copper rounded-full animate-[pulse_1s_infinite_150ms]" style="height: 100%"></div>
                                            <div class="w-1 bg-copper rounded-full animate-[pulse_1s_infinite_300ms]" style="height: 70%"></div>
                                        </div>
                                        <p class="text-xs text-midnight font-bold">Analyse en cours...</p>
                                    </div>
                                @else
                                    <div wire:loading wire:target="audio" class="flex flex-col items-center animate-pulse">
                                        <svg class="animate-spin h-8 w-8 text-copper mb-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-xs text-midnight font-semibold">Envoi du fichier...</p>
                                    </div>
                                    <div wire:loading.remove wire:target="audio" class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-copper shadow-md mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-midnight">Importer votre mémo audio</p>
                                    </div>
                                @endif
                            </label>
                        </div>
                    </div>

                    {{-- Text Mode --}}
                    <div x-show="mode === 'text'" x-transition>
                        <div class="space-y-2">
                            <textarea
                                wire:model.defer="textContent"
                                placeholder="Décrivez votre projet en quelques mots — ex: 'Rénovation salle de bain chez Madame Dupont à Lyon, carrelage effet marbre, client très satisfait, travaux terminés en 3 jours'..."
                                rows="5"
                                maxlength="2000"
                                class="w-full px-4 py-3.5 border border-gray-200 rounded-xl text-sm text-midnight bg-white focus:outline-none focus:border-copper focus:ring-2 focus:ring-copper/10 transition-all placeholder-gray-300 resize-none leading-relaxed"
                            ></textarea>
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-gray-400">L'IA utilisera ce texte pour générer votre post — plus vous êtes précis, meilleur sera le résultat.</p>
                                <span class="text-[10px] text-gray-300 shrink-0 ml-3">max 2000 car.</span>
                            </div>
                        </div>
                        @error('textContent')
                            <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($analysisSuccess)
                    <div class="flex items-center gap-2 text-xs font-semibold text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        L'IA a pré-rempli les champs basés sur votre vocal.
                    </div>
                    @endif

                    @error('audio')
                        <p class="text-red-500 text-xs font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- ── STEP 2 : Template ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">2</div>
                    <div>
                        <h3 class="font-semibold text-midnight text-base">Choisissez votre gabarit</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Quel type de post voulez-vous créer ?</p>
                    </div>
                </div>

                <div class="p-6 space-y-3"
                     x-data="{
                         openGroups: {
                             carousel: @js(in_array($template, ['chantier_detail','step_by_step','guide_pedagogique'])),
                             custom:   @js($template === 'custom')
                         }
                     }">
                    @foreach($templateGroups as $group)
                    @php $isCollapsible = in_array($group['key'], ['carousel', 'custom']); @endphp

                    <div x-data="{}" {{ $isCollapsible ? '' : '' }}>
                        {{-- Group header --}}
                        @if($isCollapsible)
                        <button type="button"
                                @click="openGroups['{{ $group['key'] }}'] = !openGroups['{{ $group['key'] }}']"
                                class="w-full flex items-center gap-3 py-2 px-1 rounded-lg hover:bg-gray-50 transition-colors group">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $group['category'] }}</span>
                            @if($group['badge'])
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-copper bg-amber-50 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    {{ $group['badge'] }}
                                </span>
                            @endif
                            <div class="flex-1 h-px bg-gray-100"></div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-copper transition-all duration-200 shrink-0"
                                 :class="openGroups['{{ $group['key'] }}'] ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        @else
                        <div class="flex items-center gap-3 mb-3 px-1">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $group['category'] }}</span>
                            @if($group['badge'])
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-copper bg-amber-50 px-2 py-0.5 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    {{ $group['badge'] }}
                                </span>
                            @endif
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>
                        @endif

                        {{-- Templates grid --}}
                        <div
                            @if($isCollapsible)
                            x-show="openGroups['{{ $group['key'] }}']"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            @endif
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 {{ $isCollapsible ? 'mt-3' : '' }}">
                            @foreach($group['templates'] as $tpl)
                            @php
                                $isCustomTpl = $group['key'] === 'custom';
                                $isSelected  = $isCustomTpl
                                    ? ($template === 'custom' && $customTemplateId == ($tpl['custom_id'] ?? null))
                                    : ($template === $tpl['id']);
                            @endphp
                            <button type="button"
                                    @if($isCustomTpl)
                                        wire:click="selectCustomTemplate({{ $tpl['custom_id'] }})"
                                    @else
                                        wire:click="$set('template', '{{ $tpl['id'] }}'); $set('customTemplateId', null)"
                                    @endif
                                    class="group relative flex flex-col gap-3 p-4 rounded-xl border-2 text-left transition-all duration-200
                                        {{ $isSelected
                                            ? 'border-copper bg-copper/[0.04] shadow-sm'
                                            : 'border-gray-100 bg-white hover:border-gray-200 hover:bg-gray-50/50' }}">

                                <div class="flex items-start justify-between">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200
                                        {{ $isSelected
                                            ? 'bg-copper text-white'
                                            : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $tpl['icon'] }}" />
                                        </svg>
                                    </div>
                                    @if($isSelected)
                                    <div class="w-5 h-5 rounded-full bg-copper flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    @endif
                                </div>

                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <p class="font-semibold text-sm leading-tight {{ $isSelected ? 'text-copper' : 'text-midnight' }}">
                                            {{ $tpl['label'] }}
                                        </p>
                                        @if($isCustomTpl)
                                            <span class="text-[9px] font-bold text-copper bg-amber-50 px-1.5 py-0.5 rounded-full leading-none">IA</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1 leading-relaxed">{{ $tpl['desc'] }}</p>
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── PERSONNALISATION DU GABARIT ── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ expanded: false }">
                <button type="button" 
                        @click="expanded = !expanded" 
                        class="w-full px-6 py-5 border-b border-gray-50 flex items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">3</div>
                        <div class="text-left">
                            <h3 class="font-semibold text-midnight text-base leading-tight">
                                Personnaliser le gabarit 
                                <span class="hidden sm:inline text-gray-400 font-normal"> - Affinez le ton, le style et les détails — l'IA s'adapte.</span>
                            </h3>
                            <p class="sm:hidden text-xs text-gray-400 mt-0.5">Affinez le ton, le style et les détails — l'IA s'adapte.</p>
                        </div>
                    </div>
                    <div class="text-gray-400 group-hover:text-copper transition-colors shrink-0">
                        <svg class="w-5 h-5 transition-transform duration-300 transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <div class="p-6 space-y-6" 
                     x-show="expanded" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0">

                    {{-- ── Ton de voix ── --}}
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Ton de voix</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            @php
                            $tones = [
                                ['id' => 'professionnel', 'label' => 'Professionnel', 'desc' => 'Sérieux & direct', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'blue'],
                                ['id' => 'chaleureux',    'label' => 'Chaleureux',    'desc' => 'Humain & proche', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'color' => 'rose'],
                                ['id' => 'expert',        'label' => 'Expert',        'desc' => 'Technique & précis', 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'color' => 'teal'],
                                ['id' => 'dynamique',     'label' => 'Dynamique',     'desc' => 'Vendeur & impact', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'amber'],
                            ];
                            $colorMap = [
                                'blue'   => ['sel' => 'border-blue-400 bg-blue-50', 'icon' => 'bg-blue-100 text-blue-500', 'dot' => 'bg-blue-500', 'label' => 'text-blue-700'],
                                'rose'   => ['sel' => 'border-rose-400 bg-rose-50', 'icon' => 'bg-rose-100 text-rose-500', 'dot' => 'bg-rose-500', 'label' => 'text-rose-700'],
                                'teal'   => ['sel' => 'border-teal-400 bg-teal-50', 'icon' => 'bg-teal-100 text-teal-500', 'dot' => 'bg-teal-500', 'label' => 'text-teal-700'],
                                'amber'  => ['sel' => 'border-amber-400 bg-amber-50', 'icon' => 'bg-amber-100 text-amber-500', 'dot' => 'bg-amber-500', 'label' => 'text-amber-700'],
                            ];
                            @endphp
                            @foreach($tones as $t)
                            @php $c = $colorMap[$t['color']]; @endphp
                            <button type="button"
                                    wire:click="$set('tone', '{{ $t['id'] }}')"
                                    @if($isAnalyzing) disabled @endif
                                    class="group relative flex flex-col items-start gap-2.5 p-3.5 rounded-xl border-2 text-left transition-all duration-200
                                        {{ $tone === $t['id'] ? $c['sel'] . ' shadow-sm' : 'border-gray-100 bg-white hover:border-gray-200 hover:bg-gray-50' }}
                                        {{ $isAnalyzing ? 'animate-pulse opacity-60 pointer-events-none' : '' }}">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $tone === $t['id'] ? $c['icon'] : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $t['icon'] }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold {{ $tone === $t['id'] ? $c['label'] : 'text-midnight' }} leading-tight">{{ $t['label'] }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $t['desc'] }}</p>
                                </div>
                                @if($tone === $t['id'])
                                <div class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full {{ $c['dot'] }} flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Hashtags ── --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-midnight">Inclure des hashtags</p>
                                <p class="text-xs text-gray-400">L'IA ajoute 3-5 hashtags pertinents automatiquement</p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="$set('includeHashtags', {{ $includeHashtags ? 'false' : 'true' }})"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none
                                    {{ $includeHashtags ? 'bg-copper' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200
                                {{ $includeHashtags ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    {{-- ── Champs spécifiques au gabarit ── --}}

                    {{-- REVIEW : Prénom du client + service --}}
                    @if($template === 'review')
                    <div class="space-y-3 pt-1 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pt-2">Détails de l'avis</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-600 block">Prénom du client <span class="text-gray-400 font-normal">(optionnel)</span></label>
                                <input type="text"
                                       wire:model.defer="clientName"
                                       placeholder="ex: Marie, Jean-Pierre..."
                                       @if($isAnalyzing) disabled @endif
                                       class="w-full px-3.5 py-2.5 border rounded-xl text-sm text-midnight bg-white focus:outline-none focus:border-copper focus:ring-2 focus:ring-amber-100 transition-all placeholder-gray-300
                                            {{ $isAnalyzing ? 'animate-pulse bg-copper/[0.03] border-copper/20' : 'border-gray-200' }}">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-600 block">Service réalisé <span class="text-gray-400 font-normal">(optionnel)</span></label>
                                <input type="text"
                                       wire:model.defer="serviceType"
                                       placeholder="ex: pose de carrelage, peinture..."
                                       @if($isAnalyzing) disabled @endif
                                       class="w-full px-3.5 py-2.5 border rounded-xl text-sm text-midnight bg-white focus:outline-none focus:border-copper focus:ring-2 focus:ring-amber-100 transition-all placeholder-gray-300
                                            {{ $isAnalyzing ? 'animate-pulse bg-copper/[0.03] border-copper/20' : 'border-gray-200' }}">
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PROJECT : Type de travaux + durée --}}
                    @if($template === 'project')
                    <div class="space-y-3 pt-1 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pt-2">Détails du chantier</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-600 block">Type de travaux</label>
                                <select wire:model.defer="workCategory"
                                        @if($isAnalyzing) disabled @endif
                                        class="w-full px-3.5 py-2.5 border rounded-xl text-sm text-midnight bg-white focus:outline-none focus:border-copper focus:ring-2 focus:ring-amber-100 transition-all
                                            {{ $isAnalyzing ? 'animate-pulse bg-copper/[0.03] border-copper/20' : 'border-gray-200' }}">
                                    <option value="">Sélectionner...</option>
                                    <option value="Peinture">Peinture</option>
                                    <option value="Carrelage">Carrelage</option>
                                    <option value="Maçonnerie">Maçonnerie</option>
                                    <option value="Plomberie">Plomberie</option>
                                    <option value="Électricité">Électricité</option>
                                    <option value="Menuiserie">Menuiserie</option>
                                    <option value="Charpente / Toiture">Charpente / Toiture</option>
                                    <option value="Isolation">Isolation</option>
                                    <option value="Rénovation complète">Rénovation complète</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-600 block">Durée du chantier</label>
                                <select wire:model.defer="jobDuration"
                                        @if($isAnalyzing) disabled @endif
                                        class="w-full px-3.5 py-2.5 border rounded-xl text-sm text-midnight bg-white focus:outline-none focus:border-copper focus:ring-2 focus:ring-amber-100 transition-all
                                            {{ $isAnalyzing ? 'animate-pulse bg-copper/[0.03] border-copper/20' : 'border-gray-200' }}">
                                    <option value="">Sélectionner...</option>
                                    <option value="1 journée">1 journée</option>
                                    <option value="2-3 jours">2-3 jours</option>
                                    <option value="1 semaine">1 semaine</option>
                                    <option value="2 semaines">2 semaines</option>
                                    <option value="1 mois">1 mois</option>
                                    <option value="Plusieurs mois">Plusieurs mois</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- FAQ : Catégorie --}}
                    @if($template === 'faq')
                    <div class="space-y-3 pt-1 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pt-2">Catégorie de la FAQ</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @php
                            $faqCats = [
                                ['id' => 'technique',       'label' => 'Technique',       'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                                ['id' => 'tarifs',          'label' => 'Tarifs & délais', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['id' => 'entretien',       'label' => 'Entretien',       'icon' => 'M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z'],
                                ['id' => 'garanties',       'label' => 'Garanties',       'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            ];
                            @endphp
                            @foreach($faqCats as $cat)
                            <button type="button"
                                    wire:click="$set('faqCategory', '{{ $cat['id'] }}')"
                                    class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 text-center transition-all duration-200
                                        {{ $faqCategory === $cat['id'] ? 'border-copper bg-amber-50 shadow-sm' : 'border-gray-100 hover:border-gray-200 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 {{ $faqCategory === $cat['id'] ? 'text-copper' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $cat['icon'] }}" />
                                </svg>
                                <span class="text-xs font-semibold {{ $faqCategory === $cat['id'] ? 'text-amber-700' : 'text-gray-500' }} leading-tight">{{ $cat['label'] }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- CONSEIL SAISONNIER : Saison --}}
                    @if($template === 'conseil_saisonnier')
                    <div class="space-y-3 pt-1 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pt-2">Saison ciblée</p>
                        <div class="grid grid-cols-5 gap-2">
                            @php
                            $seasons = [
                                ['id' => 'auto',      'label' => 'Auto',      'emoji' => '🤖'],
                                ['id' => 'printemps', 'label' => 'Printemps', 'emoji' => '🌸'],
                                ['id' => 'ete',       'label' => 'Été',       'emoji' => '☀️'],
                                ['id' => 'automne',   'label' => 'Automne',   'emoji' => '🍂'],
                                ['id' => 'hiver',     'label' => 'Hiver',     'emoji' => '❄️'],
                            ];
                            @endphp
                            @foreach($seasons as $s)
                            <button type="button"
                                    wire:click="$set('season', '{{ $s['id'] }}')"
                                    class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 text-center transition-all duration-200
                                        {{ $season === $s['id'] ? 'border-copper bg-amber-50 shadow-sm' : 'border-gray-100 hover:border-gray-200 hover:bg-gray-50' }}">
                                <span class="text-2xl leading-none">{{ $s['emoji'] }}</span>
                                <span class="text-[10px] font-semibold {{ $season === $s['id'] ? 'text-amber-700' : 'text-gray-500' }}">{{ $s['label'] }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- GUIDE PÉDAGOGIQUE : Nombre de slides --}}
                    @if($template === 'guide_pedagogique')
                    <div class="space-y-3 pt-1 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest pt-2">Nombre de slides souhaité</p>
                        <div class="flex gap-2">
                            @foreach([3, 4, 5, 6, 7] as $n)
                            <button type="button"
                                    wire:click="$set('targetSlides', '{{ $n }}')"
                                    class="flex-1 py-2.5 rounded-xl border-2 text-sm font-bold transition-all duration-200
                                        {{ $targetSlides == $n ? 'border-copper bg-amber-50 text-amber-700 shadow-sm' : 'border-gray-100 text-gray-400 hover:border-gray-200 hover:bg-gray-50' }}">
                                {{ $n }}
                            </button>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400">Incluant la slide d'accroche et le call-to-action final.</p>
                    </div>
                    @endif

                    {{-- ── Note libre pour l'IA ── --}}
                    <div class="space-y-1.5 pt-1 border-t border-gray-100">
                        <label class="text-xs font-semibold text-gray-600 block pt-2">
                            Contexte supplémentaire <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <div class="relative">
                            <textarea wire:model.defer="customNote"
                                      placeholder="Précision pour l'IA — ex: 'Client dans le 16e Paris', 'Travaux urgence dégâts des eaux', 'Mettre en avant le label RGE'..."
                                      rows="2"
                                      maxlength="300"
                                      class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm text-midnight bg-white focus:outline-none focus:border-copper focus:ring-2 focus:ring-amber-100 transition-all placeholder-gray-300 resize-none"></textarea>
                            <span class="absolute bottom-2 right-3 text-[10px] text-gray-300 pointer-events-none">max 300 car.</span>
                        </div>
                        @error('customNote') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

            {{-- REMOVED OLD STEP 2 POSITION --}}

            {{-- ── STEP 4 : Photos (conditionnel) ── --}}
            @if(in_array($template, ['project', 'lifestyle', 'quality_focus']) || ($template === 'custom' && $customPhotoMode === 'single'))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">4</div>
                    <div>
                        <h3 class="font-semibold text-midnight text-base">
                            {{ $template === 'project' ? 'Photos Avant & Après' : ($template === 'quality_focus' ? 'Photo du matériel' : ($template === 'custom' ? 'Photo requise' : 'Photo Lifestyle')) }}
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

            {{-- ── STEP 4 : Photos carrousel ── --}}
            @if(in_array($template, ['chantier_detail', 'step_by_step']) || ($template === 'custom' && $customPhotoMode === 'carousel'))
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white font-bold text-sm shrink-0">4</div>
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
                        {{-- Custom date/time picker --}}
                        <div
                            x-data="{
                                selDay: null,
                                selHour: null,
                                days: [],
                                hours: [7,8,9,10,11,12,13,14,15,16,17,18,19,20,21],
                                init() {
                                    const today = new Date();
                                    for (let i = 0; i < 14; i++) {
                                        const d = new Date(today);
                                        d.setDate(today.getDate() + i);
                                        this.days.push(d);
                                    }
                                    this.selDay  = this.days[0];
                                    this.selHour = 9;
                                    this.sync();
                                },
                                dayLabel(d) {
                                    const labels = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];
                                    const today  = new Date();
                                    today.setHours(0,0,0,0);
                                    const dd = new Date(d);
                                    dd.setHours(0,0,0,0);
                                    const diff = Math.round((dd - today) / 86400000);
                                    if (diff === 0) return 'Auj.';
                                    if (diff === 1) return 'Dem.';
                                    return labels[d.getDay()];
                                },
                                dayNum(d)   { return d.getDate(); },
                                monthShort(d) {
                                    return ['jan','fév','mar','avr','mai','jun','jul','aoû','sep','oct','nov','déc'][d.getMonth()];
                                },
                                isSameDay(a, b) {
                                    if (!a || !b) return false;
                                    return a.getDate()===b.getDate() && a.getMonth()===b.getMonth() && a.getFullYear()===b.getFullYear();
                                },
                                pad(n) { return String(n).padStart(2,'0'); },
                                sync() {
                                    if (!this.selDay) return;
                                    const d = new Date(this.selDay);
                                    d.setHours(this.selHour ?? 9, 0, 0, 0);
                                    const fmt = d.getFullYear()+'-'+this.pad(d.getMonth()+1)+'-'+this.pad(d.getDate())+'T'+this.pad(d.getHours())+':'+this.pad(d.getMinutes());
                                    $wire.set('scheduledAt', fmt);
                                },
                                summary() {
                                    if (!this.selDay) return '';
                                    const dayNames = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
                                    const months   = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                                    return dayNames[this.selDay.getDay()] + ' ' + this.selDay.getDate() + ' ' + months[this.selDay.getMonth()] + ' à ' + this.pad(this.selHour) + 'h00';
                                }
                            }"
                            class="space-y-4">

                            {{-- Day chips --}}
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Jour</p>
                                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide snap-x">
                                    <template x-for="(d, i) in days" :key="i">
                                        <button type="button"
                                                @click="selDay = d; sync()"
                                                :class="isSameDay(selDay, d)
                                                    ? 'bg-copper text-white border-copper shadow-sm shadow-copper/20'
                                                    : 'bg-white text-gray-600 border-gray-200 hover:border-copper/40 hover:text-copper'"
                                                class="flex flex-col items-center justify-center min-w-[52px] h-[62px] rounded-xl border-2 transition-all duration-150 snap-start shrink-0 cursor-pointer">
                                            <span class="text-[10px] font-bold uppercase tracking-wide leading-none" x-text="dayLabel(d)"></span>
                                            <span class="text-lg font-black leading-tight mt-0.5" x-text="dayNum(d)"></span>
                                            <span class="text-[9px] leading-none opacity-70" x-text="monthShort(d)"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Hour chips --}}
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Heure</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="h in hours" :key="h">
                                        <button type="button"
                                                @click="selHour = h; sync()"
                                                :class="selHour === h
                                                    ? 'bg-copper text-white border-copper'
                                                    : 'bg-white text-gray-600 border-gray-200 hover:border-copper/40 hover:text-copper'"
                                                class="w-10 h-8 rounded-lg border-2 text-xs font-semibold transition-all duration-150 cursor-pointer"
                                                x-text="pad(h)+'h'"></button>
                                    </template>
                                </div>
                            </div>

                            {{-- Summary --}}
                            <div class="flex items-center gap-2 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                                <svg class="w-4 h-4 text-copper shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm font-semibold text-copper" x-text="summary()"></p>
                            </div>

                            @error('scheduledAt')
                                <p class="text-red-500 text-xs font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
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
                    <svg wire:loading.remove wire:target="generate" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <svg wire:loading wire:target="generate" class="animate-spin w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span wire:loading.remove wire:target="generate">Lancer la génération</span>
                    <span wire:loading wire:target="generate">Analyse en cours...</span>
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

            {{-- Instagram Post Preview --}}
            <div class="flex justify-center">
                <div class="w-full max-w-[540px] bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-lg">

                    {{-- Header Instagram --}}
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center gap-3">
                            {{-- Avatar : photo profil IG > logo > initiale --}}
                            <div class="w-10 h-10 rounded-full overflow-hidden shrink-0"
                                 style="box-shadow: 0 0 0 2px #fff, 0 0 0 3.5px {{ $brandColor }}">
                                @if($igProfilePicture)
                                    <img src="{{ $igProfilePicture }}" alt="Profil Instagram" class="w-full h-full object-cover">
                                @elseif($hasLogo)
                                    <img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white font-bold text-sm"
                                         style="background: linear-gradient(135deg, {{ $brandColor }}, {{ $brandColor }}bb)">
                                        {{ strtoupper(substr($company?->name ?? 'E', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-[13px] font-semibold text-black leading-none">{{ $igUsername }}</p>
                                <p class="text-[11px] text-gray-500 leading-none mt-0.5">{{ $igCity }}</p>
                            </div>
                        </div>
                        {{-- Kebab menu --}}
                        <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                        </svg>
                    </div>

                    {{-- Image / Carousel --}}
                    @if(count($previewSlides) > 1)
                        <div x-data="{ current: 0, total: {{ count($previewSlides) }} }"
                             class="relative aspect-square select-none bg-black">
                            <div class="relative w-full h-full overflow-hidden"
                                 x-on:touchstart="$el._tx = $event.changedTouches[0].clientX"
                                 x-on:touchend="let dx=$event.changedTouches[0].clientX-$el._tx; if(dx<-40&&current<total-1)current++; if(dx>40&&current>0)current--;">
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
                                    class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/40 text-white rounded-full flex items-center justify-center z-10 backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button x-show="current < total - 1" @click="current++"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/40 text-white rounded-full flex items-center justify-center z-10 backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            {{-- Dots Instagram --}}
                            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1 z-10">
                                @foreach($previewSlides as $idx => $slideUrl)
                                <div :class="current === {{ $idx }} ? 'w-2 h-2 bg-[#3897f0]' : 'w-1.5 h-1.5 bg-white/50'"
                                     class="rounded-full transition-all duration-200"></div>
                                @endforeach
                            </div>
                            <div class="absolute top-3 right-3 bg-black/40 backdrop-blur-sm text-white text-[11px] font-bold px-2 py-0.5 rounded-full z-10"
                                 x-text="(current + 1) + '/' + total"></div>
                        </div>
                    @elseif($previewImageUrl)
                        <div class="aspect-square bg-black">
                            <img src="{{ $previewImageUrl }}" alt="Post généré" class="w-full h-full object-cover">
                        </div>
                    @endif

                    {{-- Actions Instagram --}}
                    <div class="px-3.5 pt-3 pb-5 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                {{-- Like --}}
                                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                {{-- Comment --}}
                                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                {{-- Share --}}
                                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                                </svg>
                            </div>
                            {{-- Bookmark --}}
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                        </div>

                        {{-- Likes --}}
                        <p class="text-[13px] font-semibold text-black">247 J'aime</p>

                        {{-- Caption avec expand --}}
                        <div x-data="{ exp: false }" class="text-[13px] text-black leading-relaxed">
                            <p class="font-semibold leading-tight mb-1">{{ $igUsername }}</p>
                            <div x-show="!exp" class="whitespace-pre-line">{{ Str::limit($previewContent ?? '', 200) }}@if(strlen($previewContent ?? '') > 200) <button @click="exp = true" class="text-gray-400 font-medium">... plus</button>@endif</div>
                            <div x-show="exp" x-cloak class="whitespace-pre-line">{{ $previewContent }}</div>
                        </div>

                        {{-- Date --}}
                        <p class="text-[11px] text-gray-400 uppercase tracking-wide pt-0.5">Il y a 2 jours</p>
                    </div>

                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col items-center gap-3 w-full">

                {{-- Bouton principal : Valider (label adaptatif selon la planification) --}}
                <button wire:click="validatePost" wire:loading.attr="disabled"
                        class="w-full max-w-[540px] flex items-center justify-center gap-2 bg-copper text-white font-semibold py-3.5 px-5 rounded-xl shadow-md shadow-copper/20 hover:bg-amber-500 hover:shadow-lg hover:shadow-copper/30 transition-all duration-200 active:scale-[0.99] disabled:opacity-60 text-sm">
                    {{-- Icône : calendrier ou check, remplacée par spinner --}}
                    @if($scheduleType === 'scheduled' && $scheduledAt)
                        <svg wire:loading.remove wire:target="validatePost" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @else
                        <svg wire:loading.remove wire:target="validatePost" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @endif
                    <svg wire:loading wire:target="validatePost" class="animate-spin w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    {{-- Texte : toujours visible, ne cause pas de double-ligne --}}
                    @if($scheduleType === 'scheduled' && $scheduledAt)
                        <span>Planifier — {{ \Carbon\Carbon::parse($scheduledAt)->translatedFormat('d/m à H\hi') }}</span>
                    @else
                        <span>Valider et publier</span>
                    @endif
                </button>

                {{-- Ligne secondaire : Régénérer · Version courte · Copier · Rejeter --}}
                <div class="flex flex-wrap justify-center gap-2 w-full max-w-[540px]">

                    {{-- Régénérer le texte --}}
                    <button wire:click="regeneratePost(false)" wire:loading.attr="disabled"
                            class="flex items-center gap-1.5 border border-gray-200 bg-white text-gray-600 font-medium py-2 px-3.5 rounded-xl hover:border-copper hover:text-copper hover:bg-copper/5 transition-all duration-200 text-xs disabled:opacity-60">
                        <svg wire:loading.remove wire:target="regeneratePost(false)" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <svg wire:loading wire:target="regeneratePost(false)" class="animate-spin w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span wire:loading.remove wire:target="regeneratePost(false)">Régénérer</span>
                        <span wire:loading wire:target="regeneratePost(false)">En cours...</span>
                    </button>

                    {{-- Version courte --}}
                    <button wire:click="regeneratePost(true)" wire:loading.attr="disabled"
                            class="flex items-center gap-1.5 border border-gray-200 bg-white text-gray-500 font-medium py-2 px-3.5 rounded-xl hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50/50 transition-all duration-200 text-xs disabled:opacity-60">
                        <svg wire:loading.remove wire:target="regeneratePost(true)" class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <svg wire:loading wire:target="regeneratePost(true)" class="animate-spin w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span wire:loading.remove wire:target="regeneratePost(true)">Version courte</span>
                        <span wire:loading wire:target="regeneratePost(true)">En cours...</span>
                    </button>

                    {{-- Copier le texte --}}
                    <button x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText({{ Js::from($previewContent ?? '') }}).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="flex items-center gap-1.5 border border-gray-200 bg-white font-medium py-2 px-3.5 rounded-xl transition-all duration-200 text-xs"
                            :class="copied ? 'border-green-300 text-green-600 bg-green-50' : 'text-gray-500 hover:border-gray-300 hover:text-gray-700'">
                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="copied ? 'Copié !' : 'Copier le texte'"></span>
                    </button>

                    {{-- Rejeter --}}
                    <button wire:click="discardPost" wire:loading.attr="disabled"
                            wire:confirm="Ce post sera supprimé définitivement. Recommencer depuis zéro ?"
                            class="flex items-center gap-1.5 border border-gray-200 bg-white text-gray-400 font-medium py-2 px-3.5 rounded-xl hover:border-red-200 hover:text-red-500 hover:bg-red-50 transition-all duration-200 text-xs disabled:opacity-60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Rejeter
                    </button>
                </div>
            </div>
        </div>

    {{-- ── FAILED ── --}}
    @elseif($status === 'failed')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-16 px-8 flex flex-col items-center justify-center text-center space-y-5">
            <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center text-red-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="space-y-3 max-w-md">
                <h3 class="text-xl font-bold text-midnight">Échec de la génération</h3>
                @if($errorMessage)
                    <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3 text-left">
                        <p class="text-red-700 text-sm leading-relaxed">{{ $errorMessage }}</p>
                    </div>
                    <p class="text-gray-400 text-xs">Corrigez les champs de personnalisation ou videz-les, puis réessayez.</p>
                @else
                    <p class="text-gray-400 text-sm">Une erreur est survenue lors de la création de votre post. Vérifiez vos fichiers et réessayez.</p>
                @endif
            </div>
            <button wire:click="$set('status', 'idle'); $set('errorMessage', null)"
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

        init() {
            // Quand la transcription Livewire est prête, basculer vers l'onglet "Écrire"
            window.addEventListener('switch-to-text-mode', () => {
                this.mode = 'text';
            });
        },

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
