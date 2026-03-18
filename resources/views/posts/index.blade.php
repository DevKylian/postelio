<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Mes Créations
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    {{ $posts->count() }} {{ $posts->count() > 1 ? 'visuels générés' : 'visuel généré' }}
                </p>
            </div>
            <x-primary-button :href="route('generate-post')" wire:navigate class="!rounded-xl !gap-2 !py-2.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau post
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
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
                        @php
                            $slides     = $post->slides ?? [];
                            $isCarousel = count($slides) > 1;
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

                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-lg hover:border-gray-200 transition-all duration-200 hover:-translate-y-0.5">

                            <!-- Image / Carousel -->
                            <div class="aspect-square relative overflow-hidden bg-gray-50">

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
                                <div>
                                    <p class="text-[10px] text-gray-300 font-medium mb-0.5">
                                        {{ $post->created_at->format('d/m/Y') }}
                                    </p>
                                    <p class="text-sm font-semibold text-midnight leading-snug line-clamp-2">
                                        {{ $post->caption ?? ($templateLabels[$post->template] ?? 'Post généré') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    @if($isCarousel)
                                        <button onclick="downloadAllSlides({{ json_encode(array_map(fn($s) => Storage::url($s), $slides)) }}, '{{ addslashes($post->caption) }}')"
                                           class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-midnight text-white text-[11px] font-semibold hover:bg-midnight/80 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Tout ({{ count($slides) }})
                                        </button>
                                    @else
                                        <a href="{{ $post->image_path ? Storage::url($post->image_path) : '#' }}"
                                           {{ $post->image_path ? 'download' : '' }}
                                           class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-midnight text-white text-[11px] font-semibold hover:bg-midnight/80 transition-colors {{ !$post->image_path ? 'opacity-40 pointer-events-none' : '' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Télécharger
                                        </a>
                                    @endif

                                    <!-- Copy text -->
                                    <button x-data="{ copied: false }"
                                            @click="
                                                navigator.clipboard.writeText('{{ addslashes($post->refined_content) }}');
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
                                        <button type="submit"
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
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        async function downloadAllSlides(urls, caption) {
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
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }
    </script>
</x-app-layout>
