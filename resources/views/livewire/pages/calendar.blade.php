{{--
    Calendrier Postelio — v3 "Chip-style"
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    • Chips compacts à hauteur fixe (Google Cal style)
    • Max N chips par colonne + "voir plus" en popover
    • Thumbnails uniquement dans la modal
    • Drag & drop HTML5 natif
    • Vue semaine par défaut
--}}
<div
    x-data="{
        draggingPostId: null,
        draggingOver: null,
        openPopover: null,
        popoverAnchor: { top: 0, left: 0, flip: false },
        toastMsg: null,
        toastTimer: null,

        dragStart(postId) { this.draggingPostId = postId; },
        dragEnd()         { this.draggingPostId = null; this.draggingOver = null; },
        dragOver(date)    { this.draggingOver = date; },
        drop(date) {
            if (this.draggingPostId !== null) {
                $wire.movePost(this.draggingPostId, date);
            }
            this.draggingPostId = null;
            this.draggingOver   = null;
        },
        showToast(msg) {
            if (!msg || msg.trim?.() === '') return;
            this.toastMsg = msg;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => this.toastMsg = null, 3500);
        },
        openOverflow(key, event) {
            if (this.openPopover === key) { this.openPopover = null; return; }
            const rect = event.currentTarget.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            this.popoverAnchor = {
                top:  spaceBelow > 240 ? rect.bottom + 6 : rect.top - 6,
                left: rect.left + rect.width / 2,
                flip: spaceBelow <= 240,
            };
            this.openPopover = key;
        },
        initChoice() {
            const saved = localStorage.getItem('postelio_cal_view');
            if (saved && saved !== '{{ $view }}') {
                $wire.switchView(saved);
            }
        },
        setChoice(v) {
            localStorage.setItem('postelio_cal_view', v);
            $wire.switchView(v);
        },
        activeTabDay: '{{ now()->format('Y-m-d') }}',
    }"
    x-init="initChoice()"
    @click.window="openPopover = null"
    @calendar-move-blocked.window="if($event.detail.message) showToast($event.detail.message)"
    class="relative"
>

    {{-- Toast notification --}}
    <div
        x-show="toastMsg"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[300] bg-midnight text-white px-4 py-2 rounded-full text-xs font-bold shadow-2xl flex items-center gap-2"
    >
        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span x-text="toastMsg"></span>
    </div>

    {{-- ══════════ HEADER ══════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">

        {{-- Navigation --}}
        <div class="flex items-center gap-2">
            <button
                @if($view === 'week') wire:click="previousWeek" @else wire:click="previousMonth" @endif
                class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:border-copper/50 hover:text-copper flex items-center justify-center text-gray-400 transition-all shadow-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <h2 class="text-base font-bold text-midnight capitalize min-w-[190px] text-center" wire:loading.class="opacity-40">
                {{ $monthLabel }}
            </h2>

            <button
                @if($view === 'week') wire:click="nextWeek" @else wire:click="nextMonth" @endif
                class="w-8 h-8 rounded-lg bg-white border border-gray-200 hover:border-copper/50 hover:text-copper flex items-center justify-center text-gray-400 transition-all shadow-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            @if($view === 'week' && !$isCurrentWeek)
            <button wire:click="goToCurrentWeek"
                    class="hidden sm:inline-flex items-center gap-1 text-xs font-semibold text-copper border border-copper/30 hover:bg-copper/5 px-2.5 py-1.5 rounded-lg transition-all">
                Auj.
            </button>
            @endif
        </div>

        {{-- Controls droite --}}
        <div class="flex items-center gap-3">
            {{-- Légende --}}
            <div class="hidden md:flex items-center gap-3 text-[11px] text-gray-400 font-medium">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-copper inline-block"></span>Publié</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-amber-400 inline-block"></span>Planifié</span>
            </div>

            {{-- Toggle --}}
            <div class="flex items-center bg-gray-100 rounded-xl p-1 gap-0.5">
                <button @click="setChoice('week')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $view === 'week' ? 'bg-white text-midnight shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                    Semaine
                </button>
                <button @click="setChoice('month')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $view === 'month' ? 'bg-white text-midnight shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                    Mois
                </button>
            </div>

            {{-- Compteur --}}
            @if($totalPosts > 0)
            <span class="text-xs text-gray-400 bg-white border border-gray-200 px-2.5 py-1.5 rounded-full shadow-sm font-medium">
                {{ $totalPosts }} post{{ $totalPosts > 1 ? 's' : '' }}
            </span>
            @endif
        </div>
    </div>


    {{-- ══════════ CALENDAR GRID (Adaptive) ══════════ --}}
    <div class="w-full">
        @if($view === 'week' && $weekDays)
        
        {{-- [MOBILE] Day Selector Slider --}}
        <div class="md:hidden mb-6">
            <div class="flex overflow-x-auto pb-2 gap-3 no-scrollbar snap-x">
                @foreach($weekDays as $wd)
                @php $dStr = $wd['date']->format('Y-m-d'); @endphp
                <button 
                    @click="activeTabDay = '{{ $dStr }}'"
                    class="flex-shrink-0 w-14 flex flex-col items-center gap-1.5 snap-start transition-all"
                >
                    <span class="text-[10px] font-bold uppercase tracking-widest transition-colors"
                          :class="activeTabDay === '{{ $dStr }}' ? 'text-midnight' : 'text-gray-300'">
                        {{ substr($wd['dayName'], 0, 3) }}
                    </span>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-sm font-bold transition-all border-2"
                         :class="activeTabDay === '{{ $dStr }}' 
                            ? 'bg-midnight text-white border-midnight shadow-lg scale-110' 
                            : ({{ $wd['isToday'] ? 'true' : 'false' }} ? 'bg-white text-copper border-copper/30' : 'bg-white text-gray-400 border-gray-100')">
                        {{ $wd['dayNum'] }}
                    </div>
                    @if($wd['isToday'])
                        <div class="w-1 h-1 rounded-full bg-copper mt-0.5"></div>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- [MOBILE] Agenda List for Active Day --}}
        <div class="md:hidden space-y-4">
            @foreach($weekDays as $wd)
            @php $dString = $wd['date']->format('Y-m-d'); @endphp
            <div x-show="activeTabDay === '{{ $dString }}'" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-3"
            >
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-extrabold text-midnight">
                        {{ $wd['date']->locale('fr')->isoFormat('dddd D MMMM') }}
                    </h3>
                    @if(!$wd['isPast'])
                    <button wire:click="createPostForDate('{{ $dString }}')"
                            class="flex items-center gap-1.5 text-xs font-bold text-copper bg-copper/5 px-3 py-1.5 rounded-xl">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Nouveau
                    </button>
                    @endif
                </div>

                @forelse($wd['posts'] as $post)
                @php
                    $isS = $post['scheduled'];
                    $t   = $post['time'] !== '00:00' ? $post['time'] : null;
                @endphp
                <div wire:click="openPost({{ $post['id'] }})"
                     class="group relative flex items-center gap-4 bg-white border border-gray-100 rounded-2xl p-4 shadow-sm active:scale-[0.98] transition-all">
                    @if($post['thumbnail'])
                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 shadow-inner bg-gray-50">
                        <img src="{{ $post['thumbnail'] }}" alt="" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="w-16 h-16 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100">
                        <span class="text-xl">📄</span>
                    </div>
                    @endif
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            @php
                                $dotColor = 'bg-copper';
                                if ($post['status'] === 'published') $dotColor = 'bg-emerald-500';
                                elseif ($isS) $dotColor = 'bg-amber-400';
                            @endphp
                            <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-300">
                                {{ ucfirst(str_replace('_', ' ', $post['template'])) }}
                            </span>
                        </div>
                        <p class="text-sm font-bold text-midnight truncate mb-1">
                            {{ $post['caption'] ? \Illuminate\Support\Str::limit($post['caption'], 45) : 'Sans légende' }}
                        </p>
                        @if($t)
                        <div class="flex items-center gap-1.5 text-[11px] font-bold {{ $isS ? 'text-amber-500' : 'text-copper' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $t }}
                        </div>
                        @else
                         <div class="text-[11px] font-bold text-gray-400">Pas d'heure définie</div>
                        @endif
                    </div>
                    
                    <div class="text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
                @empty
                <div class="py-12 flex flex-col items-center justify-center text-center opacity-40">
                    <div class="w-12 h-12 mb-3 bg-gray-50 rounded-2xl flex items-center justify-center">🏝️</div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Journée libre</p>
                </div>
                @endforelse
            </div>
            @endforeach
        </div>

        {{-- [DESKTOP] Traditional Grid --}}
        <div class="hidden md:block glass-panel rounded-2xl overflow-hidden">

        {{-- En-têtes colonnes --}}
        <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50/60">
            @foreach($weekDays as $wd)
            <div class="py-3 px-2 text-center {{ $wd['isToday'] ? 'relative' : '' }}">
                <p class="text-[10px] font-bold uppercase tracking-widest
                    {{ in_array(strtolower($wd['dayName']), ['sam', 'dim']) ? 'text-gray-300' : 'text-gray-400' }}">
                    {{ ucfirst($wd['dayName']) }}
                </p>
                <p class="mt-1 text-sm font-bold mx-auto w-8 h-8 flex items-center justify-center rounded-full
                    {{ $wd['isToday'] ? 'bg-copper text-white shadow-sm' : ($wd['isPast'] ? 'text-gray-300' : 'text-gray-700') }}">
                    {{ $wd['dayNum'] }}
                </p>
                @if($wd['isToday'])
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-8 h-0.5 bg-copper rounded-full"></span>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Corps — hauteur fixe, PAS de scroll --}}
        <div class="grid grid-cols-7 divide-x divide-gray-100" style="min-height: 420px;">
            @foreach($weekDays as $wd)
            @php
                $colDate     = $wd['date']->format('Y-m-d');
                $maxVisible  = 6;   // chips max affichés
                $allPosts    = $wd['posts'];
                $visible     = array_slice($allPosts, 0, $maxVisible);
                $overflowQty = count($allPosts) - count($visible);
                $colKey      = 'col-' . $colDate;
            @endphp
            <div
                class="relative flex flex-col p-2 gap-1 transition-colors
                    {{ $wd['isToday'] ? 'bg-copper/[0.025]' : ($wd['isPast'] && count($allPosts) === 0 ? 'bg-gray-50/40' : 'bg-white') }}"
                @dragover.prevent="dragOver('{{ $colDate }}')"
                @dragleave="if(!$el.contains($event.relatedTarget)) draggingOver = null"
                @drop.prevent="drop('{{ $colDate }}')"
                :class="draggingOver === '{{ $colDate }}' ? '!bg-copper/10 ring-inset ring-2 ring-copper/30' : ''"
            >
                {{-- Chips --}}
                @foreach($visible as $post)
                @php
                    $isScheduled = $post['scheduled'];
                    $showTime    = $post['time'] !== '00:00';
                    $label       = $post['caption']
                        ? \Illuminate\Support\Str::limit($post['caption'], 28)
                        : ucfirst(str_replace('_', ' ', $post['template']));
                @endphp
                <div
                    @if($post['status'] !== 'published') draggable="true" @endif
                    @dragstart="dragStart({{ $post['id'] }})"
                    @dragend="dragEnd()"
                    wire:key="chip-{{ $post['id'] }}"
                    class="group flex items-center gap-1.5 px-2 py-1.5 rounded-lg select-none transition-all flex-shrink-0
                        {{ $post['status'] !== 'published' ? 'cursor-grab active:cursor-grabbing' : 'cursor-default' }}
                        @if($post['status'] === 'published')
                            bg-emerald-50 border border-emerald-100
                        @elseif($isScheduled)
                            bg-amber-50 hover:bg-amber-100 border border-amber-200
                        @else
                            bg-copper/8 hover:bg-copper/15 border border-copper/20
                        @endif
                        hover:shadow-sm"
                    :class="draggingPostId === {{ $post['id'] }} ? 'opacity-40 scale-95' : 'opacity-100'"
                >
                    {{-- Bouton de clic vers modal --}}
                    <button wire:click="openPost({{ $post['id'] }})" class="flex items-center gap-1.5 flex-1 text-left min-w-0" title="{{ $post['caption'] ?? $post['template'] }}">
                        @php
                            $bulletColor = 'bg-copper';
                            $timeColor   = 'text-copper';
                            if ($post['status'] === 'published') { $bulletColor = 'bg-emerald-500'; $timeColor = 'text-emerald-600'; }
                            elseif ($isScheduled) { $bulletColor = 'bg-amber-400'; $timeColor = 'text-amber-600'; }
                        @endphp
                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $bulletColor }}"></span>
                        <span class="flex-1 min-w-0 flex items-center gap-1 leading-none">
                            @if($showTime)
                                <span class="text-[10px] font-bold {{ $timeColor }} shrink-0">{{ $post['time'] }}</span>
                                <span class="text-[10px] text-gray-400 shrink-0">·</span>
                            @endif
                            <span class="text-[10px] font-medium text-gray-600 truncate">{{ $label }}</span>
                        </span>
                    </button>

                    {{-- Drag "gripper" --}}
                    @if($post['status'] !== 'published')
                    <span class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity text-gray-300 cursor-grab">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="7" cy="6" r="1.2"/><circle cx="13" cy="6" r="1.2"/>
                            <circle cx="7" cy="10" r="1.2"/><circle cx="13" cy="10" r="1.2"/>
                            <circle cx="7" cy="14" r="1.2"/><circle cx="13" cy="14" r="1.2"/>
                        </svg>
                    </span>
                    @endif
                </div>
                @endforeach

                {{-- Bouton "voir plus" (vue semaine) --}}
                @if($overflowQty > 0)
                <button
                    @click.stop="openOverflow('{{ $colKey }}', $event)"
                    class="w-full text-center text-[10px] font-bold text-amber-500 hover:text-amber-700 py-1 rounded transition-colors"
                >
                    +{{ $overflowQty }} de plus
                </button>
                @endif


                {{-- Drop zone indicator --}}
                <div
                    class="flex-1 flex items-end justify-center pb-2"
                    :class="draggingOver === '{{ $colDate }}' ? 'opacity-100' : 'opacity-0'"
                >
                    <span class="text-[10px] text-copper/60 font-medium">Déposer ici</span>
                </div>

                {{-- Bouton + pour créer un post ce jour --}}
                @if(!$wd['isPast'])
                <button
                    wire:click="createPostForDate('{{ $colDate }}')"
                    x-show="draggingPostId === null"
                    class="w-full flex items-center justify-center gap-1 py-1 rounded-lg text-gray-200 hover:text-copper hover:bg-copper/5 transition-all opacity-0 group-hover:opacity-100 mt-auto"
                    title="Créer un post"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="text-[10px] font-semibold">Ajouter</span>
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif


    {{-- ══════════ VUE MOIS ══════════ --}}
    @if($view === 'month')
    <div class="glass-panel rounded-2xl overflow-hidden">

        {{-- En-têtes --}}
        <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50/60">
            @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $dayName)
            <div class="py-3 text-center text-[10px] font-bold uppercase tracking-widest
                {{ in_array($dayName, ['Sam', 'Dim']) ? 'text-gray-300' : 'text-gray-400' }}">
                {{ $dayName }}
            </div>
            @endforeach
        </div>

        {{-- Grille --}}
        <div class="grid grid-cols-7 divide-x divide-gray-100">
            @for($i = 0; $i < $leadingBlanks; $i++)
            <div class="min-h-[110px] bg-gray-50/30 border-b border-gray-100 p-2"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $isToday   = $isCurrentMonth && $day === $today;
                $isPast    = $isCurrentMonth && $day < $today;
                $dayPosts  = $postsByDay[$day] ?? [];
                $maxVis    = 3;
                $visChips  = array_slice($dayPosts, 0, $maxVis);
                $overflow  = max(0, count($dayPosts) - $maxVis);
                $mDate     = \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                $mKey      = 'month-' . $mDate;
            @endphp

                <div
                    class="relative min-h-[50px] md:min-h-[110px] border-b border-gray-100 p-1.5 flex flex-col items-center md:items-start gap-1 transition-colors group
                        {{ $isPast && !count($dayPosts) ? 'bg-gray-50/40' : ($isToday ? 'bg-copper/[0.025]' : 'bg-white hover:bg-gray-50/20') }}"
                    @click="activeTabDay = '{{ $mDate }}'"
                    @dragover.prevent="dragOver('{{ $mDate }}')"
                    @dragleave="if(!$el.contains($event.relatedTarget)) draggingOver = null"
                    @drop.prevent="drop('{{ $mDate }}')"
                    :class="draggingOver === '{{ $mDate }}' ? '!bg-copper/10 ring-inset ring-2 ring-copper/30' : (activeTabDay === '{{ $mDate }}' ? 'bg-amber-50/50 ring-1 ring-amber-200' : '')"
                >
                    {{-- Numéro jour --}}
                    <div class="flex items-center justify-between w-full mb-0.5">
                        <span class="{{ $isToday
                            ? 'w-5 h-5 flex items-center justify-center bg-copper text-white rounded-full text-[10px] font-bold'
                            : ($isPast ? 'text-[11px] font-bold text-gray-300' : 'text-[11px] font-bold text-gray-500') }}">
                            {{ $day }}
                        </span>
                        @if(count($dayPosts) > 0)
                        <span class="hidden md:inline text-[9px] font-bold text-gray-300">{{ count($dayPosts) }}</span>
                        @endif
                    </div>

                    {{-- Mobile Indicator dot --}}
                    @if(count($dayPosts) > 0)
                        <div class="md:hidden flex gap-0.5">
                            @foreach(array_slice($dayPosts, 0, 3) as $p)
                                <div class="w-1 h-1 rounded-full {{ $p['scheduled'] ? 'bg-amber-400' : 'bg-copper' }}"></div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Chips (DESKTOP ONLY) --}}
                    <div class="hidden md:flex flex-col gap-1 w-full">
                        @foreach($visChips as $post)
                        @php
                            $isSc  = $post['scheduled'];
                            $chipL = $post['caption']
                                ? \Illuminate\Support\Str::limit($post['caption'], 18)
                                : ucfirst(str_replace('_', ' ', $post['template']));
                        @endphp
                        <button
                            @if($post['status'] !== 'published') draggable="true" @endif
                            @dragstart="dragStart({{ $post['id'] }})"
                            @dragend="dragEnd()"
                            wire:click.stop="openPost({{ $post['id'] }})"
                    class="flex items-center gap-1 px-1.5 py-1 rounded-md w-full text-left transition-all select-none
                        {{ $post['status'] !== 'published' ? 'cursor-pointer' : 'cursor-default' }}
                        @if($post['status'] === 'published')
                            bg-emerald-50 border border-emerald-100
                        @elseif($isSc)
                            bg-amber-50 border border-amber-200 hover:bg-amber-100
                        @else
                            bg-copper/8 border border-copper/20 hover:bg-copper/15
                        @endif"
                    :class="draggingPostId === {{ $post['id'] }} ? 'opacity-40' : ''"
                >
                    @php
                        $mBullet = 'bg-copper';
                        if ($post['status'] === 'published') $mBullet = 'bg-emerald-500';
                        elseif ($isSc) $mBullet = 'bg-amber-400';
                    @endphp
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $mBullet }}"></span>
                    <span class="text-[10px] font-medium text-gray-600 truncate">{{ $chipL }}</span>
                </button>
                @endforeach

                        {{-- Bouton "voir plus" (vue mois) --}}
                        @if($overflow > 0)
                        <button
                            @click.stop="openOverflow('{{ $mKey }}', $event)"
                            class="text-[10px] font-bold text-amber-500 hover:text-amber-700 px-1 transition-colors"
                        >
                            +{{ $overflow }} de plus
                        </button>
                        @endif


                        {{-- Bouton + --}}
                        @if(!$isPast)
                        <button
                            wire:click="createPostForDate('{{ $mDate }}')"
                            x-show="draggingPostId === null"
                            class="mt-auto w-full flex items-center justify-center py-0.5 rounded text-gray-200 hover:text-copper hover:bg-copper/5 transition-all opacity-0 group-hover:opacity-100"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
            @endfor

            @php
                $totalCells     = $leadingBlanks + $daysInMonth;
                $trailingBlanks = (7 - ($totalCells % 7)) % 7;
            @endphp
            @for($i = 0; $i < $trailingBlanks; $i++)
            <div class="min-h-[50px] md:min-h-[110px] bg-gray-50/30 border-b border-gray-100 p-2 text-center text-xs font-bold text-gray-200">·</div>
            @endfor
        </div>
    </div>

    {{-- [MOBILE] Integrated Agenda for Month selection --}}
    <div class="md:hidden mt-8 space-y-4">
        @for($d = 1; $d <= $daysInMonth; $d++)
        @php
            $mD = \Carbon\Carbon::create($year, $month, $d);
            $mfStr = $mD->format('Y-m-d');
            $dPosts = $postsByDay[$d] ?? [];
        @endphp
        <div x-show="activeTabDay === '{{ $mfStr }}'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-3"
        >
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-extrabold text-midnight">
                    {{ $mD->locale('fr')->isoFormat('dddd D MMMM') }}
                </h3>
                @if(!$mD->lt(now()->startOfDay()))
                <button wire:click="createPostForDate('{{ $mfStr }}')"
                        class="flex items-center gap-1.5 text-xs font-bold text-copper bg-copper/5 px-3 py-1.5 rounded-xl">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Nouveau
                </button>
                @endif
            </div>

            @forelse($dPosts as $post)
            @php
                $isS = $post['scheduled'];
                $t   = $post['time'] !== '00:00' ? $post['time'] : null;
            @endphp
            <div wire:click="openPost({{ $post['id'] }})"
                 class="group relative flex items-center gap-4 bg-white border border-gray-100 rounded-2xl p-4 shadow-sm active:scale-[0.98] transition-all">
                @if($post['thumbnail'])
                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 shadow-inner bg-gray-50">
                        <img src="{{ $post['thumbnail'] }}" alt="" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-16 h-16 rounded-xl bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100">
                        <span class="text-xl">📄</span>
                    </div>
                @endif
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full {{ $isS ? 'bg-amber-400' : 'bg-copper' }}"></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-300">
                            {{ ucfirst(str_replace('_', ' ', $post['template'])) }}
                        </span>
                    </div>
                    <p class="text-sm font-bold text-midnight truncate mb-1">
                        {{ $post['caption'] ? \Illuminate\Support\Str::limit($post['caption'], 45) : 'Sans légende' }}
                    </p>
                    @if($t)
                    <div class="flex items-center gap-1.5 text-[11px] font-bold {{ $isS ? 'text-amber-500' : 'text-copper' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $t }}
                    </div>
                    @endif
                </div>
                
                <div class="text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
            @empty
            <div class="py-12 flex flex-col items-center justify-center text-center opacity-40">
                <div class="w-12 h-12 mb-3 bg-gray-50 rounded-2xl flex items-center justify-center">🏝️</div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Journée libre</p>
            </div>
            @endforelse
        </div>
        @endfor
    </div>
    @endif
</div>


    {{-- ══════════ ÉTAT VIDE ══════════ --}}
    @if($totalPosts === 0)
    <div class="mt-8 flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-200 mb-4 shadow-inner">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-gray-400">Aucun post {{ $view === 'week' ? 'cette semaine' : 'ce mois-ci' }}</p>
        <p class="text-xs text-gray-300 mt-1">Génère ou planifie un post pour le voir apparaître ici.</p>
        <a href="{{ route('generate-post') }}" wire:navigate
           class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-copper hover:text-amber-600 transition-colors bg-copper/5 hover:bg-copper/10 px-4 py-2.5 rounded-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Générer un post
        </a>
    </div>
    @endif


    {{-- ══════════ MODAL DÉTAIL ══════════ --}}
    @if($selectedPost)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
         wire:key="modal-container-{{ $selectedPostId }}"
         x-data="{ show: false }"
         x-init="setTimeout(() => show = true, 10)">

        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" 
             wire:click="closePost"
             wire:key="modal-backdrop-{{ $selectedPostId }}"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
        </div>

        <div data-modal
             wire:key="modal-content-{{ $selectedPostId }}"
             x-show="show"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="translate-y-4 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             class="relative bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transition-all duration-250">

            {{-- Thumbnail compact --}}
            @if($selectedPost['thumbnail'])
            <div class="relative h-44 flex-shrink-0">
                <img src="{{ $selectedPost['thumbnail'] }}" alt="" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/50"></div>
                <div class="absolute top-3 left-3">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-sm
                        {{ $selectedPost['scheduled'] ? 'bg-amber-500/90 text-white' : 'bg-copper/90 text-white' }}">
                        {{ $selectedPost['scheduled'] ? '⏰ Planifié' : '✓ Publié' }}
                    </span>
                </div>
                <button wire:click="closePost"
                        class="absolute top-3 right-3 w-7 h-7 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @else
            <div class="flex items-center justify-between px-5 pt-4 pb-2">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                    {{ $selectedPost['scheduled'] ? 'bg-amber-100 text-amber-700' : 'bg-copper/10 text-copper' }}">
                    {{ $selectedPost['scheduled'] ? '⏰ Planifié' : '✓ Publié' }}
                </span>
                <button wire:click="closePost"
                        class="w-7 h-7 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endif

            {{-- Corps --}}
            <div class="px-5 py-4 flex flex-col gap-4 overflow-y-auto" style="max-height: 60vh;">

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-0.5">Type</p>
                        <p class="text-sm font-semibold text-midnight">{{ ucfirst(str_replace('_', ' ', $selectedPost['template'])) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-0.5">
                            {{ $selectedPost['scheduled'] ? 'Planifié le' : 'Créé le' }}
                        </p>
                        <p class="text-xs text-gray-600 font-medium">
                            {{ $selectedPost['scheduled'] ? $selectedPost['scheduled_at'] : $selectedPost['created_at'] }}
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-2">Plateformes</p>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $platforms = $selectedPost['platforms'] ?? [];
                            $hasIg = in_array('instagram', $platforms);
                            $hasFb = in_array('facebook', $platforms);

                            $igConnected = $selectedPost['ig_connected'] ?? false;
                            $fbConnected = $selectedPost['fb_connected'] ?? false;
                        @endphp
                        
                        {{-- Instagram Toggle --}}
                        <button 
                            wire:click="togglePlatform('instagram')"
                            wire:loading.attr="disabled"
                            @if(!$igConnected) disabled @endif
                            title="{{ !$igConnected ? 'Instagram non connecté dans les réglages' : ($hasIg ? 'Désactiver Instagram' : 'Activer Instagram') }}"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl border transition-all
                                {{ !$igConnected ? 'opacity-40 cursor-not-allowed bg-gray-50 border-gray-100 text-gray-400' : 
                                   ($hasIg ? 'bg-pink-50 border-pink-100 text-pink-700 shadow-sm' : 'bg-white border-gray-200 text-gray-400 hover:border-pink-200 hover:text-pink-400') }}"
                        >
                            <svg class="w-4 h-4 {{ $hasIg ? 'animate-pulse-slow' : '' }}" fill="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="togglePlatform('instagram')">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" wire:loading wire:target="togglePlatform('instagram')">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-semibold">Instagram</span>
                        </button>

                        {{-- Facebook Toggle --}}
                        <button 
                            wire:click="togglePlatform('facebook')"
                            wire:loading.attr="disabled"
                            @if(!$fbConnected) disabled @endif
                            title="{{ !$fbConnected ? 'Facebook non connecté dans les réglages' : ($hasFb ? 'Désactiver Facebook' : 'Activer Facebook') }}"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl border transition-all
                                {{ !$fbConnected ? 'opacity-40 cursor-not-allowed bg-gray-50 border-gray-100 text-gray-400' : 
                                   ($hasFb ? 'bg-blue-50 border-blue-100 text-blue-700 shadow-sm' : 'bg-white border-gray-200 text-gray-400 hover:border-blue-200 hover:text-blue-400') }}"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="togglePlatform('facebook')">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" wire:loading wire:target="togglePlatform('facebook')">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-semibold">Facebook</span>
                        </button>
                    </div>
                </div>

                @if($selectedPost['caption'])
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-1">Légende</p>
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-4">{{ $selectedPost['caption'] }}</p>
                </div>
                @endif

                <div class="border-t border-gray-100"></div>

                {{-- Replanifier --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-300 mb-2">Modifier la date</p>
                    <div class="flex gap-2">
                        <input type="date" wire:model="newScheduledDate"
                               class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper/50 font-medium"
                               {{ $selectedPost['status'] === 'published' ? '' : 'min="' . now()->format('Y-m-d') . '"' }}>
                        <input type="time" wire:model="newScheduledTime"
                               class="w-24 text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper/50 font-medium">
                    </div>

                    {{-- Erreur date passée --}}
                    @if($rescheduleError)
                    <div class="mt-2 flex items-start gap-2 bg-red-50 border border-red-200 rounded-xl px-3 py-2.5">
                        <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-red-600 leading-snug">{{ $rescheduleError }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 pb-5 pt-3 flex gap-3 border-t border-gray-100">
                <button wire:click="reschedulePost"
                        class="flex-1 bg-copper hover:bg-amber-500 text-white font-semibold text-sm rounded-xl px-4 py-2.5 transition-colors flex items-center justify-center gap-2 shadow-sm"
                        wire:loading.attr="disabled" wire:target="reschedulePost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span wire:loading.remove wire:target="reschedulePost">Enregistrer</span>
                    <span wire:loading wire:target="reschedulePost">En cours…</span>
                </button>
                <button wire:click="closePost"
                        class="px-4 py-2.5 text-sm font-semibold text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 rounded-xl transition-colors">
                    Annuler
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- ══════════ POPOVER FIXE OVERFLOW ══════════
         Position fixed → jamais clippé par le calendrier.
         Les coordonnées sont capturées au clic via openOverflow().
    --}}
    <div
        x-show="openPopover !== null"
        x-cloak
        @click.stop
        class="fixed z-[200] w-52 bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden"
        :style="popoverAnchor.flip
            ? `bottom: ${window.innerHeight - popoverAnchor.top}px; left: ${popoverAnchor.left}px; transform: translateX(-50%);`
            : `top: ${popoverAnchor.top}px; left: ${popoverAnchor.left}px; transform: translateX(-50%);`"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        {{-- Vue semaine --}}
        @if($view === 'week' && $weekDays)
            @foreach($weekDays as $wd)
            @php
                $colDate = $wd['date']->format('Y-m-d');
                $colKey  = 'col-' . $colDate;
            @endphp
            @if(count($wd['posts']) > 0)
            <div x-show="openPopover === '{{ $colKey }}'" class="overflow-y-auto flex flex-col gap-0.5 p-1.5" style="max-height: 240px;">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 px-2 py-1 border-b border-gray-100 mb-0.5">
                    {{ $wd['dayNum'] }} {{ ucfirst($wd['dayName']) }}
                </p>
                @foreach($wd['posts'] as $post)
                @php
                    $isS = $post['scheduled'];
                    $lbl = $post['caption']
                        ? \Illuminate\Support\Str::limit($post['caption'], 30)
                        : ucfirst(str_replace('_', ' ', $post['template']));
                @endphp
                <button
                    wire:click="openPost({{ $post['id'] }})"
                    @click="openPopover = null"
                    class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg w-full text-left transition-colors
                        @if($post['status'] === 'published') bg-emerald-50 hover:bg-emerald-100
                        @elseif($isS) bg-amber-50 hover:bg-amber-100
                        @else bg-copper/5 hover:bg-copper/10 @endif"
                >
                    @php
                        $popBullet = 'bg-copper';
                        $popTime   = 'text-copper';
                        if ($post['status'] === 'published') { $popBullet = 'bg-emerald-500'; $popTime = 'text-emerald-600'; }
                        elseif ($isS) { $popBullet = 'bg-amber-400'; $popTime = 'text-amber-600'; }
                    @endphp
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $popBullet }}"></span>
                    @if($post['time'] !== '00:00')
                        <span class="text-[10px] font-bold {{ $popTime }} shrink-0">{{ $post['time'] }}</span>
                        <span class="text-gray-300 text-[10px] shrink-0">·</span>
                    @endif
                    <span class="text-[10px] font-medium text-gray-600 truncate">{{ $lbl }}</span>
                </button>
                @endforeach
            </div>
            @endif
            @endforeach
        @endif

        {{-- Vue mois --}}
        @if($view === 'month')
            @foreach($postsByDay as $day => $dayPosts)
            @php
                $mDate = \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                $mKey  = 'month-' . $mDate;
            @endphp
            <div x-show="openPopover === '{{ $mKey }}'" class="overflow-y-auto flex flex-col gap-0.5 p-1.5" style="max-height: 240px;">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 px-2 py-1 border-b border-gray-100 mb-0.5">
                    {{ $day }} {{ \Carbon\Carbon::create($year, $month, $day)->locale('fr')->isoFormat('MMMM') }}
                </p>
                @foreach($dayPosts as $post)
                @php $isSc = ($post['status'] !== 'completed' && $post['status'] !== 'published') || ($post['scheduled'] ?? false); @endphp
                <button
                    wire:click="openPost({{ $post['id'] }})"
                    @click="openPopover = null"
                    class="flex items-center gap-1.5 px-2 py-1.5 rounded-lg w-full text-left transition-colors
                        @if($post['status'] === 'published') bg-emerald-50 hover:bg-emerald-100
                        @elseif($post['status'] === 'review_pending' || ($post['scheduled'] ?? false)) bg-amber-50 hover:bg-amber-100
                        @else bg-copper/5 hover:bg-copper/10 @endif"
                >
                    @php
                        $mPopBullet = 'bg-copper';
                        if ($post['status'] === 'published') $mPopBullet = 'bg-emerald-500';
                        elseif ($post['status'] === 'review_pending' || ($post['scheduled'] ?? false)) $mPopBullet = 'bg-amber-400';
                    @endphp
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $mPopBullet }}"></span>
                    <span class="text-[10px] font-medium text-gray-600 truncate">
                        {{ $post['caption'] ? \Illuminate\Support\Str::limit($post['caption'], 28) : ucfirst(str_replace('_', ' ', $post['template'])) }}
                    </span>
                </button>
                @endforeach
            </div>
            @endforeach
        @endif
    </div>

</div>
