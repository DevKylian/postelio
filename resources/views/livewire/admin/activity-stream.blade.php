<div wire:key="admin-activity-stream">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                        Live Activity Stream
                    </h1>
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                </div>
                <p class="text-gray-400 text-sm mt-0.5">
                    Surveillance en temps réel des interactions sur la plateforme
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Filters & Search -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Email, IP, Ville..." class="block w-full pl-10 pr-3 py-2 border-gray-100 bg-gray-50/50 rounded-xl text-sm focus:ring-copper focus:border-copper transition-all">
                </div>

                <select wire:model.live="tag" class="block w-full py-2 px-3 border-gray-100 bg-gray-50/50 rounded-xl text-sm focus:ring-copper focus:border-copper transition-all capitalize">
                    <option value="">Tous les tags</option>
                    @foreach($availableTags as $availableTag)
                        <option value="{{ $availableTag }}">{{ $availableTag }}</option>
                    @endforeach
                </select>

                <select wire:model.live="rank" class="block w-full py-2 px-3 border-gray-100 bg-gray-50/50 rounded-xl text-sm focus:ring-copper focus:border-copper transition-all capitalize">
                    <option value="">Tous les rangs</option>
                    @foreach($availableRanks as $availableRank)
                        <option value="{{ $availableRank }}">{{ $availableRank }}</option>
                    @endforeach
                </select>

                <div class="flex items-center justify-end text-xs text-gray-400 font-medium px-2">
                    {{ number_format($logs->total()) }} événements trouvés
                </div>
            </div>

            <!-- Main Stream -->
            <div wire:poll.5s class="bg-midnight rounded-3xl border border-white/5 shadow-2xl overflow-hidden flex flex-col min-h-[600px] relative">
                {{-- Decorative scanline effect --}}
                <div class="absolute inset-0 pointer-events-none opacity-[0.03] bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_4px,3px_100%]"></div>
                
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap border-collapse">
                        <thead>
                            <tr class="text-[10px] font-bold text-gray-500 uppercase tracking-widest border-b border-white/5 bg-black/20">
                                <th class="px-6 py-4">Status & Time</th>
                                <th class="px-6 py-4">User & Rank</th>
                                <th class="px-6 py-4">Endpoint</th>
                                <th class="px-6 py-4">Location</th>
                                <th class="px-6 py-4 text-right">Lat.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($logs as $log)
                                @php
                                    $rankColor = match($log->rank) {
                                        'admin'    => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                                        'business' => 'text-purple-400 bg-purple-400/10 border-purple-400/20',
                                        'pro'      => 'text-indigo-400 bg-indigo-400/10 border-indigo-400/20',
                                        'guest'    => 'text-gray-400 bg-gray-400/10 border-gray-400/20',
                                        default    => 'text-blue-400 bg-blue-400/10 border-blue-400/20'
                                    };
                                    $methodColor = match($log->method) {
                                        'POST'   => 'text-emerald-400',
                                        'PUT'    => 'text-amber-400',
                                        'DELETE' => 'text-rose-400',
                                        default  => 'text-blue-400'
                                    };
                                @endphp
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-mono text-gray-300">{{ $log->created_at->format('H:i:s') }}</span>
                                            <span class="text-[9px] font-bold text-gray-600 uppercase">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col">
                                                @if($log->user_id)
                                                    <a href="{{ route('admin.users.show', $log->user_id) }}" wire:navigate class="text-[13px] font-semibold text-white hover:text-copper transition-colors">
                                                        {{ $log->email }}
                                                    </a>
                                                @else
                                                    <span class="text-[13px] font-semibold text-white">{{ $log->email ?? 'Anonymous' }}</span>
                                                @endif
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <span class="px-1.5 py-0.5 rounded border text-[9px] font-black uppercase tracking-tighter {{ $rankColor }}">
                                                        {{ $log->rank }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-500 font-mono">{{ $log->ip }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-black text-[10px] {{ $methodColor }} w-10">{{ $log->method }}</span>
                                                <span class="text-[13px] text-gray-300 font-mono truncate max-w-xs" title="{{ $log->url }}">
                                                    {{ Str::after($log->url, request()->getSchemeAndHttpHost()) ?: '/' }}
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-gray-500 lowercase mt-0.5">tag: <span class="text-gray-400">{{ $log->tag }}</span></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            @if($log->city)
                                                <span class="text-[13px] text-gray-300 font-semibold">{{ $log->city }}</span>
                                                <span class="text-[10px] text-gray-500 uppercase">{{ $log->country }}</span>
                                            @else
                                                <span class="text-[11px] text-gray-600 italic">Unknown Location</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs font-mono {{ $log->response_time > 500 ? 'text-amber-500' : 'text-emerald-500/80' }}">
                                            {{ $log->response_time }}ms
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10 text-white/5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <p class="text-gray-500 font-medium">No activity captured yet</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-black/40 border-t border-white/5 p-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
