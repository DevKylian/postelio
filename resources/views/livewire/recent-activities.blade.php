<div wire:poll.10s class="glass-panel rounded-2xl overflow-hidden flex flex-col h-full">
    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between bg-white/50 backdrop-blur-sm sticky top-0 z-10">
        <div class="flex items-center gap-2">
            <h3 class="font-bold text-midnight text-sm">Activité récente</h3>
            <span class="flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-copper opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-copper"></span>
            </span>
        </div>
        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Live</span>
    </div>

    <div class="flex-1 overflow-y-auto p-2 space-y-1 max-h-[400px] scrollbar-hide">
        @forelse($activities as $activity)
            <div class="group flex gap-3 p-3 rounded-xl transition-all duration-200 hover:bg-gray-50 border border-transparent hover:border-gray-100">
                <div class="shrink-0 mt-0.5">
                    @if(str_contains($activity->type, 'published'))
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    @elseif(str_contains($activity->type, 'failed'))
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    @elseif(str_contains($activity->type, 'connected'))
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-0.5">
                        <p class="text-[11px] font-bold text-midnight truncate capitalize">{{ str_replace(['post.', 'account.'], '', $activity->type) }}</p>
                        <span class="text-[9px] font-medium text-gray-300 shrink-0">{{ $activity->created_at->diffForHumans(null, true, true) }}</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed line-clamp-2">{{ $activity->message }}</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12 text-center px-4">
                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-midnight mb-1">Aucune activité</p>
                <p class="text-[10px] text-gray-400">Vos événements s'afficheront ici en temps réel.</p>
            </div>
        @endforelse
    </div>

    @if($activities->isNotEmpty())
    <div class="p-3 bg-gray-50/50 border-t border-gray-50 text-center">
        <a href="{{ route('posts.index') }}" wire:navigate class="text-[10px] font-bold text-gray-400 hover:text-copper transition-colors uppercase tracking-widest">
            Voir tout l'historique
        </a>
    </div>
    @endif
</div>
