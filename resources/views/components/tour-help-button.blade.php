@props([
    'name' => null
])

<div x-data="{ 
        hasSeen: localStorage.getItem('postelio_tour_{{ $name }}') === 'done',
        open: false
    }"
    x-init="
        window.addEventListener('tour:finished', (e) => {
            if(e.detail.name === '{{ $name }}') hasSeen = true;
        });
    "
    class="fixed bottom-6 right-6 z-[50]"
    x-show="hasSeen"
    x-cloak>
    
    {{-- Mini menu --}}
    <div x-show="open" 
         x-on:click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="absolute bottom-full right-0 mb-3 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden py-1.5 ring-1 ring-black/[0.05]">
        <button @click="open = false; $dispatch('replay-tour', { name: '{{ $name }}' })"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-amber-50 hover:text-copper transition-colors">
            <div class="w-6 h-6 rounded-lg bg-amber-50 flex items-center justify-center text-copper group-hover:bg-copper group-hover:text-white transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            Revoir le tutoriel
        </button>
    </div>

    <button @click="open = !open"
            class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300 shadow-sm hover:shadow-md"
            :class="open ? 'bg-copper text-white' : 'bg-white border border-slate-200 text-slate-400 hover:text-copper hover:border-copper/30 hover:bg-amber-50'"
            title="Aide & Tutoriel">
        <svg class="w-5 h-5 transition-transform duration-300" 
             :class="open ? 'rotate-90' : 'group-hover:scale-110'"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
