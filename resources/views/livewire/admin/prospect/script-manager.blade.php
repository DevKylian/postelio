<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════
         HEADER — Titre + création d'un nouveau script
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="font-bold text-base text-midnight flex items-center gap-2">
                    <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Bibliothèque de Scripts & Séquences
                </h2>
                <p class="text-[11px] text-slate-400 mt-1 ml-7">
                    Construis tes scripts de prospection avec des branches conditionnelles
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <input
                    type="text"
                    wire:model="newName"
                    wire:keydown.enter="addScript"
                    class="w-full sm:w-64 text-xs rounded-xl border-gray-200 focus:border-copper focus:ring-copper"
                    placeholder="Nom du nouveau script..."
                >
                <button
                    wire:click="addScript"
                    wire:loading.attr="disabled"
                    class="bg-midnight text-white px-4 py-2.5 rounded-xl text-xs font-bold
                           hover:bg-gray-800 transition-colors shadow-sm whitespace-nowrap
                           flex items-center gap-2 justify-center"
                >
                    <span wire:loading.remove wire:target="addScript">
                        <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nouveau Script
                    </span>
                    <span wire:loading wire:target="addScript" class="flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Création...
                    </span>
                </button>
            </div>
        </div>

        @if(session()->has('script_message'))
            <div class="mb-4 text-xs text-green-700 bg-green-50 border border-green-100 p-3 rounded-xl flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('script_message') }}
            </div>
        @endif

        @if($scripts->isEmpty())
            {{-- ─── Empty state ─────────────────────────────── --}}
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-500">Aucun script enregistré</p>
                <p class="text-xs text-slate-400 mt-1">Crée ton premier script de prospection ci-dessus.</p>
            </div>
        @else
            {{-- ─── Scripts list ──────────────────────────────── --}}
            <div class="space-y-4">
                @foreach($scripts as $script)
                    @php
                        $flow       = $script->flow;
                        $nodeCount  = $flow ? count($flow['nodes']) : 0;
                        $branchCount = $flow
                            ? collect($flow['nodes'])->sum(fn($n) => count($n['branches']))
                            : 0;
                        $hasFlow = !empty($flow);
                    @endphp

                    <div
                        x-data="{ open: false }"
                        class="border border-slate-200 rounded-2xl overflow-hidden bg-white"
                    >
                        {{-- ─── Script header (clickable) ─────────── --}}
                        <div
                            @click="open = !open"
                            class="flex items-center gap-3 p-4 cursor-pointer select-none
                                   hover:bg-slate-50/60 transition-colors group"
                        >
                            {{-- Chevron --}}
                            <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-slate-200
                                        flex items-center justify-center flex-shrink-0 transition-colors">
                                <svg
                                    class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                    :class="open ? 'rotate-90' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>

                            {{-- Script name (editable on click without propagating) --}}
                            <input
                                type="text"
                                value="{{ $script->name }}"
                                @click.stop
                                onblur="@this.updateName({{ $script->id }}, this.value)"
                                class="bg-transparent border-none p-0 font-bold text-midnight
                                       focus:ring-0 text-sm flex-1 min-w-0 cursor-text
                                       uppercase tracking-tight"
                            >

                            {{-- Stats badges --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($hasFlow)
                                    <span class="hidden sm:inline-flex items-center gap-1
                                                 text-[10px] font-bold text-slate-400 bg-slate-100
                                                 px-2 py-1 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                                        </svg>
                                        {{ $nodeCount }} msg
                                    </span>
                                    <span class="hidden sm:inline-flex items-center gap-1
                                                 text-[10px] font-bold text-slate-400 bg-slate-100
                                                 px-2 py-1 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        {{ $branchCount }} branche{{ $branchCount > 1 ? 's' : '' }}
                                    </span>
                                @endif

                                {{-- Delete button --}}
                                <button
                                    @click.stop
                                    wire:click="deleteScript({{ $script->id }})"
                                    wire:confirm="Supprimer ce script définitivement ?"
                                    class="w-7 h-7 flex items-center justify-center rounded-xl
                                           text-slate-300 hover:text-rose-400 hover:bg-rose-50
                                           transition-all opacity-0 group-hover:opacity-100"
                                    title="Supprimer ce script"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- ─── Flow builder (expanded) ───────────── --}}
                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                        >
                            <div class="border-t border-slate-100 p-4 sm:p-6 bg-slate-50/30">

                                @if($hasFlow)
                                    {{-- Legend --}}
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-5 pb-4 border-b border-slate-100">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Branches :</span>
                                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-700">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            💬 Si réponse
                                        </span>
                                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-amber-700">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                            ⏰ Pas de réponse
                                        </span>
                                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-blue-700">
                                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                            👍 Réponse positive
                                        </span>
                                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-rose-700">
                                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                            👎 Réponse négative
                                        </span>
                                    </div>

                                    {{-- Flow tree --}}
                                    <div class="overflow-x-auto pb-2">
                                        <div class="min-w-0">
                                            @include('livewire.admin.prospect.partials.flow-node', [
                                                'nodeId'   => $flow['root'],
                                                'nodes'    => $flow['nodes'],
                                                'scriptId' => $script->id,
                                                'depth'    => 0,
                                            ])
                                        </div>
                                    </div>
                                @else
                                    {{-- Old script without flow — migrate button --}}
                                    <div class="text-center py-8">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100
                                                    flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-600 mb-1">
                                            Ce script utilise l'ancien format
                                        </p>
                                        <p class="text-xs text-slate-400 mb-4">
                                            Convertis-le au nouveau flow builder pour bénéficier des branches conditionnelles.
                                        </p>
                                        <button
                                            wire:click="initFlow({{ $script->id }})"
                                            class="bg-midnight text-white px-5 py-2.5 rounded-xl text-xs font-bold
                                                   hover:bg-gray-800 transition-colors shadow-sm inline-flex items-center gap-2"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Convertir en Flow Builder
                                        </button>
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>{{-- end script card --}}
                @endforeach
            </div>
        @endif

    </div>{{-- end main card --}}
</div>
