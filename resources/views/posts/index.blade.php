<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
                    {{ __('Mes Créations') }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">Gérez et téléchargez vos visuels générés.</p>
            </div>
            <x-primary-button :href="route('generate-post')" wire:navigate class="!rounded-2xl shadow-lg shadow-copper/20">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                NOUVEAU POST
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if($posts->isEmpty())
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-16 text-center max-w-2xl mx-auto">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-midnight mb-2">Vous n'avez pas encore de créations</h3>
                    <p class="text-gray-500 mb-8">Commencez par envoyer un mémo vocal pour générer votre premier post premium.</p>
                    <x-primary-button :href="route('generate-post')" wire:navigate class="!px-8 !py-4 shadow-xl shadow-copper/20">
                        CRÉER MON PREMIER POST
                    </x-primary-button>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($posts as $post)
                        @php
                            $slides    = $post->slides ?? [];
                            $isCarousel = count($slides) > 1;
                            $templateLabels = [
                                'review'            => 'Avis Client',
                                'project'           => 'Avant / Après',
                                'faq'               => 'FAQ Artisan',
                                'lifestyle'         => 'Lifestyle',
                                'quality_focus'     => 'Focus Qualité',
                                'chantier_detail'   => 'Chantier en Détail',
                                'step_by_step'      => 'Step-by-Step',
                                'guide_pedagogique' => 'Guide Pédagogique',
                            ];
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group hover:shadow-xl transition-all duration-300 hover:-translate-y-1 transform-gpu">

                            <!-- Image / Carousel -->
                            <div class="aspect-square relative overflow-hidden bg-gray-100 rounded-t-2xl">

                                @if($isCarousel)
                                    {{-- Carousel with Alpine --}}
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
                                             x-transition:enter="transition-opacity duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             class="absolute inset-0">
                                            <img src="{{ Storage::url($slidePath) }}" alt="Slide {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        </div>
                                        @endforeach

                                        <!-- Desktop prev/next arrows -->
                                        <button x-show="current > 0"
                                                @click.stop="current--"
                                                class="hidden md:flex absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/50 hover:bg-black/75 text-white rounded-full items-center justify-center transition-colors z-10 opacity-0 group-hover:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <button x-show="current < total - 1"
                                                @click.stop="current++"
                                                class="hidden md:flex absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/50 hover:bg-black/75 text-white rounded-full items-center justify-center transition-colors z-10 opacity-0 group-hover:opacity-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>

                                        <!-- Dots -->
                                        <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1 z-10">
                                            @foreach($slides as $idx => $slidePath)
                                            <button @click.stop="current = {{ $idx }}"
                                                    :class="current === {{ $idx }} ? 'bg-white w-4' : 'bg-white/50 w-1.5'"
                                                    class="h-1.5 rounded-full transition-all duration-300"></button>
                                            @endforeach
                                        </div>

                                        <!-- Counter -->
                                        <div class="absolute top-2 right-2 bg-black/50 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10"
                                             x-text="(current + 1) + ' / ' + total"></div>
                                    </div>

                                @elseif($post->image_path)
                                    <img src="{{ Storage::url($post->image_path) }}" alt="Post" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-200 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <!-- Template badge -->
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="px-2.5 py-1 bg-white/90 backdrop-blur-sm shadow-sm rounded-full text-[10px] font-black uppercase tracking-widest text-midnight">
                                        {{ $templateLabels[$post->template] ?? 'Création' }}
                                    </span>
                                </div>

                                @if($isCarousel)
                                <div class="absolute top-3 right-3 z-10 bg-midnight/70 text-white text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full">
                                    {{ count($slides) }} slides
                                </div>
                                @endif
                            </div>

                            <!-- Infos & Actions -->
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div class="mb-4">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">
                                        Généré le {{ $post->created_at->format('d/m/Y') }}
                                    </p>
                                    <h4 class="font-bold text-midnight truncate text-sm">
                                        {{ $post->caption ?? 'Post sans titre' }}
                                    </h4>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if($isCarousel)
                                        <button onclick="downloadAllSlides({{ json_encode(array_map(fn($s) => Storage::url($s), $slides)) }}, '{{ addslashes($post->caption) }}')"
                                           class="flex-1 flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-midnight text-white text-xs font-bold hover:bg-midnight/90 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Tout télécharger
                                        </button>

                                        <a href="{{ Storage::url($slides[0]) }}" download
                                           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl border border-gray-200 text-midnight text-xs font-bold hover:bg-gray-50 transition-colors">
                                            Slide 1
                                        </a>
                                    @else
                                        <a href="{{ $post->image_path ? Storage::url($post->image_path) : '#' }}"
                                           {{ $post->image_path ? 'download' : '' }}
                                           class="flex-1 flex items-center justify-center gap-2 py-3 px-3 rounded-xl border border-gray-200 text-midnight text-xs font-bold hover:bg-gray-50 transition-colors {{ !$post->image_path ? 'opacity-40 pointer-events-none' : '' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Télécharger
                                        </a>
                                    @endif

                                    <button onclick="navigator.clipboard.writeText('{{ addslashes($post->refined_content) }}')"
                                            class="flex items-center justify-center p-3 rounded-xl border border-gray-100 text-copper hover:bg-copper hover:text-white transition-all group/copy relative">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-midnight text-white text-[10px] rounded opacity-0 group-hover/copy:opacity-100 pointer-events-none transition-opacity whitespace-nowrap">
                                            Copier le texte
                                        </div>
                                    </button>

                                    <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                          onsubmit="return confirm('Supprimer cette création ?')" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center justify-center p-3 rounded-xl border border-gray-100 text-red-500 hover:bg-red-500 hover:text-white transition-all group/delete relative">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-red-600 text-white text-[10px] rounded opacity-0 group-hover/delete:opacity-100 pointer-events-none transition-opacity">
                                                Supprimer
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <script>
        async function downloadAllSlides(urls, caption) {
            // Remove accents and replace non-alphanumeric with underscores
            const cleanCaption = caption.normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/[^a-z0-9]/gi, '_')
                .toLowerCase()
                .substring(0, 30);
            
            for (let i = 0; i < urls.length; i++) {
                const link = document.createElement('a');
                link.href = urls[i];
                link.download = `${cleanCaption}_slide_${i + 1}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Small delay to prevent browser from blocking multiple downloads
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }
    </script>
</x-app-layout>
