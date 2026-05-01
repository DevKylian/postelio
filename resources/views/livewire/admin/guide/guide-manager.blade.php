<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display font-bold text-2xl text-midnight tracking-tight leading-tight">
                    Guides Gratuits & Lead Magnets
                </h1>
                <p class="text-slate-400 text-sm mt-1 font-medium">
                    Générez des guides experts pour attirer des prospects qualifiés.
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

        {{-- Stats Overview --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl group-hover:bg-orange-500/10 transition-colors"></div>
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Leads</p>
                        <h4 class="text-2xl font-display font-bold text-midnight leading-tight">{{ number_format($stats['total_leads']) }}</h4>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aujourd'hui</p>
                        <h4 class="text-2xl font-display font-bold text-midnight leading-tight">+{{ number_format($stats['leads_today']) }}</h4>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Vues Totales</p>
                        <h4 class="text-2xl font-display font-bold text-midnight leading-tight">{{ number_format($stats['total_views']) }}</h4>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10 transition-colors"></div>
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Taux Conv.</p>
                        <h4 class="text-2xl font-display font-bold text-midnight leading-tight">{{ $stats['avg_conversion'] }}%</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4">
            <div class="relative w-full sm:w-80">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un guide..."
                       class="w-full pl-11 pr-4 py-2.5 bg-white/50 border-slate-100 rounded-2xl text-sm font-medium text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.guides.all-leads') }}" wire:navigate class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-midnight transition-all shadow-sm active:scale-95">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    Voir tous les leads
                </a>
                @if(!$showForm)
                <button wire:click="openAIModal" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-lg bg-gradient-to-r from-orange-500 to-amber-500 text-white hover:shadow-orange-500/30 hover:-translate-y-0.5 border border-orange-500/20 active:translate-y-0">
                    <svg wire:loading wire:target="openAIModal" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <svg wire:loading.remove wire:target="openAIModal" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <span wire:loading.remove wire:target="openAIModal">Générateur de Lead Magnet</span>
                    <span wire:loading wire:target="openAIModal">Préparation...</span>
                </button>
                @endif
                <button wire:click="toggleForm"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-lg {{ $showForm ? 'bg-slate-100 text-slate-600 hover:bg-slate-200 shadow-slate-200/50' : 'bg-midnight text-white hover:bg-slate-800 shadow-slate-900/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}" />
                    </svg>
                    {{ $showForm ? 'Fermer' : 'Nouveau Guide' }}
                </button>
            </div>
        </div>

        {{-- Form Section --}}
        @if($showForm)
        <section class="animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] border-white/80 shadow-2xl shadow-slate-200/40 relative overflow-hidden bg-white/60">
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/5 rounded-full blur-3xl -mr-32 -mt-32"></div>

                <h2 class="font-display font-bold text-xl text-midnight mb-8 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    {{ $isEditing ? 'Modifier le guide' : 'Nouveau guide gratuit' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-8 relative z-10">
                    {{-- Title + Subtitle --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Titre du guide</label>
                            <input type="text" wire:model="title" placeholder="Ex: Comment répondre à l'objection 'C'est trop cher'..."
                                   class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                            @error('title') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Sous-titre (Promesse)</label>
                            <input type="text" wire:model="sous_titre" placeholder="3 scripts exacts à lire au client..."
                                   class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-medium text-midnight focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                            @error('sous_titre') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Featured Image (Guide Cover) --}}
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Couverture du guide</label>
                        <div class="flex items-center gap-6">
                            @if($featured_image_upload)
                                <img src="{{ $featured_image_upload->temporaryUrl() }}" alt="Preview" class="w-32 aspect-[3/4] object-cover rounded-xl border border-slate-100 shadow-md">
                            @elseif($existing_featured_image)
                                <img src="{{ Storage::url($existing_featured_image) }}" alt="Current" class="w-32 aspect-[3/4] object-cover rounded-xl border border-slate-100 shadow-md">
                            @else
                                <div class="w-32 aspect-[3/4] rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div class="flex flex-col gap-2">
                                <input type="file" wire:model="featured_image_upload" accept="image/*"
                                       class="text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-orange-500/10 file:text-orange-600 hover:file:bg-orange-500/20 transition-all">
                                <p class="text-[10px] text-slate-400 font-medium">Format recommandé : Portrait (ex: 1200x1600)</p>
                            </div>
                        </div>
                        @error('featured_image_upload') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Content (HTML Mode) --}}
                    <div class="space-y-4"
                         x-data="{
                            content: @entangle('contenu_html'),
                            htmlMode: true
                         }">
                        <div class="flex items-center justify-between px-1 mb-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Contenu du guide (HTML)</label>
                        </div>

                        <div class="relative group">
                            <textarea x-model="content"
                                      placeholder="Collez le contenu HTML du guide ici..."
                                      class="w-full bg-slate-900 text-green-400 border-slate-700 rounded-[2.5rem] p-8 text-sm font-mono focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none leading-relaxed min-h-[500px] resize-y shadow-2xl shadow-slate-900/20"></textarea>
                            <div class="mt-2 px-4 py-2 bg-amber-50 rounded-xl border border-amber-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wider">Note : Utilisez des balises h2, h3, p, ul, li pour structurer le guide.</p>
                            </div>
                        </div>
                        @error('contenu_html') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- SEO Section --}}
                    <div class="space-y-6 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest">SEO Meta Description</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Meta Description <span class="text-slate-300 normal-case">(max 160 car.)</span></label>
                            <textarea wire:model="meta_description" rows="2" maxlength="160" placeholder="Description pour Google (générée auto si vide)"
                                      class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none resize-none"></textarea>
                            @error('meta_description') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Status, Date & Access --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Statut</label>
                            <select wire:model="status" class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                                <option value="draft">Brouillon</option>
                                <option value="published">Publié</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Date de publication</label>
                            <input type="date" wire:model="published_at"
                                   class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Accès du guide</label>
                            <label class="flex items-center gap-3 p-4 bg-white/50 border border-slate-100 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="checkbox" wire:model="requires_email" class="w-5 h-5 text-orange-500 bg-slate-100 border-slate-300 rounded focus:ring-orange-500 focus:ring-2">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-midnight">Bloquer l'accès</span>
                                    <span class="text-[10px] text-slate-400 font-medium">Demander l'email avant la lecture</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" wire:click="toggleForm"
                                class="px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest text-slate-500 bg-slate-100 hover:bg-slate-200 transition-all">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-orange-500 text-white px-10 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-orange-500/20 active:scale-95 disabled:opacity-50 inline-flex items-center justify-center gap-2">
                            <svg wire:loading wire:target="save" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ $isEditing ? 'Mettre à jour' : ($status === 'published' ? 'Publier' : 'Enregistrer le brouillon') }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
        @endif

        {{-- List Section --}}
        <section class="space-y-6">
            <div class="flex items-center gap-4 px-4">
                <h2 class="font-display font-bold text-midnight text-lg tracking-tight shrink-0">Tous les guides</h2>
                <div class="h-px bg-slate-100 flex-1"></div>
                <span class="text-xs font-bold text-slate-400">{{ $guides->total() }} guide(s)</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8">
                @forelse($guides as $guide)
                    <div class="group bg-white rounded-[2rem] border border-slate-100 p-3 shadow-sm hover:shadow-2xl hover:border-orange-500/10 hover:-translate-y-1 transition-all duration-500 flex flex-col h-full bg-gradient-to-b from-white to-slate-50/30">
                        {{-- Image Container --}}
                        <div class="relative aspect-[3/4] rounded-3xl overflow-hidden bg-slate-100 shrink-0 shadow-inner isolate">
                            @if($guide->featured_image)
                                <img src="{{ Storage::url($guide->featured_image) }}" alt="{{ $guide->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-200">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                </div>
                            @endif
                            
                            {{-- Status Badge --}}
                            <div class="absolute top-4 right-4">
                                @if($guide->status === 'published')
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/80 backdrop-blur-md border border-white/20 shadow-lg shadow-emerald-500/20">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                                        <span class="text-[9px] font-black text-white uppercase tracking-widest">En ligne</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/60 backdrop-blur-md border border-white/10 shadow-lg">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Brouillon</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 flex flex-col pt-5 px-2 pb-1">
                            <h3 class="font-display font-bold text-midnight text-[15px] leading-tight group-hover:text-orange-600 transition-colors line-clamp-2 min-h-[2.5rem]">{{ $guide->title }}</h3>
                            <p class="text-[10px] font-medium text-slate-400 mt-2 line-clamp-1 italic">{{ $guide->sous_titre }}</p>

                            <div class="mt-auto pt-6 border-t border-slate-100 flex items-center justify-between">
                                {{-- Stats --}}
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1 text-slate-400" title="Vues">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        <span class="text-[10px] font-black tabular-nums">{{ number_format($guide->downloads_count) }}</span>
                                    </div>
                                    <a href="{{ route('admin.guides.leads', $guide->id) }}" wire:navigate class="flex items-center gap-1 text-orange-500 hover:text-orange-600 transition-colors" title="Accéder aux leads">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span class="text-[10px] font-black tabular-nums">{{ number_format($guide->leads_count) }}</span>
                                    </a>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('guides.show', $guide->slug) }}" target="_blank" class="p-2 rounded-xl text-slate-300 hover:text-midnight hover:bg-slate-100 transition-all" title="Voir le guide">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    </a>
                                    <button wire:click="edit({{ $guide->id }})" class="p-2 rounded-xl text-slate-300 hover:text-orange-500 hover:bg-orange-50 transition-all" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button onclick="confirm('Supprimer ce guide ?') || event.stopImmediatePropagation()" wire:click="delete({{ $guide->id }})" class="p-2 rounded-xl text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition-all" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-white/40 rounded-[2.5rem] border border-dashed border-slate-200 flex flex-col items-center justify-center gap-3">
                        <div class="w-16 h-16 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        </div>
                        <p class="text-sm font-medium text-slate-400 tracking-tight">Aucun guide gratuit n'a encore été créé.</p>
                        <button wire:click="toggleForm" class="text-xs font-bold text-orange-600 hover:text-orange-500 transition-colors">Créer votre premier guide</button>
                    </div>
                @endforelse
            </div>

            @if($guides->hasPages())
            <div class="px-4 mt-8">
                {{ $guides->links() }}
            </div>
            @endif
        </section>

        {{-- AI Prompt Modal --}}
        @if($showAIPromptModal)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center"
             x-data="guideModal"
             x-init="initModal($wire)">
            <div class="fixed inset-0 bg-slate-900/45 backdrop-blur-md" wire:click="closeAIModal"></div>
            
            <div class="relative w-full max-w-5xl mx-4 sm:mx-6 bg-white/95 backdrop-blur-md border border-white/40 shadow-2xl rounded-[2.5rem] overflow-hidden flex flex-col h-[90vh] sm:h-auto sm:max-h-[85vh] animate-in zoom-in-95 duration-300">
                <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100 bg-white/50 shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-lg shadow-orange-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-xl text-midnight">Générateur de Lead Magnet</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">IA Strategist & Expert Métier</p>
                        </div>
                    </div>
                    <button wire:click="closeAIModal" class="p-2 rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Left side: Prompt Guide --}}
                    <div class="space-y-5 flex flex-col h-full">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.15em] flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-orange-500/20 text-orange-600 flex items-center justify-center text-xs">1</span>
                                Le Prompt Stratégique
                            </h4>
                            <button @click="copyPrompt()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all shadow-sm"
                            :class="copied ? 'bg-green-100 text-green-600 border border-green-200' : 'bg-white text-slate-600 border border-slate-200 hover:border-orange-500/50 hover:text-orange-600'">
                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                <svg x-cloak x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                <span x-text="copied ? 'Copié !' : 'Copier'"></span>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <input type="text" x-model="subject" placeholder="Sujet (ex: objection 'C'est trop cher')" 
                                   class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3 text-sm font-medium text-midnight focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition-all shadow-sm">
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" x-model="target" placeholder="Cible (ex: Spécial Plombiers)" 
                                       class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3 text-sm font-medium text-midnight focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition-all shadow-sm">
                                <input type="text" x-model="promise" placeholder="Promesse (ex: 3 scripts exacts)" 
                                       class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3 text-sm font-medium text-midnight focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 outline-none transition-all shadow-sm">
                            </div>
                        </div>

                        <div class="relative group flex-1 min-h-[300px]">
                            <div class="absolute inset-x-0 bottom-0 h-10 bg-gradient-to-t from-slate-50 to-transparent pointer-events-none rounded-b-2xl"></div>
                            <pre x-ref="promptText" class="absolute inset-0 text-[11px] text-slate-600 bg-slate-50 border border-slate-100 rounded-2xl p-5 overflow-y-auto font-mono whitespace-pre-wrap leading-[1.8] shadow-inner selection:bg-orange-500/20">SUJET DU GUIDE : <span x-text="subject || '[INSÉRER LE SUJET]'" class="text-orange-600 font-bold bg-orange-500/10 px-1 rounded"></span>
MÉTIER CIBLE : <span x-text="target || '[INSÉRER LA CIBLE]'" class="text-orange-600 font-bold bg-orange-500/10 px-1 rounded"></span>
PROMESSE DU GUIDE : <span x-text="promise || '[INSÉRER LA PROMESSE]'" class="text-orange-600 font-bold bg-orange-500/10 px-1 rounded"></span>

TON RÔLE :
Tu es un consultant expert en développement commercial B2B pour les TPE du bâtiment. Ton style est tranchant, professionnel, extrêmement précis et riche en valeur. Tu ne fais pas de la littérature : tu livres des méthodes prêtes à l'emploi, des chiffres, des scripts et des processus étape par étape. Tu parles "vrai" (tu tutoies l'artisan) et tu utilises son vocabulaire quotidien (devis, marge, fournisseur, chantier, SAV).

TON OBJECTIF :
Rédiger un guide gratuit (Lead Magnet) très long, exhaustif et de haute qualité (minimum 1000 mots). L'artisan qui lit ce guide doit se dire : "Si son contenu gratuit est aussi puissant, son logiciel payant doit être incroyable".

STRUCTURE DU GUIDE (Respecte impérativement cet ordre) :
1. L'Électrochoc (Introduction) : Expose la situation actuelle et la douleur financière ou mentale de la cible face au SUJET.
2. Le Cœur de la Méthode (La Valeur) : Divise cette partie en 3 ou 4 grandes étapes numérotées (&lt;h2&gt;). Pour chaque étape, donne des actions immédiates à réaliser, des erreurs à éviter, et des exemples de phrases ou de situations concrètes.
3. Le "Cheat Code" Postelio (La Transition) : Explique que la méthode donnée est redoutable, mais qu'elle demande du temps. Montre ensuite (avec la balise &lt;h3&gt;) comment Postelio automatise cette stratégie grâce à l'IA vocale et justifie le passage à l'action.
4. Conclusion & CTA : Un appel à l'action final massif pour créer son compte et publier son premier post.

CONSIGNES POUR LE CONTENU HTML :
Longueur : Minimum 1000 à 1500 mots. Sois généreux en détails et en exemples.
Format : HTML brut. Pas de balises &lt;html&gt;, &lt;head&gt; ou &lt;body&gt;.
Balises autorisées : &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;blockquote&gt;.
Interdiction absolue : N'utilise JAMAIS de balise &lt;h1&gt;.

FORMAT DE SORTIE EXIGÉ (JSON) :
Réponds impérativement et UNIQUEMENT en JSON valide :
{
"titre_guide": "Titre du guide ultra-prometteur, max 70 caractères",
"sous_titre": "Une phrase d'accroche (sous-titre) qui détaille la promesse, max 120 caractères",
"meta_description": "Description pour Google, max 150 caractères",
"contenu_html": "Le texte complet, riche et formaté en HTML brut (Minimum 1000 mots)."
}</pre>
                        </div>
                    </div>
                    
                    {{-- Right side: Paste Result --}}
                    <div class="space-y-5 flex flex-col pt-6 lg:pt-0 lg:border-l lg:border-slate-100 lg:pl-8 h-full">
                        <div class="flex items-center justify-between shrink-0">
                            <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.15em] flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-orange-500/20 text-orange-600 flex items-center justify-center text-xs">2</span>
                                Coller la réponse JSON
                            </h4>
                        </div>
                        
                        <div class="relative flex-1 min-h-[250px] lg:min-h-0">
                            <textarea wire:model="json_pasted"
                                      @input="extractCoverTitle($event.target.value)"
                                      placeholder="Collez le code JSON ici..."
                                      class="absolute inset-0 w-full h-full bg-slate-900 text-green-400/90 border-0 rounded-2xl p-6 text-xs sm:text-sm font-mono focus:ring-4 focus:ring-orange-500/30 transition-shadow outline-none resize-none shadow-inner shadow-slate-950/50 selection:bg-orange-500/30"></textarea>
                            
                            <div wire:loading wire:target="processPastedJson" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="animate-spin h-8 w-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>
                        @error('json_pasted') <span class="text-rose-500 text-[11px] font-bold tracking-tight bg-rose-50 px-3 py-1 rounded-lg shrink-0">{{ $message }}</span> @enderror
                        
                        <button wire:click="processPastedJson"
                                class="w-full shrink-0 py-4 rounded-2xl bg-midnight text-white font-black text-sm uppercase tracking-[0.1em] hover:bg-slate-800 transition-all shadow-xl shadow-midnight/10 flex flex-row items-center justify-center gap-2 group">
                             <div class="flex items-center gap-2">
                                 <svg wire:loading wire:target="processPastedJson" class="animate-spin h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                 <svg wire:loading.remove wire:target="processPastedJson" class="w-5 h-5 text-orange-500 group-hover:-rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                 <span wire:loading.remove wire:target="processPastedJson">Importer le Guide</span>
                                 <span wire:loading wire:target="processPastedJson">Analyse en cours...</span>
                             </div>
                        </button>
                    </div>
                </div>

                {{-- Cover Generator Section --}}
                <div class="px-8 pb-8 shrink-0">
                    <div class="pt-8 border-t border-slate-100 flex flex-col lg:flex-row items-center gap-8">
                        <div class="flex-1 w-full space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.15em] flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-orange-500/20 text-orange-600 flex items-center justify-center text-xs">3</span>
                                    Générer la Couverture (Format Livre)
                                </h4>
                                <div class="flex items-center gap-2">
                                    <button @click="exportCover()" :disabled="isExporting || !coverTitle"
                                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg bg-orange-500 hover:bg-orange-600 shadow-orange-500/20 disabled:opacity-50">
                                        <svg x-show="!isExporting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <svg x-show="isExporting" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        <span x-text="isExporting ? 'Génération...' : 'Télécharger PNG'"></span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="relative flex items-center gap-2">
                                <input type="text" x-model="coverTitle" placeholder="Titre percutant (*mot* pour l'effet)..."
                                       class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-4 text-sm font-medium text-midnight focus:border-orange-500 focus:ring-2 focus:ring-orange-500/10 outline-none transition-all">
                                <button @click="simplifyTitle()"
                                        :disabled="isSimplifying || !coverTitle"
                                        title="Simplifier avec l'IA"
                                        class="shrink-0 flex items-center gap-1.5 px-4 py-4 rounded-xl bg-orange-50 border border-orange-200 text-orange-600 text-xs font-bold hover:bg-orange-100 transition-all disabled:opacity-40 whitespace-nowrap">
                                    <svg x-show="!isSimplifying" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
                                    <svg x-show="isSimplifying" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <span x-text="isSimplifying ? '' : 'IA'"></span>
                                </button>
                            </div>
                            
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Aperçu en temps réel (Format Vertical 3:4)</p>
                        </div>

                        {{-- Final Preview --}}
                        <div class="w-full lg:w-[320px] shrink-0">
                            <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-2xl bg-slate-100 cover-preview-container" style="aspect-ratio: 3/4">
                                <div class="absolute inset-0 scale-[0.25] origin-top-left">
                                    <div class="cover-canvas-wrapper" x-ref="coverCanvas">
                                        <div class="cover-spine"></div>
                                        <div class="cover-glow"></div>
                                        <div class="cover-content">
                                            <div class="cover-header">
                                                <div class="cover-logo">
                                                    <div class="cover-logo-icon">
                                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="12" cy="12" r="12" fill="url(#logoGrad)"/>
                                                            <line x1="7" y1="10" x2="7" y2="14" stroke="white" stroke-width="1.2" stroke-linecap="round"/>
                                                            <line x1="9.5" y1="8" x2="9.5" y2="16" stroke="white" stroke-width="1.2" stroke-linecap="round"/>
                                                            <line x1="12" y1="6" x2="12" y2="18" stroke="white" stroke-width="1.2" stroke-linecap="round"/>
                                                            <line x1="14.5" y1="8.5" x2="14.5" y2="15.5" stroke="white" stroke-width="1.2" stroke-linecap="round"/>
                                                            <line x1="17" y1="10.5" x2="17" y2="13.5" stroke="white" stroke-width="1.2" stroke-linecap="round"/>
                                                            <defs>
                                                                <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                                                    <stop offset="0%" stop-color="#FBBF24"/>
                                                                    <stop offset="100%" stop-color="#F97316"/>
                                                                </linearGradient>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <div class="cover-logo-text">Postelio</div>
                                                </div>
                                            </div>
                                            <div class="cover-title-wrap">
                                                <h1 class="cover-title-text" x-html="formatTitle(coverTitle) || 'Titre du Guide'"></h1>
                                            </div>
                                            <div class="cover-footer">
                                                <div class="cover-signature">Par {{ auth()->user()->name ?? 'Kylian Dev' }}</div>
                                                <div class="cover-domain">postelio.fr</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('styles')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
        <style>
            .cover-canvas-wrapper {
                width: 1200px;
                height: 1600px;
                background: #0B1120;
                position: relative;
                overflow: hidden;
                font-family: 'Outfit', sans-serif;
                box-sizing: border-box;
            }

            .cover-spine {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                width: 60px;
                background: linear-gradient(to right, rgba(0,0,0,0.4), transparent);
                border-right: 1px solid rgba(255,255,255,0.05);
                z-index: 2;
            }

            .cover-glow {
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at 70% 30%, rgba(249, 115, 22, 0.15) 0%, transparent 70%);
                z-index: 1;
            }

            .cover-content {
                position: relative;
                z-index: 3;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 100px 120px 100px 180px;
            }

            .cover-logo {
                position: relative;
                height: 90px;
                margin-bottom: 50px;
            }

            .cover-logo-icon {
                position: absolute;
                left: 0;
                top: 0;
                width: 90px;
                height: 90px;
            }

            .cover-logo-text {
                position: absolute;
                left: 115px;
                top: -15px; /* Manually recalibrated for better visual center */
                color: white;
                font-size: 80px;
                font-weight: 700;
                line-height: 90px;
                font-family: 'Outfit', sans-serif;
                margin: 0;
            }

            .cover-title-text {
                color: #F8FAFC;
                font-family: 'Outfit', sans-serif !important;
                font-size: 110px;
                font-weight: 700;
                line-height: 1.1;
                letter-spacing: -0.03em;
                margin: 0;
                text-transform: none;
            }

            .cover-title-text span {
                background: linear-gradient(90deg, #F59E0B, #FB923C);
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                color: transparent;
                font-style: italic;
                font-weight: 800;
                display: inline-block;
                padding-right: 0.1em;
                margin-right: 0.08em;
                vertical-align: baseline;
            }

            .cover-signature {
                color: white;
                font-size: 48px;
                font-weight: 700;
                margin-bottom: 15px;
            }

            .cover-domain {
                color: rgba(255,255,255,0.4);
                font-size: 28px;
                font-weight: 600;
                letter-spacing: 0.05em;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('guideModal', (wire) => ({
                    subject: '',
                    target: '',
                    promise: '',
                    coverTitle: '',
                    copied: false,
                    isExporting: false,
                    isSimplifying: false,

                    initModal(wireRef) {
                        this.$wire = wireRef;
                    },

                    copyPrompt() {
                        let text = this.$refs.promptText.innerText;
                        text = text.replace('[INSÉRER LE SUJET]', this.subject || '[VOTRE SUJET]');
                        text = text.replace('[INSÉRER LA CIBLE]', this.target || '[VOTRE CIBLE]');
                        text = text.replace('[INSÉRER LA PROMESSE]', this.promise || '[VOTRE PROMESSE]');
                        navigator.clipboard.writeText(text);
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    },

                    extractCoverTitle(json) {
                        try {
                            const data = JSON.parse(json);
                            this.coverTitle = data.titre_guide || data.titre || '';
                            this.$nextTick(() => this.fitTitle());
                        } catch(e) {}
                    },

                    fitTitle() {
                        const el = this.$refs.coverTitle;
                        if (!el) return;
                        const wrapper = el.parentElement;
                        const maxH = wrapper ? wrapper.offsetHeight : 800;
                        let size = 110;
                        el.style.fontSize = size + 'px';
                        while (size > 40 && el.scrollHeight > maxH) {
                            size -= 5;
                            el.style.fontSize = size + 'px';
                        }
                    },

                    simplifyTitle() {
                        this.isSimplifying = true;
                        this.$wire.simplifyGuideCoverTitle(this.coverTitle).then(r => {
                            this.coverTitle = r.title;
                            this.isSimplifying = false;
                            this.$nextTick(() => this.fitTitle());
                        }).catch(() => this.isSimplifying = false);
                    },

                    formatTitle(text) {
                        if (!text) return '';
                        return text.replace(/\*(.*?)\*/g, '<span>$1</span>');
                    },

                    async exportCover() {
                        if (this.isExporting) return;
                        this.isExporting = true;
                        const source = this.$refs.coverCanvas;
                        if (!source) { this.isExporting = false; return; }

                        // Clone to bypass scaling issues
                        const clone = source.cloneNode(true);
                        const wrap = document.createElement('div');
                        wrap.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1200px;height:1600px;overflow:hidden;pointer-events:none;';
                        wrap.appendChild(clone);
                        document.body.appendChild(wrap);

                        // Fallback for html2canvas (gradient text-clip doesn't work well)
                        clone.querySelectorAll('.cover-title-text span').forEach(span => {
                            // Brighter, warmer orange from the blog components
                            span.style.cssText = 'background:none !important;-webkit-text-fill-color:#FB923C !important;color:#FB923C !important;font-style:italic !important;display:inline !important;padding-right:0.1em;';
                        });

                        // Forced Absolute Alignment for the Export
                        const cloneLogo = clone.querySelector('.cover-logo');
                        if (cloneLogo) {
                            cloneLogo.style.position = 'relative';
                            cloneLogo.style.height = '90px';
                            cloneLogo.style.display = 'block';
                            
                            const iconWrap = cloneLogo.querySelector('.cover-logo-icon');
                            const textWrap = cloneLogo.querySelector('.cover-logo-text');
                            
                            if (iconWrap) {
                                iconWrap.style.position = 'absolute';
                                iconWrap.style.left = '0';
                                iconWrap.style.top = '0';
                                iconWrap.style.width = '90px';
                                iconWrap.style.height = '90px';
                            }
                            if (textWrap) {
                                textWrap.style.position = 'absolute';
                                textWrap.style.left = '115px';
                                textWrap.style.top = '-35px'; /* Final aggressive lift for the export */
                                textWrap.style.height = '90px';
                                textWrap.style.lineHeight = '90px';
                                textWrap.style.marginTop = '0';
                                textWrap.style.display = 'block';
                            }
                        }

                        try {
                            await document.fonts.ready;
                            const canvas = await html2canvas(clone, {
                                width: 1200,
                                height: 1600,
                                scale: 1,
                                useCORS: true,
                                backgroundColor: '#0B1120'
                            });
                            
                            const link = document.createElement('a');
                            link.download = `couverture-guide-${Date.now()}.png`;
                            link.href = canvas.toDataURL('image/png');
                            link.click();
                        } catch (err) {
                            console.error('Export error:', err);
                        } finally {
                            document.body.removeChild(wrap);
                            this.isExporting = false;
                        }
                    }
                }));
            });
        </script>
    @endpush
</div>
