<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display font-bold text-2xl text-midnight tracking-tight leading-tight">
                    Gestion du Changelog
                </h1>
                <p class="text-slate-400 text-sm mt-1 font-medium">
                    Administrez les mises à jour et nouveautés de la plateforme.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Retour au dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-10">
        
        <div class="flex justify-end px-4">
            <button wire:click="toggleForm" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-lg {{ $showForm ? 'bg-slate-100 text-slate-600 hover:bg-slate-200 shadow-slate-200/50' : 'bg-midnight text-white hover:bg-slate-800 shadow-slate-900/10' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}" />
                </svg>
                {{ $showForm ? 'Fermer le formulaire' : 'Ajouter une mise à jour' }}
            </button>
        </div>

        {{-- Form Section --}}
        @if($showForm)
        <section class="animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] border-white/80 shadow-2xl shadow-slate-200/40 relative overflow-hidden bg-white/60">
                <div class="absolute top-0 right-0 w-64 h-64 bg-copper/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
                
                <h2 class="font-display font-bold text-xl text-midnight mb-8 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-copper/10 flex items-center justify-center text-copper">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    {{ $isEditing ? 'Modifier la mise à jour' : 'Nouvelle mise à jour' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-8 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="space-y-2 lg:col-span-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Titre de la mise à jour</label>
                            <input type="text" wire:model="title" placeholder="Ex: Refonte de l'interface" 
                                   class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                            @error('title') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Type d'update</label>
                            <select wire:model="type" class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                                <option value="feature">🚀 Nouveauté</option>
                                <option value="improvement">⚡ Amélioration</option>
                                <option value="fix">🐛 Correction</option>
                            </select>
                            @error('type') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Date de publication</label>
                            <input type="date" wire:model="published_at" 
                                   class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                            @error('published_at') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Version (optionnel)</label>
                            <input type="text" wire:model="version" placeholder="Ex: v2.1.0" 
                                   class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                            @error('version') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2" 
                         x-data="{ 
                            content: @entangle('content'),
                            isFocused: false
                         }"
                         x-init="
                            $refs.trix.editor.loadHTML(content);
                            $watch('content', value => {
                                if (value !== $refs.trix.editor.getHTML()) {
                                    $refs.trix.editor.loadHTML(value);
                                }
                            })
                         "
                         @trix-change="content = $event.target.value"
                         wire:ignore>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Description détaillée (Riche)</label>
                        <input id="content" type="hidden" name="content">
                        <trix-editor input="content" 
                                     x-ref="trix"
                                     placeholder="Décrivez les changements apportés (gras, listes, retours...)" 
                                     class="w-full bg-white/50 border-slate-100 rounded-[2rem] p-6 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none leading-relaxed min-h-[250px]"></trix-editor>
                        
                        @error('content') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" 
                                class="bg-copper text-white px-10 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-copper/20 active:scale-95 disabled:opacity-50">
                            {{ $isEditing ? 'Mettre à jour' : 'Publier maintenant' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
        @endif

        {{-- List Section --}}
        <section class="space-y-6">
            <div class="flex items-center gap-4 px-4">
                <h2 class="font-display font-bold text-midnight text-lg tracking-tight shrink-0">Historique des entrées</h2>
                <div class="h-px bg-slate-100 flex-1"></div>
            </div>

            <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/60 shadow-xl shadow-slate-200/30 bg-white/40">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5">Titre & Détails</th>
                            <th class="px-8 py-5">Date</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($changelogs as $log)
                        <tr class="group hover:bg-white/60 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-{{ $log->type_color }}-50 flex items-center justify-center text-{{ $log->type_color }}-600 shrink-0 border border-{{ $log->type_color }}-100/50">
                                        @if($log->type == 'feature')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                        @elseif($log->type == 'fix')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-midnight text-sm flex items-center gap-2">
                                            {{ $log->title }}
                                            @if($log->version)
                                                <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded text-slate-400">{{ $log->version }}</span>
                                            @endif
                                        </h3>
                                        <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 italic leading-relaxed">
                                            {{ $log->content }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-[11px] font-bold text-slate-500 tracking-tight">
                                    {{ \Carbon\Carbon::parse($log->published_at)->translatedFormat('d F Y') }}
                                </div>
                                <div class="text-[10px] text-slate-300 mt-0.5 uppercase tracking-widest leading-none">
                                    {{ \Carbon\Carbon::parse($log->published_at)->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit({{ $log->id }})" 
                                            class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:bg-copper hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="confirm('Voulez-vous vraiment supprimer cette mise à jour ?') || event.stopImmediatePropagation()"
                                            wire:click="delete({{ $log->id }})" 
                                            class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-400 tracking-tight">Aucune mise à jour publiée à ce jour.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <style>
        .animate-in { animation: animate-in 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes animate-in {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @push('styles')
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
        <style>
            trix-toolbar .trix-button-group--file-tools { display: none !important; }
            trix-editor { 
                border-color: #f1f5f9 !important; 
                border-radius: 2rem !important; 
                background: white !important;
                padding: 1.5rem !important;
                min-height: 250px !important;
            }
            trix-editor:focus { border-color: #b87333 !important; ring: 2px #b873331a; }
        </style>
    @endpush

    @push('scripts')
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    @endpush
</div>
