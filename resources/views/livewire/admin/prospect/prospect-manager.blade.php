<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Suivi Prospection Instagram
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Gérez et suivez vos démarches auprès des comptes Instagram
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            
            @if (session()->has('message'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    {{ session('message') }}
                </div>
            @endif

            <!-- Sales Scripts (Modular & Copyable) -->
            <div x-data="{ open: true }">
                <button @click="open = !open" class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-copper transition-colors mb-4">
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    Bibliothèque de Scripts & Séquences
                    <span class="bg-slate-100 px-2 py-0.5 rounded text-[10px]">{{ \App\Models\SalesScript::count() }}</span>
                </button>
                
                <div x-show="open" x-collapse style="display: none;">
                    <livewire:admin.prospect.script-flow-manager />
                </div>
            </div>

            <!-- KPI Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex flex-col justify-center">
                    <div class="flex items-center gap-2 text-rose-500 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-xs font-bold uppercase tracking-wider">À Relancer</p>
                    </div>
                    <p class="text-2xl font-bold text-midnight">{{ $stats['actions_todo'] }}</p>
                </div>
                
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex flex-col justify-center">
                    <div class="flex items-center gap-2 text-green-500 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-xs font-bold uppercase tracking-wider">Réponses</p>
                    </div>
                    <p class="text-2xl font-bold text-midnight">{{ $stats['responded'] }}</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex flex-col justify-center">
                    <div class="flex items-center gap-2 text-amber-500 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        <p class="text-xs font-bold uppercase tracking-wider">DM Envoyés</p>
                    </div>
                    <p class="text-2xl font-bold text-midnight">{{ $stats['dm_sent_total'] }}</p>
                </div>
                
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex flex-col justify-center">
                    <div class="flex items-center gap-2 text-copper mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-xs font-bold uppercase tracking-wider">DM Aujourd'hui</p>
                    </div>
                    <p class="text-2xl font-bold text-midnight">{{ $stats['dm_sent_today'] }}</p>
                </div>
            </div>

            <!-- Add Prospect Form -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <form wire:submit="addProspect" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="w-full sm:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du compte</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                @
                            </div>
                            <input type="text" wire:model="newAccountName" class="pl-8 block w-full rounded-xl border-gray-200 shadow-sm focus:border-copper focus:ring-copper sm:text-sm" placeholder="artisan_du_coin">
                        </div>
                        @error('newAccountName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="w-full sm:w-1/2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Profil (Optionnel)</label>
                        <input type="url" wire:model="newProfileUrl"
                            x-on:paste="
                                const text = $event.clipboardData.getData('text');
                                const match = text.match(/instagram\.com\/([^/?#\s]+)/);
                                const reserved = ['p','reel','reels','stories','explore','accounts','tv','direct'];
                                if (match && match[1] && !reserved.includes(match[1])) {
                                    $wire.set('newAccountName', match[1]);
                                }
                            "
                            class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-copper focus:ring-copper sm:text-sm" placeholder="https://instagram.com/...">
                        @error('newProfileUrl') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto bg-midnight text-white px-6 py-2.5 rounded-xl font-medium hover:bg-gray-800 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <svg wire:loading.remove wire:target="addProspect" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            <svg wire:loading wire:target="addProspect" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="addProspect">Ajouter</span>
                            <span wire:loading wire:target="addProspect">Ajout...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Script Stats -->
            @if($scriptStats->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-sm text-midnight mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Performance par script
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($scriptStats as $stat)
                    @php $rate = $stat->total > 0 ? round(($stat->responded / $stat->total) * 100) : 0; @endphp
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Script {{ $stat->script_version }}</div>
                        <div class="flex items-end justify-between gap-2 mb-2">
                            <div>
                                <div class="text-xl font-bold text-midnight">{{ $stat->responded }}<span class="text-sm font-normal text-slate-400">/{{ $stat->total }}</span></div>
                                <div class="text-[10px] text-slate-400">réponses</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold {{ $rate >= 20 ? 'text-green-500' : ($rate >= 10 ? 'text-amber-500' : 'text-slate-400') }}">{{ $rate }}%</div>
                                <div class="text-[10px] text-slate-400">taux</div>
                            </div>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $rate >= 20 ? 'bg-green-400' : ($rate >= 10 ? 'bg-amber-400' : 'bg-slate-300') }}" style="width: {{ min($rate, 100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Prospects List -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <h2 class="font-bold text-base text-midnight">Comptes encours</h2>
                        <span class="bg-gray-100 text-gray-600 py-1 px-3 rounded-full text-xs font-semibold">{{ $prospects->total() }} total</span>
                    </div>
                    
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" wire:model.live="filterAction" class="sr-only">
                                <div class="block {{ $filterAction ? 'bg-rose-500' : 'bg-gray-200' }} w-10 h-6 rounded-full transition-colors"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $filterAction ? 'transform translate-x-4' : '' }}"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700 {{ $filterAction ? 'text-rose-600 font-bold' : '' }}">Actions à faire aujourd'hui</span>
                        </label>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="text-gray-400 bg-white">
                            <tr>
                                <th class="px-6 py-4 font-medium w-1/4">Compte</th>
                                <th class="px-6 py-4 font-medium">Statut</th>
                                <th class="px-6 py-4 font-medium" style="min-width:130px">Script</th>
                                <th class="px-6 py-4 font-medium">Notes</th>
                                <th class="px-6 py-4 font-medium text-right w-1/6">Dernière action</th>
                                <th class="px-6 py-4 font-medium text-right w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($prospects as $prospect)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold shrink-0 border border-gray-200">
                                            {{ strtoupper(substr($prospect->account_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-midnight">{{ $prospect->account_name }}</div>
                                            @if($prospect->profile_url)
                                                <a href="{{ $prospect->profile_url }}" target="_blank" class="text-xs text-copper hover:underline flex items-center gap-1 mt-0.5">
                                                    Voir profil <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4" style="min-width:180px">
                                    @php
                                        $currentNode = $prospect->getCurrentNode();
                                        $branches    = $prospect->getAvailableBranches();
                                        $needsFollowUp = $prospect->needsFollowUp();
                                        $branchColors = [
                                            'reply'    => 'bg-emerald-50 border-emerald-300 text-emerald-700 hover:bg-emerald-100',
                                            'no_reply' => 'bg-amber-50 border-amber-300 text-amber-700 hover:bg-amber-100',
                                            'positive' => 'bg-blue-50 border-blue-300 text-blue-700 hover:bg-blue-100',
                                            'negative' => 'bg-rose-50 border-rose-300 text-rose-700 hover:bg-rose-100',
                                        ];
                                    @endphp

                                    @if($prospect->current_node_id)
                                        {{-- Flow mode --}}
                                        <div class="space-y-1.5">
                                            @if($needsFollowUp)
                                                <div class="flex items-center gap-1.5 animate-pulse">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                                    <span class="text-[10px] text-rose-600 font-bold">À relancer !</span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                                    <span class="text-[10px] text-amber-600 font-semibold">En attente</span>
                                                </div>
                                            @endif

                                            @if($currentNode && !empty($currentNode['content']))
                                                <div class="text-[10px] text-gray-400 truncate max-w-[160px]" title="{{ $currentNode['content'] }}">
                                                    {{ \Illuminate\Support\Str::limit($currentNode['content'], 45) }}
                                                </div>
                                            @endif

                                            @if(!empty($branches))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($branches as $branch)
                                                        <button
                                                            wire:click="advanceProspect({{ $prospect->id }}, '{{ $branch['next'] ?? '' }}', '{{ $branch['condition'] }}')"
                                                            wire:loading.attr="disabled"
                                                            class="text-[10px] font-bold px-2 py-0.5 rounded-md border transition-colors {{ $branchColors[$branch['condition']] ?? 'bg-gray-50 border-gray-200 text-gray-700 hover:bg-gray-100' }}"
                                                        >
                                                            {{ $branch['label'] ?? $branch['condition'] }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] text-gray-400 italic">Fin du script</span>
                                                    <button wire:click="resetScript({{ $prospect->id }})" class="text-[10px] text-copper font-semibold hover:underline">
                                                        Recommencer
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                    @elseif($prospect->script_version)
                                        {{-- Script assigné mais pas encore démarré --}}
                                        <button
                                            wire:click="startScript({{ $prospect->id }})"
                                            class="inline-flex items-center gap-1.5 bg-midnight text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-gray-800 transition-colors"
                                        >
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            Démarrer
                                        </button>

                                    @else
                                        {{-- Pas de script : dropdown legacy --}}
                                        @php
                                            $statusColors = [
                                                'discovered'     => 'bg-gray-100 text-gray-600 border-gray-200',
                                                'followed'       => 'bg-blue-50 text-blue-600 border-blue-200',
                                                'dm_sent'        => 'bg-yellow-50 text-yellow-600 border-yellow-200',
                                                'relance_1'      => 'bg-orange-50 text-orange-600 border-orange-200',
                                                'not_interested' => 'bg-red-50 text-red-600 border-red-200',
                                                'responded'      => 'bg-green-50 text-green-600 border-green-200',
                                            ];
                                            $colorClass = $statusColors[$prospect->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                                        @endphp
                                        <div class="relative">
                                            <select
                                                wire:change="updateStatus({{ $prospect->id }}, $event.target.value)"
                                                class="appearance-none block w-full px-3 py-1.5 pr-8 border rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-copper/30 transition-colors {{ $colorClass }}"
                                            >
                                                @foreach($statuses as $key => $label)
                                                    <option value="{{ $key }}" @if($prospect->status === $key) selected @endif>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-current opacity-70">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <select
                                        wire:change="updateScript({{ $prospect->id }}, $event.target.value)"
                                        class="appearance-none block w-full px-3 py-1.5 border rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-copper/30 transition-colors {{ $prospect->script_version ? 'bg-copper/10 text-amber-700 border-amber-200' : 'bg-gray-50 text-gray-400 border-gray-200' }}"
                                        style="min-width:110px"
                                    >
                                        <option value="">—</option>
                                        @foreach($scripts as $num => $label)
                                            <option value="{{ $num }}" @if((int)$prospect->script_version === $num) selected @endif>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4">
                                    <div x-data="{ editing: false, note: '{{ addslashes($prospect->notes) }}' }" class="relative">
                                        <div x-show="!editing" @click="editing = true" class="cursor-pointer group flex items-start gap-2">
                                            <div class="text-xs text-gray-500 whitespace-normal break-words w-full min-h-[1.5rem] border-b border-transparent group-hover:border-gray-300 transition-colors">
                                                {{ $prospect->notes ?: 'Ajouter une note...' }}
                                            </div>
                                            <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-copper shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </div>
                                        <div x-show="editing" style="display: none;" class="flex gap-2 items-center">
                                            <input 
                                                type="text" 
                                                x-model="note"
                                                @keydown.enter="editing = false; $wire.updateNotes({{ $prospect->id }}, note)"
                                                @keydown.escape="editing = false"
                                                class="w-full text-xs border-gray-300 rounded-md focus:border-copper focus:ring-copper p-1.5 shadow-sm"
                                                placeholder="Note..."
                                                x-effect="if(editing) $el.focus()"
                                            >
                                            <button @click="editing = false; $wire.updateNotes({{ $prospect->id }}, note)" class="p-1.5 bg-copper text-white rounded-md hover:bg-copper/80">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-xs">
                                    <div class="text-gray-900 font-medium">{{ $prospect->last_action_date ? $prospect->last_action_date->format('d/m/Y') : '-' }}</div>
                                    <div class="text-gray-400">{{ $prospect->last_action_date ? $prospect->last_action_date->diffForHumans() : '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="deleteProspect({{ $prospect->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce prospect ?" class="text-gray-400 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-2xl flex items-center justify-center mb-4 border border-gray-100">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1.5">Aucun prospect</h3>
                                        <p class="text-sm text-gray-500 max-w-md">Vous n'avez pas encore ajouté de comptes Instagram à prospecter.<br>Utilisez le formulaire ci-dessus pour commencer.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($prospects->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $prospects->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
