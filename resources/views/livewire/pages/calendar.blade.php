<div x-data="{ expandedDay: null }">

    {{-- ══════════════════════════════════════════════════════════════════════
         Header : navigation + toggle vue
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">

        {{-- Navigation gauche --}}
        <div class="flex items-center gap-3">
            {{-- Bouton précédent --}}
            @if($view === 'week')
            <button wire:click="previousWeek"
                    class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-500 hover:text-midnight transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            @else
            <button wire:click="previousMonth"
                    class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-500 hover:text-midnight transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            @endif

            <h2 class="text-lg font-bold text-midnight capitalize min-w-[180px] text-center" wire:loading.class="opacity-50">
                {{ $monthLabel }}
            </h2>

            {{-- Bouton suivant --}}
            @if($view === 'week')
            <button wire:click="nextWeek"
                    class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-500 hover:text-midnight transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            @else
            <button wire:click="nextMonth"
                    class="w-9 h-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-500 hover:text-midnight transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            @endif

            {{-- Bouton "Aujourd'hui" pour vue semaine --}}
            @if($view === 'week' && !$isCurrentWeek)
            <button wire:click="goToCurrentWeek"
                    class="hidden sm:inline-flex items-center text-xs font-semibold text-copper hover:text-amber-600 border border-copper/30 hover:border-amber-400 px-3 py-1.5 rounded-lg transition-all">
                Aujourd'hui
            </button>
            @endif
        </div>

        {{-- Droite : légende + toggle + compteur --}}
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Légende --}}
            <div class="hidden md:flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-copper inline-block"></span>
                    Publié
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                    Planifié
                </span>
            </div>

            {{-- Toggle mois / semaine --}}
            <div class="flex items-center bg-gray-100 rounded-xl p-1 gap-1">
                <button wire:click="switchView('month')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all
                            {{ $view === 'month' ? 'bg-white text-midnight shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                    Mois
                </button>
                <button wire:click="switchView('week')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all
                            {{ $view === 'week' ? 'bg-white text-midnight shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                    Semaine
                </button>
            </div>

            {{-- Compteur --}}
            <span class="text-xs text-gray-400 font-medium bg-white border border-gray-200 px-3 py-1.5 rounded-full shadow-sm">
                {{ $totalPosts }} post{{ $totalPosts > 1 ? 's' : '' }}
                {{ $view === 'week' ? 'cette semaine' : 'ce mois' }}
            </span>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════
         VUE MENSUELLE
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($view === 'month')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

        {{-- En-têtes jours --}}
        <div class="grid grid-cols-7 border-b border-gray-100 rounded-t-2xl overflow-hidden">
            @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
            <div class="py-3 text-center text-[11px] font-bold uppercase tracking-wider
                {{ in_array($dayName, ['Sam', 'Dim']) ? 'text-gray-300' : 'text-gray-400' }}">
                {{ $dayName }}
            </div>
            @endforeach
        </div>

        {{-- Grille des jours --}}
        <div class="grid grid-cols-7 divide-x divide-gray-50">

            {{-- Cellules vides avant le 1er --}}
            @for($i = 0; $i < $leadingBlanks; $i++)
            <div class="min-h-[160px] bg-gray-50/50 border-b border-gray-50 p-2"></div>
            @endfor

            {{-- Jours --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $isToday  = $isCurrentMonth && $day === $today;
                $isPast   = $isCurrentMonth && $day < $today;
                $dayPosts = $postsByDay[$day] ?? [];
                $overflow = max(0, count($dayPosts) - 3);
                $visible  = array_slice($dayPosts, 0, 3);
            @endphp

            <div class="relative min-h-[160px] border-b border-gray-50 p-2 flex flex-col gap-1.5 transition-colors
                {{ $isPast && !count($dayPosts) ? 'bg-gray-50/40' : 'bg-white hover:bg-gray-50/30' }}
                {{ $isToday ? 'ring-inset ring-2 ring-copper/20' : '' }}">

                {{-- Numéro du jour --}}
                <div class="flex items-center justify-between mb-0.5">
                    <span class="{{ $isToday
                        ? 'w-6 h-6 flex items-center justify-center bg-copper text-white rounded-full text-[11px] font-bold'
                        : ($isPast ? 'text-[12px] font-bold text-gray-300' : 'text-[12px] font-bold text-gray-500') }}">
                        {{ $day }}
                    </span>
                    @if(count($dayPosts) > 0)
                    <span class="text-[9px] font-bold text-gray-300">{{ count($dayPosts) }}</span>
                    @endif
                </div>

                {{-- Posts visibles (3 max) --}}
                @foreach($visible as $post)
                <button wire:click="openPost({{ $post['id'] }})"
                        class="group relative rounded-lg overflow-hidden flex-shrink-0 text-left w-full cursor-pointer
                            {{ $post['scheduled'] ? 'ring-1 ring-amber-200 hover:ring-amber-400' : 'ring-1 ring-copper/20 hover:ring-copper/50' }}
                            transition-all"
                        title="{{ $post['caption'] ?? $post['template'] }}">

                    @if($post['thumbnail'])
                        <img src="{{ $post['thumbnail'] }}"
                             alt="Post"
                             class="w-full h-14 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-1">
                            <span class="text-[9px] text-white font-semibold leading-tight truncate">
                                {{ $post['caption'] ? \Illuminate\Support\Str::limit($post['caption'], 30) : '' }}
                            </span>
                        </div>
                        @if($post['scheduled'])
                        <div class="absolute top-1 right-1 w-3.5 h-3.5 bg-amber-500 rounded-full flex items-center justify-center">
                            <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3" />
                            </svg>
                        </div>
                        @endif
                    @else
                        <div class="w-full flex items-center gap-1.5 px-2 py-1.5
                            {{ $post['scheduled'] ? 'bg-amber-50' : 'bg-amber-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0
                                {{ $post['scheduled'] ? 'bg-amber-400' : 'bg-copper' }}"></span>
                            <span class="text-[10px] font-medium text-gray-600 truncate leading-tight">
                                {{ $post['caption']
                                    ? \Illuminate\Support\Str::limit($post['caption'], 24)
                                    : ucfirst(str_replace('_', ' ', $post['template'])) }}
                            </span>
                        </div>
                    @endif
                </button>
                @endforeach

                {{-- Overflow : "+ N de plus" --}}
                @if($overflow > 0)
                <button
                    @click="expandedDay = (expandedDay === {{ $day }}) ? null : {{ $day }}"
                    class="text-[10px] font-bold text-amber-500 hover:text-amber-700 text-center py-0.5 w-full rounded transition-colors">
                    +{{ $overflow }} de plus
                </button>

                {{-- Popup overlay pour le jour --}}
                <div x-show="expandedDay === {{ $day }}"
                     x-cloak
                     @click.outside="expandedDay = null"
                     class="absolute z-40 bg-white border border-gray-200 shadow-xl rounded-2xl p-3 w-56 flex flex-col gap-2"
                     style="bottom: calc(100% - 2rem); left: 50%; transform: translateX(-50%); min-width: 14rem;">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-midnight">{{ $day }} – tous les posts</span>
                        <button @click="expandedDay = null" class="text-gray-300 hover:text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @foreach($dayPosts as $post)
                    <button wire:click="openPost({{ $post['id'] }})" @click="expandedDay = null"
                            class="group relative rounded-lg overflow-hidden text-left w-full
                                {{ $post['scheduled'] ? 'ring-1 ring-amber-200 hover:ring-amber-400' : 'ring-1 ring-copper/20 hover:ring-copper/50' }}
                                transition-all">
                        @if($post['thumbnail'])
                            <img src="{{ $post['thumbnail'] }}" alt="Post" class="w-full h-12 object-cover">
                        @else
                            <div class="w-full flex items-center gap-1.5 px-2 py-2
                                {{ $post['scheduled'] ? 'bg-amber-50' : 'bg-amber-50' }}">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0
                                    {{ $post['scheduled'] ? 'bg-amber-400' : 'bg-copper' }}"></span>
                                <span class="text-[10px] font-medium text-gray-600 truncate">
                                    {{ $post['caption']
                                        ? \Illuminate\Support\Str::limit($post['caption'], 28)
                                        : ucfirst(str_replace('_', ' ', $post['template'])) }}
                                </span>
                            </div>
                        @endif
                    </button>
                    @endforeach
                </div>
                @endif

            </div>
            @endfor

            {{-- Cellules fin de grille --}}
            @php
                $totalCells     = $leadingBlanks + $daysInMonth;
                $trailingBlanks = (7 - ($totalCells % 7)) % 7;
            @endphp
            @for($i = 0; $i < $trailingBlanks; $i++)
            <div class="min-h-[160px] bg-gray-50/50 border-b border-gray-50 p-2"></div>
            @endfor

        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════════════
         VUE SEMAINE
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($view === 'week' && $weekDays)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">

        {{-- En-têtes jours --}}
        <div class="grid grid-cols-7 border-b border-gray-100 rounded-t-2xl overflow-hidden">
            @foreach($weekDays as $wd)
            <div class="py-4 text-center border-r border-gray-50 last:border-r-0
                {{ $wd['isToday'] ? 'bg-copper/5' : '' }}">
                <div class="text-[11px] font-bold uppercase tracking-wider mb-1
                    {{ in_array($wd['dayName'], ['sam', 'dim', 'Sam', 'Dim']) ? 'text-gray-300' : 'text-gray-400' }}">
                    {{ ucfirst($wd['dayName']) }}
                </div>
                <div class="{{ $wd['isToday']
                    ? 'w-8 h-8 mx-auto flex items-center justify-center bg-copper text-white rounded-full text-sm font-bold'
                    : ($wd['isPast'] ? 'text-gray-300 text-sm font-bold' : 'text-gray-700 text-sm font-bold') }}">
                    {{ $wd['dayNum'] }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Corps : colonnes jours --}}
        <div class="grid grid-cols-7 divide-x divide-gray-50 min-h-[500px]">
            @foreach($weekDays as $wd)
            <div class="p-2 flex flex-col gap-2
                {{ $wd['isToday'] ? 'bg-copper/5' : ($wd['isPast'] && !count($wd['posts']) ? 'bg-gray-50/40' : 'bg-white') }}">

                @if(count($wd['posts']) === 0)
                    <div class="flex-1 flex items-center justify-center">
                        <span class="text-[10px] text-gray-200 font-medium">—</span>
                    </div>
                @endif

                @foreach($wd['posts'] as $post)
                <button wire:click="openPost({{ $post['id'] }})"
                        class="group relative rounded-xl overflow-hidden text-left w-full cursor-pointer flex-shrink-0
                            {{ $post['scheduled'] ? 'ring-1 ring-amber-200 hover:ring-amber-400' : 'ring-1 ring-copper/20 hover:ring-copper/50' }}
                            shadow-sm hover:shadow-md transition-all"
                        title="{{ $post['caption'] ?? $post['template'] }}">

                    @if($post['thumbnail'])
                        <img src="{{ $post['thumbnail'] }}" alt="Post"
                             class="w-full h-24 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    @endif

                    <div class="px-2 py-2 {{ $post['thumbnail'] ? 'absolute bottom-0 left-0 right-0 text-white' : ($post['scheduled'] ? 'bg-amber-50' : 'bg-amber-50') }}">
                        <div class="flex items-center gap-1 mb-0.5">
                            <span class="w-1.5 h-1.5 rounded-full shrink-0
                                {{ $post['scheduled'] ? 'bg-amber-400' : 'bg-copper' }}
                                {{ $post['thumbnail'] ? 'opacity-0' : '' }}"></span>
                            <span class="text-[10px] font-bold {{ $post['thumbnail'] ? 'text-white' : ($post['scheduled'] ? 'text-amber-600' : 'text-amber-600') }}">
                                {{ $post['scheduled'] ? 'Planifié' : 'Publié' }}
                                @if($post['time'] !== '00:00')
                                    · {{ $post['time'] }}
                                @endif
                            </span>
                        </div>
                        @if($post['caption'])
                        <p class="text-[11px] leading-tight truncate {{ $post['thumbnail'] ? 'text-white/90' : 'text-gray-600' }}">
                            {{ \Illuminate\Support\Str::limit($post['caption'], 50) }}
                        </p>
                        @else
                        <p class="text-[11px] leading-tight truncate {{ $post['thumbnail'] ? 'text-white/90' : 'text-gray-500' }}">
                            {{ ucfirst(str_replace('_', ' ', $post['template'])) }}
                        </p>
                        @endif
                    </div>
                </button>
                @endforeach

            </div>
            @endforeach
        </div>
    </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════════════════
         Message mois / semaine vide
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($totalPosts === 0)
    <div class="mt-6 flex flex-col items-center justify-center py-12 text-center">
        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-300 mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <p class="text-sm font-semibold text-gray-400">Aucun post {{ $view === 'week' ? 'cette semaine' : 'ce mois-ci' }}</p>
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


    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL DÉTAIL DU POST
    ══════════════════════════════════════════════════════════════════════════ --}}
    @if($selectedPost)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data
         x-init="$el.querySelector('[data-modal]').classList.remove('scale-95','opacity-0')">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
             wire:click="closePost"></div>

        {{-- Panneau --}}
        <div data-modal
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden
                    transform scale-95 opacity-0 transition-all duration-200">

            {{-- Image en-tête (si disponible) --}}
            @if($selectedPost['thumbnail'])
            <div class="relative">
                <img src="{{ $selectedPost['thumbnail'] }}"
                     alt="Post"
                     class="w-full h-48 object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/30"></div>
                {{-- Badge statut --}}
                <div class="absolute top-3 left-3">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                        {{ $selectedPost['scheduled'] ? 'bg-amber-500 text-white' : 'bg-copper text-white' }}">
                        {{ $selectedPost['scheduled'] ? 'Planifié' : 'Publié' }}
                    </span>
                </div>
                {{-- Bouton fermer --}}
                <button wire:click="closePost"
                        class="absolute top-3 right-3 w-8 h-8 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @else
            {{-- Header sans image --}}
            <div class="flex items-center justify-between px-5 pt-5 pb-2">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                    {{ $selectedPost['scheduled'] ? 'bg-amber-100 text-amber-700' : 'bg-amber-100 text-amber-600' }}">
                    {{ $selectedPost['scheduled'] ? 'Planifié' : 'Publié' }}
                </span>
                <button wire:click="closePost"
                        class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            @endif

            {{-- Corps --}}
            <div class="px-5 py-4 flex flex-col gap-4 overflow-y-auto max-h-[60vh]">

                {{-- Template --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-1">Template</p>
                    <p class="text-sm font-semibold text-midnight">
                        {{ ucfirst(str_replace('_', ' ', $selectedPost['template'])) }}
                    </p>
                </div>

                {{-- Légende / caption --}}
                @if($selectedPost['caption'])
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-1">Légende</p>
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-4">
                        {{ $selectedPost['caption'] }}
                    </p>
                </div>
                @endif

                {{-- Date actuelle --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-1">
                        {{ $selectedPost['scheduled'] ? 'Planifié le' : 'Publié le' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ $selectedPost['scheduled'] ? $selectedPost['scheduled_at'] : $selectedPost['created_at'] }}
                    </p>
                </div>

                {{-- Séparateur --}}
                <div class="border-t border-gray-100"></div>

                {{-- Replanifier --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-2">
                        Modifier la date de publication
                    </p>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <input type="date"
                                   wire:model="newScheduledDate"
                                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper/50 transition-all">
                        </div>
                        <div class="w-24">
                            <input type="time"
                                   wire:model="newScheduledTime"
                                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper/50 transition-all">
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer : actions --}}
            <div class="px-5 pb-5 pt-3 flex items-center gap-3 border-t border-gray-100">
                <button wire:click="reschedulePost"
                        class="flex-1 bg-copper hover:bg-amber-500 text-white font-semibold text-sm rounded-xl px-4 py-2.5 transition-colors flex items-center justify-center gap-2"
                        wire:loading.attr="disabled"
                        wire:target="reschedulePost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span wire:loading.remove wire:target="reschedulePost">Enregistrer</span>
                    <span wire:loading wire:target="reschedulePost">Enregistrement…</span>
                </button>
                <button wire:click="closePost"
                        class="px-4 py-2.5 text-sm font-semibold text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 rounded-xl transition-colors">
                    Annuler
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
