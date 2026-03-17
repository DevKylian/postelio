<div class="bg-white rounded-3xl shadow-xl shadow-midnight/5 border border-gray-100 p-8 md:p-12 overflow-hidden relative">
    <!-- Decorative elements -->
    <div class="absolute -top-24 -right-24 w-48 h-48 bg-copper/5 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-midnight/5 rounded-full blur-3xl"></div>

    <div class="relative">
        @if ($status === 'idle')
            <form wire:submit.prevent="generate" class="space-y-8">
                <!-- Template Selection -->
                <div class="space-y-4">
                    <label class="text-sm font-bold text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-copper"></span>
                        1. Choisissez votre gabarit
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button type="button" 
                                wire:click="$set('template', 'review')"
                                class="relative p-6 rounded-2xl border-2 transition-all duration-300 text-left group {{ $template === 'review' ? 'border-copper bg-copper/5 shadow-lg shadow-copper/10' : 'border-gray-100 hover:border-gray-200' }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-xl {{ $template === 'review' ? 'bg-copper text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100' }} transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </div>
                                @if($template === 'review')
                                    <span class="flex h-3 w-3 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-copper opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-copper"></span>
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-bold text-midnight text-lg">Avis Client</h4>
                            <p class="text-gray-500 text-sm mt-1">Transformez un témoignage vocal en citation premium.</p>
                        </button>

                        <button type="button" 
                                wire:click="$set('template', 'project')"
                                class="relative p-6 rounded-2xl border-2 transition-all duration-300 text-left group {{ $template === 'project' ? 'border-copper bg-copper/5 shadow-lg shadow-copper/10' : 'border-gray-100 hover:border-gray-200' }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-xl {{ $template === 'project' ? 'bg-copper text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100' }} transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                @if($template === 'project')
                                    <span class="flex h-3 w-3 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-copper opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-copper"></span>
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-bold text-midnight text-lg">Avant / Après</h4>
                            <p class="text-gray-500 text-sm mt-1">Mettez en valeur vos réalisations de chantier.</p>
                        </button>

                        <button type="button" 
                                wire:click="$set('template', 'faq')"
                                class="relative p-6 rounded-2xl border-2 transition-all duration-300 text-left group {{ $template === 'faq' ? 'border-copper bg-copper/5 shadow-lg shadow-copper/10' : 'border-gray-100 hover:border-gray-200' }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-xl {{ $template === 'faq' ? 'bg-copper text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100' }} transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                @if($template === 'faq')
                                    <span class="flex h-3 w-3 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-copper opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-copper"></span>
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-bold text-midnight text-lg">FAQ Artisan</h4>
                            <p class="text-gray-500 text-sm mt-1">Répondez aux questions fréquentes de vos clients.</p>
                        </button>

                        <button type="button" 
                                wire:click="$set('template', 'lifestyle')"
                                class="relative p-6 rounded-2xl border-2 transition-all duration-300 text-left group {{ $template === 'lifestyle' ? 'border-copper bg-copper/5 shadow-lg shadow-copper/10' : 'border-gray-100 hover:border-gray-200' }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 rounded-xl {{ $template === 'lifestyle' ? 'bg-copper text-white' : 'bg-gray-50 text-gray-400 group-hover:bg-gray-100' }} transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                @if($template === 'lifestyle')
                                    <span class="flex h-3 w-3 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-copper opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-copper"></span>
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-bold text-midnight text-lg">Lifestyle</h4>
                            <p class="text-gray-500 text-sm mt-1">Partagez l'ambiance et le quotidien de votre métier.</p>
                        </button>
                    </div>
                </div>

                <!-- Audio Upload -->
                <div class="space-y-4">
                    <label class="text-sm font-bold text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-copper"></span>
                        2. Envoyez votre mémo vocal
                    </label>
                    <div x-data="{ isDragging: false }" 
                         class="relative">
                        <input type="file" wire:model="audio" id="audio-input" class="hidden" accept="audio/*">
                        <label for="audio-input" 
                               @dragover.prevent="isDragging = true" 
                               @dragleave.prevent="isDragging = false"
                               @drop.prevent="isDragging = false"
                               class="flex flex-col items-center justify-center w-full min-h-[160px] border-2 border-dashed rounded-3xl transition-all duration-300 cursor-pointer {{ $audio ? 'bg-green-50 border-green-500 shadow-lg shadow-green-500/10' : 'border-gray-200 hover:border-copper hover:bg-copper/5' }} {{ $audio ? '' : 'bg-gray-50' }}">
                            
                            @if ($audio)
                                <div class="p-3 bg-green-500 rounded-full text-white mb-2 animate-bounce">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-green-700 font-bold text-sm">Mémo chargé !</span>
                            @else
                                <div class="p-4 bg-white rounded-full text-copper shadow-xl mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                    </svg>
                                </div>
                                <span class="text-midnight font-bold">Cliquez ou glissez votre audio</span>
                                <span class="text-gray-400 text-xs mt-1">Maximum 10 Mo</span>
                            @endif
                        </label>
                    </div>
                    @error('audio') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                @if($template === 'project' || $template === 'lifestyle')
                <!-- Image Upload Section -->
                <div class="space-y-4">
                    <label class="text-sm font-bold text-midnight uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-copper"></span>
                        3. {{ $template === 'project' ? 'Photos Avant / Après' : 'Photo Lifestyle' }}
                    </label>
                    <div class="grid grid-cols-1 {{ $template === 'project' ? 'sm:grid-cols-2' : '' }} gap-6">
                        <!-- Photo (Avant ou Unique) -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-gray-400 uppercase">{{ $template === 'project' ? 'Image Avant' : 'Photo Ambiance' }}</span>
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
                        <!-- After Image (Only for project) -->
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

                <div class="pt-8">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            wire:target="generate"
                            class="w-full relative group overflow-hidden bg-copper text-white font-black uppercase tracking-widest text-sm py-6 rounded-2xl shadow-xl shadow-copper/20 hover:shadow-2xl hover:shadow-copper/30 hover:-translate-y-1 transition-all duration-500 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                        
                        <!-- Background Glow Effect on Hover -->
                        <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>

                        <div class="relative flex items-center justify-center gap-3">
                            <!-- Spinner / Icon (Fixed Width Container) -->
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

                            <!-- Text -->
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
        @elseif($status === 'failed')
            <div class="py-20 flex flex-col items-center justify-center text-center space-y-8">
                <div class="w-24 h-24 bg-red-500 rounded-full flex items-center justify-center text-white shadow-2xl shadow-red-500/20 animate-scale-in">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-midnight">Oups ! Échec de la génération</h3>
                    <p class="text-gray-500 mt-2 text-lg">Une erreur est survenue lors de la création de votre post. Veuillez réessayer ou contacter le support.</p>
                </div>
                <div class="pt-4">
                    <button wire:click="$set('status', 'idle')" class="bg-copper text-white font-bold py-4 px-8 rounded-2xl hover:bg-copper/90 transition-all shadow-xl shadow-copper/20">
                        Réessayer
                    </button>
                </div>
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
                    <a href="{{ route('dashboard') }}" class="bg-midnight text-white font-bold py-4 px-8 rounded-2xl hover:bg-slate-800 transition-all text-center">
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
