<div>

    {{-- ── Header navigation mois ── --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <button wire:click="previousMonth"
                    class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-500 hover:text-midnight transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <h2 class="text-lg font-bold text-midnight capitalize" wire:loading.class="opacity-50">
                {{ $monthLabel }}
            </h2>

            <button wire:click="nextMonth"
                    class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-500 hover:text-midnight transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-3">
            {{-- Légende --}}
            <div class="hidden sm:flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-copper inline-block"></span>
                    Publié
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 inline-block"></span>
                    Planifié
                </span>
            </div>
            <span class="text-xs text-gray-400 font-medium bg-white border border-gray-200 px-3 py-1.5 rounded-full shadow-sm">
                {{ $totalPosts }} post{{ $totalPosts > 1 ? 's' : '' }} ce mois
            </span>
        </div>
    </div>

    {{-- ── Grille calendrier ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Jours de la semaine --}}
        <div class="grid grid-cols-7 border-b border-gray-100">
            @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
            <div class="py-3 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider
                {{ in_array($dayName, ['Sam', 'Dim']) ? 'text-gray-300' : '' }}">
                {{ $dayName }}
            </div>
            @endforeach
        </div>

        {{-- Jours du mois --}}
        <div class="grid grid-cols-7 divide-x divide-gray-50">
            {{-- Cellules vides avant le 1er --}}
            @for($i = 0; $i < $leadingBlanks; $i++)
            <div class="min-h-[120px] bg-gray-50/50 border-b border-gray-50 p-2"></div>
            @endfor

            {{-- Jours --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $isToday   = $isCurrentMonth && $day === $today;
                $isPast    = $isCurrentMonth && $day < $today;
                $dayPosts  = $postsByDay[$day] ?? [];
                $hasPosts  = count($dayPosts) > 0;
                $overflow  = max(0, count($dayPosts) - 2);
            @endphp
            <div class="min-h-[120px] border-b border-gray-50 p-2 flex flex-col gap-1.5 transition-colors
                {{ $isPast && !$hasPosts ? 'bg-gray-50/40' : 'bg-white hover:bg-gray-50/50' }}
                {{ $isToday ? 'ring-inset ring-2 ring-copper/20' : '' }}">

                {{-- Numéro du jour --}}
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold leading-none
                        {{ $isToday
                            ? 'w-6 h-6 flex items-center justify-center bg-copper text-white rounded-full text-[11px]'
                            : ($isPast ? 'text-gray-300' : 'text-gray-500') }}">
                        {{ $day }}
                    </span>
                    @if($hasPosts)
                    <span class="text-[9px] font-bold text-gray-300">
                        {{ count($dayPosts) }}
                    </span>
                    @endif
                </div>

                {{-- Posts du jour (2 max + overflow) --}}
                @foreach(array_slice($dayPosts, 0, 2) as $post)
                <div class="group relative rounded-lg overflow-hidden flex-shrink-0
                    {{ $post['scheduled'] ? 'ring-1 ring-indigo-200' : 'ring-1 ring-copper/20' }}"
                     title="{{ $post['caption'] ?? ($post['template']) }}">
                    @if($post['thumbnail'])
                        <img src="{{ $post['thumbnail'] }}"
                             alt="Post"
                             class="w-full h-12 object-cover">
                        {{-- Overlay template badge --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        @if($post['scheduled'])
                        <div class="absolute top-1 right-1 w-3.5 h-3.5 bg-indigo-500 rounded-full flex items-center justify-center">
                            <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3" />
                            </svg>
                        </div>
                        @endif
                    @else
                        {{-- Post textuel / pas d'image --}}
                        <div class="w-full h-10 flex items-center gap-1.5 px-2
                            {{ $post['scheduled'] ? 'bg-indigo-50' : 'bg-copper/8 bg-amber-50' }}">
                            <span class="w-2 h-2 rounded-full shrink-0
                                {{ $post['scheduled'] ? 'bg-indigo-400' : 'bg-copper' }}"></span>
                            <span class="text-[10px] font-medium text-gray-600 truncate leading-tight">
                                {{ $post['caption'] ? \Illuminate\Support\Str::limit($post['caption'], 22) : ucfirst(str_replace('_', ' ', $post['template'])) }}
                            </span>
                        </div>
                    @endif
                </div>
                @endforeach

                @if($overflow > 0)
                <div class="text-[10px] font-bold text-gray-400 text-center py-0.5">
                    +{{ $overflow }} de plus
                </div>
                @endif
            </div>
            @endfor

            {{-- Cellules vides en fin de grille pour compléter la dernière ligne --}}
            @php
                $totalCells  = $leadingBlanks + $daysInMonth;
                $trailingBlanks = (7 - ($totalCells % 7)) % 7;
            @endphp
            @for($i = 0; $i < $trailingBlanks; $i++)
            <div class="min-h-[120px] bg-gray-50/50 border-b border-gray-50 p-2"></div>
            @endfor
        </div>
    </div>

    {{-- ── Message mois vide ── --}}
    @if($totalPosts === 0)
    <div class="mt-6 flex flex-col items-center justify-center py-12 text-center">
        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-300 mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <p class="text-sm font-semibold text-gray-400">Aucun post ce mois-ci</p>
        <p class="text-xs text-gray-300 mt-1">Générez ou planifiez un post pour le voir apparaître ici.</p>
        <a href="{{ route('generate-post') }}" wire:navigate
           class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-copper hover:text-amber-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Générer un post
        </a>
    </div>
    @endif

</div>
