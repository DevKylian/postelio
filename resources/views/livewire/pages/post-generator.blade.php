<div class="bg-white rounded-3xl shadow-xl shadow-midnight/5 border border-gray-100 p-8 md:p-12 overflow-hidden relative">
    <div class="absolute -top-24 -right-24 w-48 h-48 bg-copper/5 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-midnight/5 rounded-full blur-3xl"></div>

    <div class="relative">
        @if ($status === 'idle')
            <form wire:submit.prevent="generate" class="space-y-8">

                <!-- Template Selection -->
                <div class="space-y-5">
                    <label class="text-sm font-bold text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-copper"></span>
                        1. Choisissez votre gabarit
                    </label>

                    @php
                    $templateGroups = [
                        [
                            'key' => 'single',
                            'category' => 'Posts uniques',
                            'templates' => [
                                ['id' => 'review',        'label' => 'Avis Client',       'desc' => 'Transformez un témoignage vocal en citation premium.',                'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                                ['id' => 'project',       'label' => 'Avant / Après',     'desc' => 'Mettez en valeur vos réalisations de chantier.',                      'icon' => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z'],
                                ['id' => 'faq',           'label' => 'FAQ Artisan',       'desc' => 'Répondez aux questions fréquentes de vos clients.',                   'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['id' => 'lifestyle',     'label' => 'Lifestyle',         'desc' => 'Partagez l\'ambiance et le quotidien de votre métier.',               'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                ['id' => 'quality_focus', 'label' => 'Focus Qualité',    'desc' => 'Valorisez vos matériaux ou équipements professionnels.',              'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                            ],
                        ],
                        [
                            'key' => 'carousel',
                            'category' => 'Carrousels',
                            'templates' => [
                                ['id' => 'chantier_detail',  'label' => 'Chantier en Détail', 'desc' => 'Galerie multi-photos — montrez un chantier sous tous ses angles.',      'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                                ['id' => 'step_by_step',     'label' => 'Step-by-Step',       'desc' => 'Carrousel étapes — montrez votre process de travail pas à pas.',         'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                                ['id' => 'guide_pedagogique','label' => 'Guide Pédagogique',  'desc' => 'Carrousel éducatif — positionnez-vous en expert de votre métier.',      'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
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

                    <div class="space-y-4">
                        @foreach($templateGroups as $group)
                        <div>
                            <!-- Category header -->
                            <div class="flex items-center gap-3 mb-2.5">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 whitespace-nowrap">{{ $group['category'] }}</span>
                                <div class="flex-1 h-px bg-gray-100"></div>
                                @if($group['key'] === 'carousel')
                                <div class="flex items-center gap-1 text-[9px] font-black uppercase tracking-widest text-midnight/50">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    Multi-slides
                                </div>
                                @endif
                            </div>

                            <!-- Compact template cards -->
                            <div class="{{ $group['key'] === 'single' ? 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2' : 'grid grid-cols-1 sm:grid-cols-3 gap-2' }}">
                                @foreach($group['templates'] as $tpl)
                                <button type="button"
                                        wire:click="$set('template', '{{ $tpl['id'] }}')"
                                        class="group relative flex items-center gap-2.5 px-3 py-2.5 rounded-xl border-2 transition-all duration-200 text-left {{ $template === $tpl['id'] ? 'border-copper bg-copper/5 shadow-sm shadow-copper/10' : 'border-gray-100 hover:border-gray-200 bg-white hover:bg-gray-50' }}">

                                    <!-- Icon -->
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $template === $tpl['id'] ? 'bg-copper text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tpl['icon'] }}" />
                                        </svg>
                                    </div>

                                    <!-- Label -->
                                    <span class="text-xs font-bold leading-tight {{ $template === $tpl['id'] ? 'text-copper' : 'text-midnight' }}">{{ $tpl['label'] }}</span>

                                    <!-- Selected dot -->
                                    @if($template === $tpl['id'])
                                    <span class="absolute top-1.5 right-1.5 flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-copper opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-copper"></span>
                                    </span>
                                    @endif
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Selected template description strip -->
                    @if($selectedTemplateInfo)
                    <div class="flex items-center gap-3 px-4 py-3 bg-copper/5 border border-copper/20 rounded-xl">
                        <div class="w-8 h-8 rounded-lg bg-copper flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $selectedTemplateInfo['icon'] }}" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-black text-copper uppercase tracking-wide">{{ $selectedTemplateInfo['label'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 leading-snug">{{ $selectedTemplateInfo['desc'] }}</p>
                        </div>
                        <svg class="w-4 h-4 text-copper/40 flex-shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    @endif
                </div>

                <!-- Audio Section -->
                <div class="space-y-4" x-data="voiceRecorder">
                    <label class="text-sm font-bold text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-copper"></span>
                        2. Votre mémo vocal
                    </label>

                    <div class="flex bg-gray-100 rounded-2xl p-1 gap-1">
                        <button type="button" @click="mode = 'record'"
                                :class="mode === 'record' ? 'bg-white shadow-sm text-midnight font-bold' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            Enregistrer
                        </button>
                        <button type="button" @click="mode = 'upload'"
                                :class="mode === 'upload' ? 'bg-white shadow-sm text-midnight font-bold' : 'text-gray-400 hover:text-gray-600'"
                                class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Importer
                            <span class="text-[10px] bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded-full leading-none">test</span>
                        </button>
                    </div>

                    <div x-show="mode === 'record'">
                        <div x-show="recState === 'idle'"
                             class="flex flex-col items-center justify-center min-h-[180px] border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50 gap-4 transition-all">
                            <button type="button" @click="startRecording()"
                                    class="w-20 h-20 bg-copper rounded-full flex items-center justify-center text-white shadow-2xl shadow-copper/30 hover:scale-110 hover:shadow-copper/40 transition-all duration-300 active:scale-95">
                                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                </svg>
                            </button>
                            <span class="text-midnight font-bold">Appuyez pour enregistrer</span>
                            <span class="text-gray-400 text-xs">Microphone requis</span>
                        </div>

                        <div x-show="recState === 'recording'"
                             class="flex flex-col items-center justify-center min-h-[180px] border-2 border-dashed border-red-300 rounded-3xl bg-red-50 gap-5 transition-all">
                            <div class="flex items-center gap-3">
                                <span class="flex h-4 w-4 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                                </span>
                                <span class="text-red-600 font-black text-2xl tabular-nums" x-text="formatTime(elapsed)">00:00</span>
                            </div>
                            <span class="text-red-500 text-sm font-medium">Enregistrement en cours...</span>
                            <button type="button" @click="stopRecording()"
                                    class="bg-red-500 text-white font-bold px-8 py-3 rounded-2xl hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20">
                                Arrêter
                            </button>
                        </div>

                        <div x-show="recState === 'done'"
                             class="flex flex-col items-center justify-center min-h-[180px] border-2 border-dashed border-amber-300 rounded-3xl bg-amber-50 gap-5 p-6 transition-all">
                            <audio :src="audioUrl" controls class="w-full rounded-xl"></audio>
                            <div class="flex gap-3 w-full">
                                <button type="button" @click="confirmRecording()"
                                        class="flex-1 bg-copper text-white font-bold py-3 rounded-2xl hover:bg-copper/90 transition-colors text-sm shadow-lg shadow-copper/20">
                                    Utiliser cet enregistrement
                                </button>
                                <button type="button" @click="resetRecording()"
                                        class="bg-white border-2 border-gray-200 text-gray-500 font-bold py-3 px-5 rounded-2xl hover:border-red-200 hover:text-red-500 hover:bg-red-50 transition-all text-sm">
                                    Recommencer
                                </button>
                            </div>
                        </div>

                        <div x-show="recState === 'uploading'"
                             class="flex flex-col items-center justify-center min-h-[180px] border-2 border-dashed border-copper/30 rounded-3xl bg-copper/5 gap-4 transition-all">
                            <svg class="animate-spin h-10 w-10 text-copper" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-midnight font-bold">Chargement de l'enregistrement...</span>
                        </div>

                        <div x-show="recState === 'confirmed'"
                             class="flex flex-col items-center justify-center min-h-[180px] border-2 border-dashed border-green-500 rounded-3xl bg-green-50 gap-3 transition-all">
                            <div class="p-3 bg-green-500 rounded-full text-white animate-bounce">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-green-700 font-bold">Mémo enregistré !</span>
                            <button type="button" @click="resetRecording()"
                                    class="text-xs text-gray-400 hover:text-gray-600 underline">
                                Recommencer
                            </button>
                        </div>
                    </div>

                    <div x-show="mode === 'upload'">
                        <div x-data="{ isDragging: false }" class="relative">
                            <input type="file" wire:model="audio" id="audio-input" class="hidden" accept="audio/*">
                            <label for="audio-input"
                                   @dragover.prevent="isDragging = true"
                                   @dragleave.prevent="isDragging = false"
                                   @drop.prevent="isDragging = false"
                                   class="flex flex-col items-center justify-center w-full min-h-[180px] border-2 border-dashed rounded-3xl transition-all duration-300 cursor-pointer {{ $audio ? 'bg-green-50 border-green-500 shadow-lg shadow-green-500/10' : 'border-gray-200 hover:border-copper hover:bg-copper/5 bg-gray-50' }}">
                                @if ($audio)
                                    <div class="p-3 bg-green-500 rounded-full text-white mb-2 animate-bounce">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-green-700 font-bold text-sm">Fichier chargé !</span>
                                @else
                                    <div class="p-4 bg-white rounded-full text-copper shadow-xl mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <span class="text-midnight font-bold">Cliquez ou glissez votre audio</span>
                                    <span class="text-gray-400 text-xs mt-1">Maximum 10 Mo</span>
                                @endif
                            </label>
                        </div>
                    </div>

                    @error('audio') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Photo uploads — single image templates -->
                @if(in_array($template, ['project', 'lifestyle', 'quality_focus']))
                <div class="space-y-4">
                    <label class="text-sm font-bold text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-copper"></span>
                        3. {{ $template === 'project' ? 'Photos Avant / Après' : ($template === 'quality_focus' ? 'Photo du matériel / matériau' : 'Photo Lifestyle') }}
                    </label>
                    <div class="grid grid-cols-1 {{ $template === 'project' ? 'sm:grid-cols-2' : '' }} gap-6">
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-gray-400 uppercase">{{ $template === 'project' ? 'Image Avant' : 'Photo' }}</span>
                            <div class="relative group h-48">
                                <input type="file" wire:model="imageBefore" id="before-input" class="hidden" accept="image/*">
                                <label for="before-input" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-2xl cursor-pointer transition-all overflow-hidden {{ $imageBefore ? 'border-green-500' : 'border-gray-200 hover:border-copper hover:bg-copper/5' }}">
                                    @if($imageBefore)
                                        <img src="{{ $imageBefore->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold">Changer</div>
                                    @else
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span class="text-xs text-gray-400">{{ $template === 'project' ? 'Photo Avant' : 'Choisir une photo' }}</span>
                                    @endif
                                </label>
                            </div>
                            @error('imageBefore') <span class="text-red-500 text-[10px] font-medium">{{ $message }}</span> @enderror
                        </div>

                        @if($template === 'project')
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-gray-400 uppercase">Image Après</span>
                            <div class="relative group h-48">
                                <input type="file" wire:model="imageAfter" id="after-input" class="hidden" accept="image/*">
                                <label for="after-input" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-2xl cursor-pointer transition-all overflow-hidden {{ $imageAfter ? 'border-green-500' : 'border-gray-200 hover:border-copper hover:bg-copper/5' }}">
                                    @if($imageAfter)
                                        <img src="{{ $imageAfter->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold">Changer</div>
                                    @else
                                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <span class="text-xs text-gray-400">Photo Après</span>
                                    @endif
                                </label>
                            </div>
                            @error('imageAfter') <span class="text-red-500 text-[10px] font-medium">{{ $message }}</span> @enderror
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Multi-image upload — carousel templates -->
                @if(in_array($template, ['chantier_detail', 'step_by_step']))
                <div class="space-y-4">
                    <label class="text-sm font-bold text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-copper"></span>
                        3. Photos du chantier
                        <span class="text-[10px] font-bold bg-copper/10 text-copper px-2 py-0.5 rounded-full normal-case">2 à 5 photos</span>
                    </label>
                    <p class="text-xs text-gray-400">La 1ère photo sera la couverture du carrousel. Ajoutez ensuite les photos de détail.</p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @for($i = 0; $i < 5; $i++)
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $i === 0 ? 'Couverture' : 'Photo ' . $i }}</span>
                            <div class="relative group h-36">
                                <input type="file" wire:model="extraImages.{{ $i }}" id="extra-{{ $i }}" class="hidden" accept="image/*">
                                <label for="extra-{{ $i }}" class="flex flex-col items-center justify-center w-full h-full border-2 border-dashed rounded-xl cursor-pointer transition-all overflow-hidden {{ isset($extraImages[$i]) ? 'border-green-500' : 'border-gray-200 hover:border-copper hover:bg-copper/5' }}">
                                    @if(isset($extraImages[$i]))
                                        <img src="{{ $extraImages[$i]->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-bold">Changer</div>
                                    @else
                                        <svg class="w-6 h-6 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/></svg>
                                        <span class="text-[10px] text-gray-400">Ajouter</span>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endfor
                    </div>
                    @error('extraImages') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                    @error('extraImages.*') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                </div>
                @endif

                <!-- guide_pedagogique: no photos needed -->
                @if($template === 'guide_pedagogique')
                <div class="bg-copper/5 border border-copper/20 rounded-2xl p-5 flex gap-4 items-start">
                    <div class="p-2 bg-copper/10 rounded-xl flex-shrink-0">
                        <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-midnight">Aucune photo requise</p>
                        <p class="text-xs text-gray-500 mt-0.5">Le Guide Pédagogique est un carrousel 100% textuel. L'IA génère les slides à partir de votre mémo vocal.</p>
                    </div>
                </div>
                @endif

                <div class="pt-8">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="generate"
                            class="w-full relative group overflow-hidden bg-copper text-white font-black uppercase tracking-widest text-sm py-6 rounded-2xl shadow-xl shadow-copper/20 hover:shadow-2xl hover:shadow-copper/30 hover:-translate-y-1 transition-all duration-500 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                        <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        <div class="relative flex items-center justify-center gap-3">
                            <div class="w-6 h-5 flex items-center justify-center">
                                <div wire:loading.remove wire:target="generate">
                                    <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div wire:loading wire:target="generate">
                                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            <span wire:loading.remove wire:target="generate">Lancer la génération</span>
                            <span wire:loading wire:target="generate" class="animate-pulse">Analyse en cours...</span>
                        </div>
                    </button>
                </div>
            </form>

        @elseif($status === 'processing')
            <div class="py-20 flex flex-col items-center justify-center text-center space-y-6">
                <div class="relative">
                    <div class="w-24 h-24 border-8 border-gray-100 border-t-copper rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-10 h-10 text-copper animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-midnight font-premium">Génération en cours...</h3>
                    <p class="text-gray-500 mt-2">Whisper et GPT-4 travaillent sur votre post.</p>
                </div>
            </div>

        @elseif($status === 'review_pending')
            <div class="space-y-8">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-copper/10 rounded-2xl mb-4">
                        <svg class="w-7 h-7 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-midnight">Vérifiez votre post</h3>
                    <p class="text-gray-500 mt-1 text-sm">Relisez le contenu avant de le valider.</p>
                </div>

                <!-- Preview -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

                    <!-- Image / Carousel -->
                    <div>
                        @if(count($previewSlides) > 1)
                            {{-- Carousel: slider mobile + flèches desktop --}}
                            <div x-data="{ current: 0, total: {{ count($previewSlides) }} }"
                                 class="relative rounded-2xl overflow-hidden shadow-xl border border-gray-100 aspect-square select-none">

                                <!-- Slides -->
                                <div class="relative w-full h-full overflow-hidden"
                                     x-on:touchstart="$el._tx = $event.changedTouches[0].clientX"
                                     x-on:touchend="
                                        let dx = $event.changedTouches[0].clientX - $el._tx;
                                        if (dx < -40 && current < total - 1) current++;
                                        if (dx >  40 && current > 0) current--;
                                     ">
                                    @foreach($previewSlides as $idx => $slideUrl)
                                    <div x-show="current === {{ $idx }}"
                                         x-transition:enter="transition-opacity duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         class="absolute inset-0">
                                        <img src="{{ $slideUrl }}" alt="Slide {{ $idx + 1 }}" class="w-full h-full object-cover">
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Desktop arrows -->
                                <button x-show="current > 0"
                                        @click="current--"
                                        class="hidden md:flex absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/70 text-white rounded-full items-center justify-center transition-colors z-10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button x-show="current < total - 1"
                                        @click="current++"
                                        class="hidden md:flex absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/70 text-white rounded-full items-center justify-center transition-colors z-10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <!-- Dots -->
                                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-10">
                                    @foreach($previewSlides as $idx => $slideUrl)
                                    <button @click="current = {{ $idx }}"
                                            :class="current === {{ $idx }} ? 'bg-white w-5' : 'bg-white/50 w-2'"
                                            class="h-2 rounded-full transition-all duration-300"></button>
                                    @endforeach
                                </div>

                                <!-- Slide counter -->
                                <div class="absolute top-3 right-3 bg-black/50 text-white text-xs font-bold px-2.5 py-1 rounded-full z-10"
                                     x-text="(current + 1) + ' / ' + total"></div>
                            </div>
                        @elseif($previewImageUrl)
                            <div class="rounded-2xl overflow-hidden shadow-xl border border-gray-100">
                                <img src="{{ $previewImageUrl }}" alt="Post généré" class="w-full aspect-square object-cover">
                            </div>
                        @endif
                    </div>

                    <!-- Text content -->
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Contenu du post</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 min-h-[180px]">
                            <p class="text-midnight text-sm leading-relaxed whitespace-pre-line">{{ $previewContent }}</p>
                        </div>
                        <p class="text-xs text-gray-400">Vous pouvez copier et modifier ce texte directement sur vos réseaux sociaux.</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <button wire:click="validatePost" wire:loading.attr="disabled"
                            class="flex-1 flex items-center justify-center gap-2 bg-copper text-white font-black uppercase tracking-widest text-sm py-5 rounded-2xl shadow-xl shadow-copper/20 hover:shadow-2xl hover:shadow-copper/30 hover:-translate-y-1 transition-all duration-300 active:scale-95 disabled:opacity-70">
                        <div wire:loading.remove wire:target="validatePost">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div wire:loading wire:target="validatePost">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <span wire:loading.remove wire:target="validatePost">Valider et publier</span>
                        <span wire:loading wire:target="validatePost" class="animate-pulse">Validation...</span>
                    </button>

                    <button wire:click="discardPost" wire:loading.attr="disabled"
                            wire:confirm="Ce post sera supprimé définitivement. Recommencer depuis zéro ?"
                            class="sm:w-auto flex items-center justify-center gap-2 bg-white border-2 border-gray-200 text-gray-500 font-bold text-sm py-5 px-8 rounded-2xl hover:border-red-200 hover:text-red-500 hover:bg-red-50 transition-all duration-300 disabled:opacity-70">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Rejeter et recommencer
                    </button>
                </div>
            </div>

        @elseif($status === 'failed')
            <div class="py-20 flex flex-col items-center justify-center text-center space-y-8">
                <div class="w-24 h-24 bg-red-500 rounded-full flex items-center justify-center text-white shadow-2xl shadow-red-500/20 animate-scale-in">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-midnight">Oups ! Échec de la génération</h3>
                    <p class="text-gray-500 mt-2 text-lg">Une erreur est survenue lors de la création de votre post. Veuillez réessayer.</p>
                </div>
                <button wire:click="$set('status', 'idle')" class="bg-copper text-white font-bold py-4 px-8 rounded-2xl hover:bg-copper/90 transition-all shadow-xl shadow-copper/20">
                    Réessayer
                </button>
            </div>

        @elseif($status === 'completed')
            <div class="py-20 flex flex-col items-center justify-center text-center space-y-8">
                <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center text-white shadow-2xl shadow-green-500/20 animate-scale-in">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-midnight">C'est prêt !</h3>
                    <p class="text-gray-500 mt-2 text-lg">Votre post a été généré et ajouté à vos créations.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full justify-center pt-4">
                    <a href="{{ route('posts.index') }}" wire:navigate class="bg-midnight text-white font-bold py-4 px-8 rounded-2xl hover:bg-slate-800 transition-all text-center">
                        Voir mes créations
                    </a>
                    <button wire:click="$set('status', 'idle')" class="bg-gray-100 text-midnight font-bold py-4 px-8 rounded-2xl hover:bg-gray-200 transition-all">
                        Générer un autre post
                    </button>
                </div>
            </div>
        @endif
    </div>
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
