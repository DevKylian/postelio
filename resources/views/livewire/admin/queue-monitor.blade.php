<div>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-midnight leading-tight flex items-center gap-3">
            <div class="p-2 bg-white rounded-xl shadow-sm border border-slate-100">
                <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span>Moniteur de Queue Live</span>
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            @if (session()->has('success'))
                <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 animate-fade-in shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                {{-- Card: Pending --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">En attente</p>
                        <p class="text-2xl font-bold text-midnight tabular-nums">{{ $stats['pending'] }}</p>
                    </div>
                </div>

                {{-- Card: Processing --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">En cours</p>
                        <p class="text-2xl font-bold text-midnight tabular-nums">{{ $stats['processing'] }}</p>
                    </div>
                </div>

                {{-- Card: Success --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Réussis aujourd'hui</p>
                        <p class="text-2xl font-bold text-midnight tabular-nums">{{ $stats['today'] }}</p>
                    </div>
                </div>

                {{-- Card: Failed --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Échecs système</p>
                        <p class="text-2xl font-bold text-midnight tabular-nums">{{ $stats['failed'] }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-panel rounded-3xl overflow-hidden overflow-x-auto shadow-2xl border-white/40">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">ID System</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Type de Job</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Cible / Détails</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Exécution Prévue</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 text-center">Tentatives</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/50">
                        @forelse($jobs as $job)
                        <tr class="hover:bg-white/60 transition-colors group {{ $job['is_reserved'] ? 'bg-amber-50/40' : '' }}">
                            <td class="px-6 py-5">
                                <span class="font-mono text-[11px] text-slate-400 bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-100">#{{ $job['id'] }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-bold text-slate-700 tracking-tight">{{ $job['type'] }}</span>
                            </td>
                            <td class="px-6 py-5">
                                @if($job['post'])
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-midnight leading-tight">{{ $job['post']->company->name ?? 'Sans entreprise' }}</span>
                                        <span class="text-[11px] text-slate-400 font-semibold truncate max-w-[200px] mt-0.5">
                                            "{{ \Illuminate\Support\Str::limit($job['post']->caption, 40) ?: 'Sans légende' }}"
                                        </span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Action Système</span>
                                        @if($job['postId'])
                                            <span class="text-[10px] text-slate-300">#{{ $job['postId'] }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        @if($job['is_reserved'])
                                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest leading-none">En cours...</span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.3)]"></span>
                                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest leading-none">Prêt</span>
                                        @endif
                                    </div>
                                    <span class="text-[13px] font-bold text-slate-700 tabular-nums leading-none mt-1">{{ $job['available_at'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span @class([
                                    'text-xs font-black tabular-nums px-3 py-1.5 rounded-xl border',
                                    'bg-white text-slate-400 border-slate-100' => $job['attempts'] == 0,
                                    'bg-rose-50 text-rose-600 border-rose-100' => $job['attempts'] > 0,
                                ])>
                                    {{ $job['attempts'] }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <button
                                    wire:click="deleteJob({{ $job['id'] }})"
                                    wire:confirm="Es-tu sûr de vouloir supprimer ce job système ?"
                                    class="p-2.5 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-2xl transition-all active:scale-90"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center opacity-20">
                                    <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-xl font-black text-midnight">File d'attente vide</p>
                                    <p class="text-sm font-medium mt-1">Le système est au repos total.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex items-start gap-5 p-6 glass-panel rounded-[2.5rem] border-white/40 text-slate-400 shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-copper/5 blur-[80px] rounded-full group-hover:bg-copper/10 transition-colors"></div>
                
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl shrink-0">
                    <svg class="w-6 h-6 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-midnight mb-1 uppercase tracking-widest">Guide de Supervision Système</h4>
                    <p class="text-[13px] leading-relaxed max-w-2xl font-medium text-slate-500">
                        Ce moniteur affiche les processus de bas niveau. Les lignes <span class="text-amber-500 font-bold">orangées</span> indiquent qu'un worker est activement sur ce job. 
                        Si le nombre de <span class="text-rose-500 font-bold underline decoration-rose-500/20">tentatives</span> augmente, vérifie les logs serveur pour d'éventuels problèmes de connexion API.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
