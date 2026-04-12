<div @if($hasPendingPosts) wire:poll.5s @endif>
    @if($posts->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-20 h-20 bg-white rounded-3xl border border-gray-100 shadow-sm flex items-center justify-center mb-5">
                <svg class="w-9 h-9 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-midnight mb-2">Aucune création pour l'instant</h3>
            <p class="text-gray-400 text-sm mb-6 max-w-sm">Envoyez un mémo vocal pour générer votre premier visuel professionnel en quelques secondes.</p>
            <x-primary-button :href="route('generate-post')" wire:navigate class="!gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Créer mon premier post
            </x-primary-button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($posts as $post)
                @if(($post->status === 'pending' || $post->status === 'processing') && !$post->image_path)
                    @php
                        $ratioClass = ($post->template_options['aspect_ratio'] ?? '1:1') === '4:5' ? 'aspect-[4/5]' : 'aspect-square';
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col animate-pulse">
                        <!-- Image placeholder -->
                        <div class="{{ $ratioClass }} relative overflow-hidden bg-gray-50 flex flex-col items-center justify-center">
                            <div class="w-8 h-8 border-4 border-gray-200 border-t-copper rounded-full animate-spin"></div>
                            <p class="text-[11px] text-gray-400 font-medium mt-3">Génération...</p>
                        </div>
                        
                        <!-- Infos placeholder -->
                        <div class="p-4 flex-1 flex flex-col justify-between gap-3">
                            <div>
                                <div class="h-2 bg-gray-100 rounded-full w-1/4 mb-3"></div>
                                <div class="h-3.5 bg-gray-100 rounded-full w-[90%] mb-2"></div>
                                <div class="h-3.5 bg-gray-100 rounded-full w-[60%]"></div>
                            </div>
                            
                            <div class="flex items-center gap-1.5 mt-1">
                                <div class="flex-1 h-8 bg-gray-50 rounded-lg"></div>
                                <div class="w-8 h-8 bg-gray-50 rounded-lg"></div>
                                <div class="w-8 h-8 bg-gray-50 rounded-lg"></div>
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        $slides     = $post->slides ?? [];
                        $isCarousel = count($slides) > 1;
                        $ratioClass = ($post->template_options['aspect_ratio'] ?? '1:1') === '4:5' ? 'aspect-[4/5]' : 'aspect-square';
                        $templateLabels = [
                            'review'            => 'Avis Client',
                            'project'           => 'Avant / Après',
                            'faq'               => 'FAQ Artisan',
                            'lifestyle'         => 'Lifestyle',
                            'quality_focus'       => 'Focus Qualité',
                            'conseil_saisonnier'  => 'Conseil Saisonnier',
                            'erreur_a_eviter'     => 'Erreur à Éviter',
                            'chantier_detail'     => 'Chantier en Détail',
                            'step_by_step'        => 'Step-by-Step',
                            'guide_pedagogique'   => 'Guide Pédagogique',
                        ];
                    @endphp

                    <div wire:click="selectPost({{ $post->id }})" class="cursor-pointer bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-lg hover:border-gray-200 transition-all duration-200 hover:-translate-y-0.5">

                        <!-- Image / Carousel -->
                        <div class="{{ $ratioClass }} relative overflow-hidden bg-gray-50">

                            @if($isCarousel)
                                <div x-data="{ current: 0, total: {{ count($slides) }} }"
                                     class="relative w-full h-full"
                                     x-on:touchstart="$el._tx = $event.changedTouches[0].clientX"
                                     x-on:touchend="
                                        let dx = $event.changedTouches[0].clientX - $el._tx;
                                        if (dx < -40 && current < total - 1) current++;
                                        if (dx >  40 && current > 0) current--;
                                     ">
                                    @foreach($slides as $idx => $slidePath)
                                    <div x-show="current === {{ $idx }}"
                                         x-transition:enter="transition-opacity duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         class="absolute inset-0">
                                        <img src="{{ Storage::url($slidePath) }}" alt="Slide {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                                    </div>
                                    @endforeach

                                    <!-- Arrows -->
                                    <button x-show="current > 0"
                                            @click.stop="current--"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 bg-black/40 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition-all z-10 opacity-0 group-hover:opacity-100 backdrop-blur-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button x-show="current < total - 1"
                                            @click.stop="current++"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 bg-black/40 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition-all z-10 opacity-0 group-hover:opacity-100 backdrop-blur-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    <!-- Dots -->
                                    <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1 z-10">
                                        @foreach($slides as $idx => $slidePath)
                                        <button @click.stop="current = {{ $idx }}"
                                                :class="current === {{ $idx }} ? 'bg-white w-4' : 'bg-white/50 w-1.5'"
                                                class="h-1 rounded-full transition-all duration-300"></button>
                                        @endforeach
                                    </div>

                                    <!-- Counter -->
                                    <div class="absolute top-2.5 right-2.5 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10"
                                         x-text="(current + 1) + ' / ' + total"></div>
                                </div>

                            @elseif($post->image_path)
                                <img src="{{ Storage::url($post->image_path) }}" alt="Post" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <!-- Template badge -->
                            <div class="absolute top-2.5 left-2.5 z-10">
                                <span class="px-2 py-1 bg-black/50 backdrop-blur-sm text-white text-[9px] font-bold uppercase tracking-widest rounded-full">
                                    {{ $templateLabels[$post->template] ?? 'Création' }}
                                </span>
                            </div>

                        </div>

                        <!-- Infos & Actions -->
                        <div class="p-4 flex-1 flex flex-col justify-between gap-3">
                                <div class="flex items-center justify-between mb-0.5">
                                    <p class="text-[10px] text-gray-300 font-medium">
                                        {{ $post->created_at->format('d/m/Y') }}
                                    </p>
                                    <span class="flex items-center gap-1 text-[10px] font-bold {{ $post->status_color_class }} px-1.5 py-0.5 rounded-md border text-center">
                                        {{ $post->status_label }}
                                        @if($post->status === 'completed' && $post->zernio_id)
                                            <button wire:click.stop="refreshStatus({{ $post->id }})" title="Vérifier le statut réel" class="ml-1 hover:rotate-180 transition-transform duration-500">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                            </button>
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm font-semibold text-midnight leading-snug line-clamp-2">
                                    {{ $post->caption ?? ($templateLabels[$post->template] ?? 'Post généré') }}
                                </p>

                            @php
                                $isScheduled = $post->scheduled_at && $post->scheduled_at->isFuture();
                                $hasFailed = $post->status === \App\Models\Post::STATUS_FAILED;
                                $platformsArr = $post->platforms ?? [];
                                $canPublishIg = $igConnected && !in_array('instagram', $platformsArr);
                                $canPublishFb = $fbConnected && !in_array('facebook', $platformsArr);
                            @endphp

                            @if($post->status === \App\Models\Post::STATUS_REVIEW_PENDING)
                                <a href="{{ route('generate-post', ['resume' => $post->id]) }}" wire:navigate
                                   class="w-full mb-3 flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-copper text-white text-[11px] font-bold hover:bg-amber-500 transition-colors shadow-sm shadow-copper/20 animate-pulse">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Reprendre le choix
                                </a>
                            @endif

                            @if($isScheduled)
                                <div class="w-full mb-3 p-2.5 bg-amber-50 rounded-xl border border-amber-100 flex items-center justify-between gap-2 animate-fade-in shadow-inner shadow-amber-100/50">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-7 h-7 bg-white rounded-lg flex items-center justify-center text-copper shadow-sm shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[9px] font-bold text-copper uppercase tracking-widest leading-none mb-0.5">Programmé</p>
                                            <p class="text-[11px] font-semibold text-amber-900 truncate">{{ $post->scheduled_at->translatedFormat('d M à H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button wire:click.stop="publishNow({{ $post->id }})" title="Publier maintenant" class="w-8 h-8 rounded-lg bg-white border border-amber-200 text-copper hover:bg-copper hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                        </button>
                                        <button wire:click.stop="editSchedule({{ $post->id }})" title="Modifier l'heure" class="w-8 h-8 rounded-lg bg-white border border-amber-200 text-copper hover:bg-copper hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </button>
                                        <button wire:click.stop="cancelSchedule({{ $post->id }})" title="Annuler" class="w-8 h-8 rounded-lg bg-white border border-red-200 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center gap-1.5">
                                @if(($canPublishIg || $canPublishFb) && !$isScheduled)
                                    <button wire:click.stop="openPublishModal({{ $post->id }})"
                                            class="flex-[1.2] flex items-center justify-center gap-1 py-2 px-2 rounded-lg bg-copper text-white text-[11px] font-semibold hover:bg-amber-500 transition-colors shadow-sm shadow-copper/20">
                                        <span class="truncate">Publier</span>
                                    </button>
                                @endif

                                @if($isCarousel)
                                    <button @click.stop onclick="downloadAllSlides({{ json_encode(array_map(fn($s) => Storage::url($s), $slides)) }}, @js($post->caption ?? ''))"
                                       class="flex-[1.5] flex items-center justify-center gap-1 py-2 px-2 rounded-lg bg-midnight text-white text-[11px] font-semibold hover:bg-midnight/80 transition-colors shadow-sm">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span class="truncate">Télécharger ({{ count($slides) }} visuels)</span>
                                    </button>
                                @else
                                    <a @click.stop href="{{ $post->image_path ? Storage::url($post->image_path) : '#' }}"
                                       {{ $post->image_path ? 'download' : '' }}
                                       class="flex-[1.5] flex items-center justify-center gap-1 py-2 px-2 rounded-lg bg-midnight text-white text-[11px] font-semibold hover:bg-midnight/80 transition-colors shadow-sm {{ !$post->image_path ? 'opacity-40 pointer-events-none' : '' }}">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span class="truncate">Télécharger</span>
                                    </a>
                                @endif

                                <!-- Copy text -->
                                <button x-data="{ copied: false }"
                                        @click.stop="
                                            navigator.clipboard.writeText({{ json_encode($post->caption ?? '') }});
                                            copied = true;
                                            setTimeout(() => copied = false, 1500);
                                        "
                                        class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-100 text-gray-400 hover:text-copper hover:border-copper/30 hover:bg-copper/5 transition-all relative group/copy">
                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-midnight text-white text-[9px] px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover/copy:opacity-100 transition-opacity pointer-events-none">
                                        Copier le texte
                                    </span>
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette création ?')" class="contents">
                                    @csrf
                                    @method('DELETE')
                                    <button @click.stop type="submit"
                                            class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-100 text-gray-300 hover:text-red-500 hover:border-red-100 hover:bg-red-50 transition-all group/del relative">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-red-600 text-white text-[9px] px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover/del:opacity-100 transition-opacity pointer-events-none">
                                            Supprimer
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- Modal Preview User -->
    @if($selectedPost)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-midnight/60 backdrop-blur-sm" wire:click.self="closeModal">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl md:h-[650px] max-h-[90vh] overflow-hidden flex flex-col md:flex-row transform transition-all border border-white/20">
                
                <!-- Image Side -->
                <div class="w-full md:w-1/2 bg-gray-50 relative flex items-center justify-center p-8 border-r border-gray-100 min-h-[350px]">
                    @php
                        $selectedSlides = $selectedPost->slides ?? [];
                        $modalRatioClass = ($selectedPost->template_options['aspect_ratio'] ?? '1:1') === '4:5' ? 'aspect-[4/5]' : 'aspect-square';
                    @endphp
                    @if(count($selectedSlides) > 1)
                        <div x-data="{ current: 0, total: {{ count($selectedSlides) }} }" class="w-full h-full flex flex-col items-center justify-center">
                            <div class="relative w-full {{ $modalRatioClass }} rounded-2xl overflow-hidden shadow-sm border border-gray-200 bg-white group/modal">
                                @foreach($selectedSlides as $idx => $slidePath)
                                    <div x-show="current === {{ $idx }}" x-transition.opacity class="absolute inset-0">
                                        <img src="{{ Storage::url($slidePath) }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                                
                                <button x-show="current > 0" @click="current--" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center transition-all z-10 backdrop-blur-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                                </button>
                                <button x-show="current < total - 1" @click="current++" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center transition-all z-10 backdrop-blur-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </div>
                            <div class="mt-4 flex justify-center gap-1.5">
                                @foreach($selectedSlides as $idx => $slidePath)
                                    <button @click="current = {{ $idx }}" :class="current === {{ $idx }} ? 'bg-copper w-6' : 'bg-gray-200 w-2'" class="h-1.5 rounded-full transition-all duration-300"></button>
                                @endforeach
                            </div>
                        </div>
                    @elseif($selectedPost->image_path)
                        <div class="w-full {{ $modalRatioClass }} rounded-2xl overflow-hidden shadow-sm border border-gray-200 bg-white">
                            <img src="{{ Storage::url($selectedPost->image_path) }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="text-gray-300 font-medium flex flex-col items-center gap-3">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Aucun visuel disponible
                        </div>
                    @endif
                </div>

                <!-- Text Side -->
                <div class="w-full md:w-1/2 flex flex-col bg-white overflow-hidden min-h-0">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center sticky top-0 bg-white/80 backdrop-blur-md z-10">
                        <h4 class="font-bold text-midnight text-lg">Aperçu du contenu</h4>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-midnight transition-colors p-2 bg-gray-50 hover:bg-gray-100 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-8 flex-1 overflow-y-auto space-y-8">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Légende Instagram</h5>
                                <button x-data="{ copied: false }"
                                        @click="
                                            navigator.clipboard.writeText({{ json_encode($selectedPost->caption ?? '') }});
                                            copied = true;
                                            setTimeout(() => copied = false, 2000);
                                        "
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all"
                                        :class="copied ? 'bg-green-50 text-green-600' : 'bg-copper/10 text-copper hover:bg-copper/20'">
                                    <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                    <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                    <span x-text="copied ? 'Copié !' : 'Copier le texte'"></span>
                                </button>
                            </div>
                            <div class="bg-gray-50/50 rounded-2xl p-6 text-[15px] text-midnight border border-gray-100 whitespace-pre-wrap leading-relaxed shadow-inner font-medium">{{ trim($selectedPost->caption ?? 'Aucune légende disponible.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Publish -->
    @if($showPublishModal)
        <div class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-midnight/60 backdrop-blur-sm" wire:click.self="closePublishModal">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col transform transition-all border border-white/20">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white sticky top-0 z-10">
                    <h4 class="font-bold text-midnight text-lg">Publier ce post</h4>
                    <button wire:click="closePublishModal" class="text-gray-400 hover:text-midnight transition-colors p-2 bg-gray-50 hover:bg-gray-100 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[70vh] space-y-6">
                    
                    {{-- ── Plateformes ── --}}
                    <div class="bg-gray-50 rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative">
                        @if(!$igConnected && !$fbConnected)
                            <div class="absolute inset-0 bg-white/60 backdrop-blur-sm z-20 flex flex-col items-center justify-center p-6 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                    <p class="text-sm font-semibold text-midnight mt-1">Connexion requise</p>
                                    <p class="text-[11px] text-gray-500 max-w-[220px] mx-auto mb-2">Pour configurer la diffusion, veuillez lier un compte social.</p>
                                    <a href="{{ route('post-settings') }}" wire:navigate class="px-3.5 py-1.5 bg-white border border-gray-200 shadow-sm hover:border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        Paramétrer
                                    </a>
                                </div>
                            </div>
                        @endif
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-4 bg-white">
                            <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s-1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-midnight text-sm">Où publier ?</h3>
                            </div>
                        </div>
                        <div class="p-6 bg-white">
                            <div class="grid grid-cols-2 gap-3">
                                {{-- Instagram --}}
                                <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 transition-all duration-200
                                    {{ (!$igConnected || in_array('instagram', $existingPlatforms)) ? 'opacity-50 grayscale cursor-not-allowed bg-gray-50 border-gray-100' : (in_array('instagram', $publishPlatforms) ? 'border-pink-400 bg-pink-50/50 cursor-pointer' : 'border-gray-100 bg-gray-50 hover:border-gray-200 cursor-pointer') }}">
                                    <input type="checkbox" wire:model.live="publishPlatforms" value="instagram" class="hidden" {{ (!$igConnected || in_array('instagram', $existingPlatforms)) ? 'disabled' : '' }} />
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                         style="background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888)">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-semibold {{ in_array('instagram', $publishPlatforms) ? 'text-midnight' : 'text-gray-600' }}">Instagram</p>
                                    </div>
                                    @if(in_array('instagram', $publishPlatforms))
                                        <div class="w-4 h-4 rounded-full bg-pink-500 flex items-center justify-center shrink-0">
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                    @else
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-200 shrink-0"></div>
                                    @endif
                                </label>

                                {{-- Facebook --}}
                                <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 transition-all duration-200
                                    {{ (!$fbConnected || in_array('facebook', $existingPlatforms)) ? 'opacity-50 grayscale cursor-not-allowed bg-gray-50 border-gray-100' : (in_array('facebook', $publishPlatforms) ? 'border-blue-400 bg-blue-50/50 cursor-pointer' : 'border-gray-100 bg-gray-50 hover:border-gray-200 cursor-pointer') }}">
                                    <input type="checkbox" wire:model.live="publishPlatforms" value="facebook" class="hidden" {{ (!$fbConnected || in_array('facebook', $existingPlatforms)) ? 'disabled' : '' }} />
                                    <div class="w-8 h-8 rounded-lg bg-[#1877F2] flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] font-semibold {{ in_array('facebook', $publishPlatforms) ? 'text-midnight' : 'text-gray-600' }}">Facebook</p>
                                    </div>
                                    @if(in_array('facebook', $publishPlatforms))
                                        <div class="w-4 h-4 rounded-full bg-blue-500 flex items-center justify-center shrink-0">
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                    @else
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-200 shrink-0"></div>
                                    @endif
                                </label>
                            </div>
                            @error('publishPlatforms') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- ── Planification ── --}}
                    <div class="bg-gray-50 rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative">
                        @if(!$igConnected && !$fbConnected)
                            <div class="absolute inset-0 bg-white/60 backdrop-blur-sm z-20 flex flex-col items-center justify-center p-6 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <p class="text-sm font-semibold text-midnight mt-1">Connexion requise</p>
                                    <p class="text-[11px] text-gray-500 max-w-[220px] mx-auto mb-2">Connectez un compte social en premier pour diffuser vos posts.</p>
                                </div>
                            </div>
                        @endif
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-4 bg-white">
                            <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-midnight text-sm">Planification</h3>
                            </div>
                        </div>

                        <div class="p-6 bg-white space-y-4">
                            {{-- Toggle now / scheduled --}}
                            <div class="flex bg-gray-100 rounded-xl p-1 gap-1">
                                <button type="button"
                                        wire:click="$set('publishScheduleType', 'now')"
                                        class="flex-1 flex items-center justify-center gap-2 py-2 px-4 rounded-lg text-sm font-medium transition-all duration-200
                                            {{ $publishScheduleType === 'now' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600' }}">
                                    Maintenant
                                </button>
                                <button type="button"
                                        wire:click="$set('publishScheduleType', 'scheduled')"
                                        class="flex-1 flex items-center justify-center gap-2 py-2 px-4 rounded-lg text-sm font-medium transition-all duration-200
                                            {{ $publishScheduleType === 'scheduled' ? 'bg-white shadow-sm text-midnight font-semibold' : 'text-gray-400 hover:text-gray-600' }}">
                                    Planifier
                                </button>
                            </div>

                            @if($publishScheduleType === 'scheduled')
                                {{-- Custom date/time picker (adapted from PostGenerator) --}}
                                <div x-data="{
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
                                            $wire.set('publishScheduledAt', fmt);
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
                                                        class="flex flex-col items-center justify-center min-w-[50px] h-[58px] rounded-xl border-2 transition-all duration-150 snap-start shrink-0 cursor-pointer">
                                                    <span class="text-[9px] font-bold uppercase tracking-wide leading-none" x-text="dayLabel(d)"></span>
                                                    <span class="text-base font-black leading-tight mt-0.5" x-text="dayNum(d)"></span>
                                                    <span class="text-[8px] leading-none opacity-70" x-text="monthShort(d)"></span>
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
                                        <p class="text-xs font-semibold text-copper" x-text="summary()"></p>
                                    </div>

                                    @error('publishScheduledAt')
                                        <p class="text-red-500 text-xs font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-100 bg-white">
                    <button wire:click="publishPost" wire:loading.attr="disabled"
                            class="w-full relative group overflow-hidden bg-copper text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-copper/25 hover:shadow-xl hover:shadow-copper/30 hover:bg-amber-500 transition-all duration-300 active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg wire:loading wire:target="publishPost" class="animate-spin w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            
                            @if($publishScheduleType === 'scheduled')
                                <span>Planifier la publication</span>
                            @else
                                <span>Publier maintenant</span>
                            @endif
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
