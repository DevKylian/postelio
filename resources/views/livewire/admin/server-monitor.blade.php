<div wire:poll.30s="loadStats">
    <div class="px-5 pt-8 pb-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-midnight tracking-tight">Moniteur Système</h1>
                <p class="text-gray-400 text-sm mt-1">État de santé et ressources du serveur en temps réel.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs font-semibold text-green-600 uppercase tracking-widest leading-none">Live Monitor</span>
                <button wire:click="loadStats" class="ml-4 p-2 text-gray-400 hover:text-copper transition-colors">
                    <svg wire:loading.class="animate-spin" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="px-5 pb-8 space-y-8">
        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- CPU Load -->
            <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-12 -top-12 w-40 h-40 bg-blue-50 rounded-full blur-3xl opacity-50 group-hover:bg-blue-100 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                        </div>
                        <span class="text-2xl font-bold text-midnight tabular-nums">{{ $stats['cpu'] }}%</span>
                    </div>
                    <h3 class="font-bold text-midnight mb-1">Charge CPU</h3>
                    <p class="text-xs text-gray-400 mb-6">Utilisation instantanée du processeur</p>
                    
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full transition-all duration-1000" style="width: {{ min(100, $stats['cpu']) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- RAM Usage -->
            <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-12 -top-12 w-40 h-40 bg-purple-50 rounded-full blur-3xl opacity-50 group-hover:bg-purple-100 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a10.046 10.046 0 01-6.308.6l-2.313-.46a4.123 4.123 0 01-1.464-.543V1.644c.48.275.992.54 1.517.794.704.34 1.48.552 2.222.842 2.067.808 4.254.912 6.368.494l2.585-.516a8 8 0 014.653.64l1.628.814v10.743c0 .264-.047.525-.138.77z"></path></svg>
                        </div>
                        <span class="text-2xl font-bold text-midnight tabular-nums">{{ $stats['ram']['percentage'] }}%</span>
                    </div>
                    <h3 class="font-bold text-midnight mb-1">Mémoire RAM</h3>
                    <p class="text-xs text-gray-400 mb-6">{{ number_format($stats['ram']['used'] / (1024*1024*1024), 2) }} Go / {{ number_format($stats['ram']['total'] / (1024*1024*1024), 1) }} Go</p>
                    
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 rounded-full transition-all duration-1000" style="width: {{ $stats['ram']['percentage'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Disk Usage -->
            <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-12 -top-12 w-40 h-40 bg-amber-50 rounded-full blur-3xl opacity-50 group-hover:bg-amber-100 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        </div>
                        <span class="text-2xl font-bold text-midnight tabular-nums">{{ $stats['disk']['percentage'] }}%</span>
                    </div>
                    <h3 class="font-bold text-midnight mb-1">Stockage</h3>
                    <p class="text-xs text-gray-400 mb-6">{{ number_format($stats['disk']['used'] / (1024*1024*1024), 1) }} Go / {{ number_format($stats['disk']['total'] / (1024*1024*1024), 0) }} Go</p>
                    
                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full transition-all duration-1000" style="width: {{ $stats['disk']['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-midnight flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Information Système
                    </h3>
                </div>
                <div class="p-8">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Système d'exploitation</dt>
                            <dd class="mt-1 text-sm font-semibold text-midnight">{{ $stats['system']['os'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Uptime Serveur</dt>
                            <dd class="mt-1 text-sm font-semibold text-midnight">{{ $stats['uptime'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Version PHP</dt>
                            <dd class="mt-1 text-sm font-semibold text-midnight">{{ $stats['system']['php'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-400">Logiciel Serveur</dt>
                            <dd class="mt-1 text-sm font-semibold text-midnight truncate">{{ $stats['system']['server'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-midnight flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Temps de calcul
                    </h3>
                </div>
                <div class="p-8 flex-1 flex flex-col justify-center text-center">
                    <div class="text-5xl font-bold text-midnight tracking-tight tabular-nums mb-2">{{ date('H:i:s') }}</div>
                    <p class="text-gray-400 text-sm">Heure système actuelle du serveur</p>
                </div>
            </div>
        </div>
    </div>
</div>
