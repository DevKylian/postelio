<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display font-bold text-2xl text-midnight tracking-tight leading-tight">
                    Gestion du Blog
                </h1>
                <p class="text-slate-400 text-sm mt-1 font-medium">
                    Créez et gérez vos articles de blog.
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

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4">
            <div class="relative w-full sm:w-80">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un article..."
                       class="w-full pl-11 pr-4 py-2.5 bg-white/50 border-slate-100 rounded-2xl text-sm font-medium text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
            </div>
            <div class="flex items-center gap-3">
                @if(!$showForm)
                <button wire:click="openAIModal" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-lg bg-gradient-to-r from-copper to-amber-500 text-white hover:shadow-copper/30 hover:-translate-y-0.5 border border-copper/20 active:translate-y-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Générer avec l'IA
                </button>
                @endif
                <button wire:click="toggleForm"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-lg {{ $showForm ? 'bg-slate-100 text-slate-600 hover:bg-slate-200 shadow-slate-200/50' : 'bg-midnight text-white hover:bg-slate-800 shadow-slate-900/10' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}" />
                    </svg>
                    {{ $showForm ? 'Fermer' : 'Nouvel article' }}
                </button>
            </div>
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
                    {{ $isEditing ? 'Modifier l\'article' : 'Nouvel article' }}
                </h2>

                <form wire:submit.prevent="save" class="space-y-8 relative z-10">
                    {{-- Title --}}
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Titre de l'article</label>
                        <input type="text" wire:model="title" placeholder="Un titre accrocheur pour votre article..."
                               class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                        @error('title') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Featured Image --}}
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Image de couverture</label>
                        <div class="flex items-center gap-6">
                            @if($featured_image_upload)
                                <img src="{{ $featured_image_upload->temporaryUrl() }}" alt="Preview" class="w-40 object-cover rounded-xl border border-slate-100" style="aspect-ratio: 16/8">
                            @elseif($existing_featured_image)
                                <img src="{{ Storage::url($existing_featured_image) }}" alt="Current" class="w-40 object-cover rounded-xl border border-slate-100" style="aspect-ratio: 16/8">
                            @else
                                <div class="w-40 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center" style="aspect-ratio: 16/8">
                                    <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <input type="file" wire:model="featured_image_upload" accept="image/*"
                                   class="text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-2xl file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-copper/10 file:text-copper hover:file:bg-copper/20 transition-all">
                        </div>
                        @error('featured_image_upload') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Excerpt --}}
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Extrait <span class="text-slate-300 normal-case">(optionnel — généré automatiquement si vide)</span></label>
                        <textarea wire:model="excerpt" rows="2" placeholder="Résumé court de l'article pour les listes et le SEO..."
                                  class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none resize-none"></textarea>
                        @error('excerpt') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Content (CKEditor 5 + HTML Mode) --}}
                    <div class="space-y-4"
                         x-data="{
                            content: @entangle('content'),
                            htmlMode: false,
                            editor: null,
                            init() {
                                if (typeof ClassicEditor === 'undefined') return;
                                ClassicEditor
                                    .create(this.$refs.editorArea, {
                                        toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo' ],
                                        heading: {
                                            options: [
                                                { model: 'paragraph', title: 'Paragraphe', class: 'ck-heading_paragraph' },
                                                { model: 'heading1', view: 'h2', title: 'Titre Principal (H2)', class: 'ck-heading_heading1' },
                                                { model: 'heading2', view: 'h3', title: 'Sous-titre (H3)', class: 'ck-heading_heading2' }
                                            ]
                                        }
                                    })
                                    .then(editor => {
                                        this.editor = editor;
                                        // Set initial data
                                        editor.setData(this.content || '');
                                        // Sync to Alpine/Livewire on change
                                        editor.model.document.on('change:data', () => {
                                            this.content = editor.getData();
                                        });

                                        // Watch for external content changes (Livewire -> Editor)
                                        this.$watch('content', value => {
                                            if (!this.htmlMode && value !== editor.getData()) {
                                                editor.setData(value || '');
                                            }
                                        });
                                    })
                                    .catch(error => { console.error(error); });
                            }
                         }"
                         x-on:json-imported.window="htmlMode = true"
                         wire:ignore>
                        <div class="flex items-center justify-between px-1 mb-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Contenu de l'article</label>
                            <button type="button"
                                    @click="
                                        htmlMode = !htmlMode;
                                        if (!htmlMode && editor) {
                                            editor.setData(content);
                                        }
                                    "
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all"
                                    :class="htmlMode ? 'bg-copper/10 text-copper' : 'bg-slate-100 text-slate-400 hover:text-slate-600'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span x-text="htmlMode ? 'Mode visuel' : 'Mode HTML'"></span>
                            </button>
                        </div>

                        <div x-show="!htmlMode" class="ck-editor-container">
                            <div x-ref="editorArea" class="prose prose-slate max-w-none"></div>
                        </div>

                        <div x-show="htmlMode" x-cloak>
                            <textarea x-model="content"
                                      placeholder="Collez votre code HTML ici..."
                                      class="w-full bg-slate-900 text-green-400 border-slate-700 rounded-[2rem] p-8 text-sm font-mono focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none leading-relaxed min-h-[500px] resize-y shadow-2xl shadow-slate-900/20"></textarea>
                            <div class="mt-2 px-4 py-2 bg-amber-50 rounded-xl border border-amber-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wider">Note : Modifiez directement le code pour un contrôle total sur le formattage.</p>
                            </div>
                        </div>
                        @error('content') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- SEO Section --}}
                    <div class="space-y-6 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest">Optimisation SEO</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Meta Title <span class="text-slate-300 normal-case">(max 70 car.)</span></label>
                                <input type="text" wire:model="meta_title" maxlength="70" placeholder="Titre pour Google (généré auto si vide)"
                                       class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-medium text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                                @error('meta_title') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Mots-clés <span class="text-slate-300 normal-case">(séparés par des virgules)</span></label>
                                <input type="text" wire:model="meta_keywords" placeholder="artisan, réseaux sociaux, marketing..."
                                       class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-medium text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                                @error('meta_keywords') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Meta Description <span class="text-slate-300 normal-case">(max 160 car.)</span></label>
                            <textarea wire:model="meta_description" rows="2" maxlength="160" placeholder="Description pour Google (générée auto si vide)"
                                      class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-medium text-slate-600 focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none resize-none"></textarea>
                            @error('meta_description') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- SEO Preview --}}
                        <div class="mt-4 p-4 bg-white rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-3">Aperçu Google</p>
                            <div class="space-y-1">
                                <p class="text-[#1a0dab] text-base font-medium truncate">{{ $meta_title ?: ($title ?: 'Titre de l\'article') }} — Postelio Blog</p>
                                <p class="text-[#006621] text-xs truncate">{{ url('/blog') }}/{{ \Illuminate\Support\Str::slug($title ?: 'slug-article') }}</p>
                                <p class="text-[#545454] text-xs line-clamp-2">{{ $meta_description ?: ($excerpt ?: 'La description de votre article apparaîtra ici...') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Status & Date --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Statut</label>
                            <select wire:model="status" class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                                <option value="draft">Brouillon</option>
                                <option value="published">Publié</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Date de publication</label>
                            <input type="date" wire:model="published_at"
                                   class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" wire:click="toggleForm"
                                class="px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest text-slate-500 bg-slate-100 hover:bg-slate-200 transition-all">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-copper text-white px-10 py-4 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-copper/20 active:scale-95 disabled:opacity-50">
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
                <h2 class="font-display font-bold text-midnight text-lg tracking-tight shrink-0">Tous les articles</h2>
                <div class="h-px bg-slate-100 flex-1"></div>
                <span class="text-xs font-bold text-slate-400">{{ $posts->total() }} article(s)</span>
            </div>

            <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/60 shadow-xl shadow-slate-200/30 bg-white/40">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5">Article</th>
                            <th class="px-8 py-5">Statut</th>
                            <th class="px-8 py-5">Date</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($posts as $post)
                        <tr class="group hover:bg-white/60 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    @if($post->featured_image)
                                        <div class="w-24 overflow-hidden rounded-xl border border-slate-100 shrink-0" style="aspect-ratio: 16/8">
                                            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700 [backface-visibility:hidden] [transform:translateZ(0)]">
                                        </div>
                                    @else
                                        <div class="w-24 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0" 
                                             style="aspect-ratio: 16/8">
                                            <svg class="w-5 h-5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-midnight text-sm truncate">{{ $post->title }}</h3>
                                        <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $post->excerpt }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <button wire:click="toggleStatus({{ $post->id }})" class="cursor-pointer">
                                    @if($post->status === 'published')
                                        <span class="badge badge-success gap-1.5 !text-[10px] uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Publié
                                        </span>
                                    @else
                                        <span class="badge badge-gray gap-1.5 !text-[10px] uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                            Brouillon
                                        </span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-[11px] font-bold text-slate-500 tracking-tight">
                                    {{ $post->published_at ? $post->published_at->translatedFormat('d F Y') : $post->created_at->translatedFormat('d F Y') }}
                                </div>
                                <div class="text-[10px] text-slate-300 mt-0.5">
                                    {{ $post->read_time }} min de lecture
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if($post->status === 'published')
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                       class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:bg-blue-500 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    @endif
                                    <button wire:click="edit({{ $post->id }})"
                                            class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:bg-copper hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="confirm('Supprimer cet article ?') || event.stopImmediatePropagation()"
                                            wire:click="delete({{ $post->id }})"
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
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-400 tracking-tight">Aucun article publié.</p>
                                    <button wire:click="toggleForm" class="text-xs font-bold text-copper hover:text-amber-600 transition-colors">
                                        Écrire votre premier article
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($posts->hasPages())
            <div class="px-4">
                {{ $posts->links() }}
            </div>
            @endif
        </section>

        {{-- AI Prompt Modal --}}
        @if($showAIPromptModal)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center"
             x-data="blogModal"
             x-init="initModal($wire)"
             x-on:banner-saved.window="bannerSaved = true; setTimeout(() => bannerSaved = false, 3000)">
            <div class="fixed inset-0 bg-slate-900/45 backdrop-blur-md" wire:click="closeAIModal"></div>
            
            <div class="relative w-full max-w-5xl mx-4 sm:mx-6 bg-white/95 backdrop-blur-md border border-white/40 shadow-2xl rounded-[2.5rem] overflow-hidden flex flex-col h-[90vh] sm:h-auto sm:max-h-[85vh] animate-in zoom-in-95 duration-300">
                <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100 bg-white/50 shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-copper to-amber-500 flex items-center justify-center text-white shadow-lg shadow-copper/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-xl text-midnight">Générateur d'Article IA</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Prompt et Importation JSON</p>
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
                                <span class="w-5 h-5 rounded-full bg-copper/20 text-copper flex items-center justify-center text-xs">1</span>
                                Le Prompt à copier
                            </h4>
                            <button @click="
                                let text = $refs.promptText.innerText;
                                text = text.replace('[INSÉRER LE TITRE DU TABLEAU]', subject || '[VOTRE SUJET]');
                                text = text.replace('[INSÉRER L\'AXE DU TABLEAU]', axe || '[VOTRE AXE]');
                                navigator.clipboard.writeText(text);
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            " class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all shadow-sm"
                            :class="copied ? 'bg-green-100 text-green-600 border border-green-200' : 'bg-white text-slate-600 border border-slate-200 hover:border-copper/50 hover:text-copper'">
                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                <svg x-cloak x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                <span x-text="copied ? 'Copié !' : 'Copier'"></span>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input type="text" x-model="subject" placeholder="Sujet de l'article (Titre du tableau)" 
                                   class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3 text-sm font-medium text-midnight focus:border-copper focus:ring-2 focus:ring-copper/20 outline-none transition-all shadow-sm">
                            <input type="text" x-model="axe" placeholder="Axe de rédaction (Axe du tableau)" 
                                   class="w-full bg-white border border-slate-200 rounded-xl px-5 py-3 text-sm font-medium text-midnight focus:border-copper focus:ring-2 focus:ring-copper/20 outline-none transition-all shadow-sm">
                        </div>

                        <div class="relative group flex-1 min-h-[300px]">
                            <div class="absolute inset-x-0 bottom-0 h-10 bg-gradient-to-t from-slate-50 to-transparent pointer-events-none rounded-b-2xl"></div>
                            <pre x-ref="promptText" class="absolute inset-0 text-[11px] sm:text-xs text-slate-600 bg-slate-50 border border-slate-100 rounded-2xl p-5 overflow-y-auto font-mono whitespace-pre-wrap leading-[1.8] shadow-inner selection:bg-copper/20">SUJET DE L'ARTICLE : <span x-text="subject || '[INSÉRER LE TITRE DU TABLEAU]'" class="text-copper font-bold bg-copper/10 px-1 rounded"></span>
AXE DE RÉDACTION (PAS) : <span x-text="axe || '[INSÉRER L\'AXE DU TABLEAU]'" class="text-copper font-bold bg-copper/10 px-1 rounded"></span>

TON RÔLE :
Tu es un copywriter web d'élite spécialisé dans le B2B pour les artisans du bâtiment (plombiers, électriciens, menuisiers, paysagistes...). Tu parles "vrai". Pas de jargon marketing, pas de "bullshit corporate". Tu t'adresses directement à l'artisan d'humain à humain (tutoie-le, sois percutant, utilise son vocabulaire : chantier, devis, matos, fin de journée, galère).

Ton lecteur rentre à 19h, il est crevé. Il n'a pas le temps pour les théories. Il veut comprendre en 2 minutes comment arrêter de brader ses prix et comment gagner du temps.

TON OBJECTIF :
Rédiger un article de blog complet qui utilise le framework PAS pour amener logiquement le lecteur à tester Postelio.

STRUCTURE DE L'ARTICLE (Respecte impérativement cet axe) :
Suis scrupuleusement la logique suivante basée sur l'AXE DE RÉDACTION fourni :

Problème (Problem) : Attaque direct sur la douleur mentionnée dans l'axe. Pas d'introduction "Bonjour". On entre dans le vif du sujet.

Agitation (Agitate) : Remue le couteau. Parle des conséquences (perte de fric, soirées gâchées, fatigue, clients qui négocient tout).

[CTA] : Place la balise sur une ligne seule.

Solution (Solution) : Donne 3 conseils concrets de "vieux loup de mer" que l'artisan peut appliquer demain. Puis, montre comment Postelio automatise tout ça pour lui.

CONSIGNES POUR LE CONTENU HTML :
Longueur : Minimum 600 mots.
Format : HTML brut.
Balises : &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;.
Interdiction : Pas de balise &lt;h1&gt;.

FORMAT DE SORTIE EXIGÉ (JSON) :
Réponds UNIQUEMENT en JSON valide :

{
"titre": "Titre percutant, max 60 caractères",
"titre_miniature": "Reformule le titre de l'article en une phrase courte (5 à 8 mots) qui dit clairement de quoi parle l'article. Pas de deux-points, pas de point d'interrogation, pas de style teaser vague. Écris une affirmation directe. Tu peux mettre *un mot clé* entre astérisques pour l'accentuer visuellement. Exemples pour 'Devis trop cher : comment convaincre ton client ?' → 'Vendre ton *devis* même quand il est cher' ou 'Convaincre un client face à un devis élevé'",
"extrait": "2 phrases choc pour accrocher le lecteur",
"meta_title": "Titre SEO optimisé, max 60 caractères",
"meta_description": "Description sans guillemets ni HTML, max 150 caractères",
"mots_cles": ["mot-cle-1", "mot-cle-2", "mot-cle-3", "mot-cle-4", "mot-cle-5"],
"contenu_html": "Le texte complet (min 600 mots) avec le shortcode [CTA] au milieu."
}</pre>
                        </div>
                    </div>
                    
                    {{-- Right side: Paste Result --}}
                    <div class="space-y-5 flex flex-col pt-6 lg:pt-0 lg:border-l lg:border-slate-100 lg:pl-8 h-full">
                        <div class="flex items-center justify-between shrink-0">
                            <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.15em] flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-copper/20 text-copper flex items-center justify-center text-xs">2</span>
                                Coller la réponse JSON
                            </h4>
                        </div>
                        
                        <div class="relative flex-1 min-h-[250px] lg:min-h-0">
                            <textarea wire:model="json_pasted" placeholder="Collez le code JSON ici...
{
  &quot;titre&quot;: &quot;...&quot;,
  ...
}"
                                      class="absolute inset-0 w-full h-full bg-slate-900 text-green-400/90 border-0 rounded-2xl p-6 text-xs sm:text-sm font-mono focus:ring-4 focus:ring-copper/30 transition-shadow outline-none resize-none shadow-inner shadow-slate-950/50 selection:bg-copper/30"></textarea>
                            
                            <div wire:loading wire:target="processPastedJson" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="animate-spin h-8 w-8 text-copper" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>
                        @error('json_pasted') <span class="text-rose-500 text-[11px] font-bold tracking-tight bg-rose-50 px-3 py-1 rounded-lg shrink-0">{{ $message }}</span> @enderror
                        
                        <button wire:click="processPastedJson"
                                class="w-full shrink-0 py-4 rounded-2xl bg-midnight text-white font-black text-sm uppercase tracking-[0.1em] hover:bg-slate-800 transition-all shadow-xl shadow-midnight/10 flex flex-col items-center justify-center gap-1 group">
                             <div class="flex items-center gap-2">
                                 <svg class="w-5 h-5 text-copper group-hover:-rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                 Importer dans l'éditeur
                             </div>
                        </button>
                    </div>
                </div>

                {{-- Banner Section --}}
                <div class="px-8 pb-8 shrink-0">
                    <div class="pt-8 border-t border-slate-100 flex flex-col lg:flex-row items-center gap-8">
                        <div class="flex-1 w-full space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.15em] flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-copper/20 text-copper flex items-center justify-center text-xs">3</span>
                                    Générer la bannière
                                </h4>
                                <div class="flex items-center gap-2">
                                    <button @click="applyBanner()"
                                            :disabled="isExporting || !bannerTitle"
                                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg disabled:opacity-50"
                                            :class="bannerSaved ? 'bg-green-500 shadow-green-500/20' : 'bg-copper hover:bg-amber-600 shadow-copper/20'">
                                        <svg x-show="!isExporting && !bannerSaved" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <svg x-show="isExporting" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        <span x-text="isExporting ? 'Génération...' : (bannerSaved ? 'Appliquée !' : 'Appliquer à l\'article')"></span>
                                    </button>
                                    <button @click="exportBanner()" :disabled="isExporting" title="Télécharger en PNG"
                                            class="p-2.5 rounded-xl border border-slate-200 text-slate-400 hover:text-copper hover:border-copper/30 transition-all disabled:opacity-40">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="relative flex items-center gap-2">
                                <input type="text" x-model="bannerTitle" placeholder="Titre de la bannière (*mot* pour l'effet orange)..."
                                       class="w-full bg-slate-50 border border-slate-100 rounded-xl px-5 py-4 text-sm font-medium text-midnight focus:border-copper focus:ring-2 focus:ring-copper/10 outline-none transition-all">
                                <button @click="
                                            isSimplifying = true;
                                            $wire.simplifyBannerTitle(bannerTitle).then(r => {
                                                bannerTitle = r.title;
                                                isSimplifying = false;
                                                $nextTick(() => fitTitle());
                                            }).catch(() => isSimplifying = false);
                                        "
                                        :disabled="isSimplifying || !bannerTitle"
                                        title="Générer un titre simplifié avec l'IA"
                                        class="shrink-0 flex items-center gap-1.5 px-4 py-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 text-xs font-bold hover:bg-amber-100 transition-all disabled:opacity-40 whitespace-nowrap">
                                    <svg x-show="!isSimplifying" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
                                    <svg x-show="isSimplifying" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <span x-text="isSimplifying ? '' : 'IA'"></span>
                                </button>
                            </div>
                            
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Aperçu en temps réel (1200x630)</p>
                        </div>

                        {{-- Final Preview --}}
                        <div class="w-full lg:w-[480px] shrink-0">
                            <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100 banner-preview-container" style="aspect-ratio: 16/9">
                                <div class="absolute inset-0 scale-[0.4] origin-top-left">
                                    <div class="banner-canvas-wrapper" x-ref="bannerCanvas">
                                        <div class="banner-glow banner-glow-1"></div>
                                        <div class="banner-glow banner-glow-2"></div>
                                        <div class="banner-accent-line"></div>

                                        <div class="banner-content-inner">
                                            <div class="banner-top-row">
                                                <div class="banner-badge"><span class="banner-badge-dot">●</span> Article de blog</div>
                                                <div class="banner-domain">postelio.fr</div>
                                            </div>

                                            <div class="banner-title-text-wrap">
                                                <h1 class="banner-title-text" x-ref="bannerTitle" x-html="formatBannerTitle(bannerTitle) || 'Titre de votre article'"></h1>
                                            </div>

                                            <div class="banner-bottom-bar">
                                                <span class="banner-bottom-line">——</span><span class="banner-bottom-label">Blog</span>
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

    <style>
        .animate-in { animation: animate-in 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes animate-in {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @push('styles')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
        <style>
            /* --- Banner Generator Internal Styles --- */
            .banner-canvas-wrapper {
                width: 1200px;
                height: 630px;
                background: #0B1120;
                position: relative;
                overflow: hidden;
                border-radius: 0;
                transform-origin: top left;
                font-family: 'Outfit', sans-serif;
                box-sizing: border-box;
            }

            /* Ambient warm glow top-right */
            .banner-glow {
                position: absolute;
                border-radius: 50%;
                pointer-events: none;
            }

            .banner-glow-1 {
                width: 700px;
                height: 700px;
                top: -180px;
                right: -100px;
                background: radial-gradient(circle, rgba(245, 158, 11, 0.13) 0%, transparent 70%);
            }

            .banner-glow-2 {
                width: 400px;
                height: 400px;
                bottom: -120px;
                left: 60px;
                background: radial-gradient(circle, rgba(251, 146, 60, 0.07) 0%, transparent 70%);
            }

            /* Ligne copper pleine largeur en haut */
            .banner-accent-line {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(to right, #F59E0B, #FB923C);
            }

            .banner-content-inner {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 72px 88px 68px 88px;
                box-sizing: border-box;
            }

            .banner-top-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
            }

            .banner-badge {
                color: #F59E0B;
                font-size: 16px;
                font-weight: 700;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                font-family: 'Outfit', sans-serif;
                line-height: 1;
            }

            .banner-badge-dot {
                font-size: 10px;
                margin-right: 4px;
            }

            .banner-domain {
                color: rgba(255, 255, 255, 0.22);
                font-size: 16px;
                font-weight: 600;
                letter-spacing: 0.12em;
                font-family: 'Outfit', sans-serif;
                text-transform: uppercase;
                line-height: 1;
            }

            .banner-title-text-wrap {
                flex: 1;
                display: flex;
                align-items: center;
            }

            .banner-title-text {
                color: #F8FAFC;
                font-family: 'Outfit', sans-serif !important;
                font-size: 90px;
                font-weight: 700;
                text-align: left;
                line-height: 1.1;
                letter-spacing: -0.03em;
                margin: 0;
                max-width: 1000px;
                word-break: break-word;
                overflow-wrap: break-word;
                display: block;
                box-sizing: border-box;
            }

            .banner-title-text span {
                background: linear-gradient(90deg, #F59E0B, #FB923C);
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                color: transparent;
                font-style: italic;
                font-weight: 800;
                display: inline-block;
                padding-right: 0.12em;
                vertical-align: baseline;
            }

            .banner-bottom-bar {
                font-family: 'Outfit', sans-serif;
                font-size: 15px;
                font-weight: 600;
                line-height: 1;
            }

            .banner-bottom-line {
                color: rgba(245, 158, 11, 0.7);
                letter-spacing: -0.05em;
                margin-right: 14px;
            }

            .banner-bottom-label {
                color: rgba(255, 255, 255, 0.3);
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }


            /* Editor specific styles */
            .ck-editor__separator { display: none !important; }
            .ck-editor__editable {
                border-radius: 0 0 2rem 2rem !important;
                background: white !important;
                padding: 1.5rem 2.5rem !important;
                min-height: 500px !important;
                border-color: #f1f5f9 !important;
                font-size: 0.95rem !important;
                line-height: 1.8 !important;
                color: #475569 !important;
            }
            .ck-toolbar {
                border-radius: 2rem 2rem 0 0 !important;
                background: #f8fafc !important;
                border-color: #f1f5f9 !important;
                padding: 0.5rem 1rem !important;
            }
            .ck-editor__editable:focus {
                border-color: #f59e0b !important;
                box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1) !important;
                outline: none !important;
            }
            .ck-content h2 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-top: 2rem; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; }
            .ck-content h3 { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-top: 1.5rem; margin-bottom: 0.75rem; }
            .ck-content p { margin-bottom: 1rem; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('blogModal', () => ({
                copied: false,
                subject: '',
                axe: '',
                bannerTitle: '',
                isExporting: false,
                isSimplifying: false,
                bannerSaved: false,

                initModal(wire) {
                    // Watch the Livewire property — fires reliably after every server response
                    wire.$watch('banner_title_for_modal', (val) => {
                        if (val) {
                            this.bannerTitle = val;
                            this.$nextTick(() => this.fitTitle());
                        }
                    });
                    // Also watch bannerTitle to update preview on manual edits
                    this.$watch('bannerTitle', () => {
                        this.$nextTick(() => this.fitTitle());
                    });
                },

                formatBannerTitle(text) {
                    if (!text) return '';
                    return text.replace(/\*([^*]+)\*/g, '<span>$1</span>');
                },

                fitTitle() {
                    const el = this.$refs.bannerTitle;
                    if (!el) return;
                    const wrapper = el.parentElement;
                    const maxH = wrapper ? wrapper.offsetHeight : 220;
                    let size = 90;
                    el.style.fontSize = size + 'px';
                    while (size > 32 && el.scrollHeight > maxH) {
                        size -= 4;
                        el.style.fontSize = size + 'px';
                    }
                },

                captureCanvas() {
                    return new Promise((resolve, reject) => {
                        const source = this.$refs.bannerCanvas;
                        if (!source) { reject(new Error('bannerCanvas ref not found')); return; }
                        const clone = source.cloneNode(true);
                        const wrap = document.createElement('div');
                        wrap.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1200px;height:630px;overflow:hidden;pointer-events:none;';
                        wrap.appendChild(clone);
                        document.body.appendChild(wrap);
                        // Replace gradient text with solid colour for html2canvas
                        clone.querySelectorAll('.banner-title-text span').forEach(span => {
                            span.style.cssText = 'background:none;-webkit-text-fill-color:#F59E0B;color:#F59E0B;font-style:italic;font-weight:800;padding-right:0;display:inline;';
                        });
                        document.fonts.ready
                            .then(() => html2canvas(clone, {
                                width: 1200, height: 630, scale: 1,
                                useCORS: true, allowTaint: false,
                                backgroundColor: '#0B1120', logging: false
                            }))
                            .then(canvas => { document.body.removeChild(wrap); resolve(canvas); })
                            .catch(err => { document.body.removeChild(wrap); reject(err); });
                    });
                },

                exportBanner() {
                    if (this.isExporting) return;
                    this.isExporting = true;
                    this.captureCanvas()
                        .then(canvas => {
                            const link = document.createElement('a');
                            link.download = 'banniere-blog.png';
                            link.href = canvas.toDataURL('image/png');
                            link.click();
                        })
                        .catch(err => { console.error('Export error:', err); alert('Erreur export PNG : ' + err.message); })
                        .finally(() => { this.isExporting = false; });
                },

                applyBanner() {
                    if (this.isExporting) return;
                    this.isExporting = true;
                    const wire = this.$wire;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    this.captureCanvas()
                        .then(canvas => new Promise((resolve, reject) => {
                            canvas.toBlob(blob => {
                                if (!blob) { reject(new Error('toBlob a échoué')); return; }
                                const fd = new FormData();
                                fd.append('banner', blob, 'banner.png');
                                fd.append('_token', csrfToken);
                                fetch('/admin/blog/banner-upload', { method: 'POST', body: fd })
                                    .then(r => r.ok ? r.json() : r.text().then(t => Promise.reject(t)))
                                    .then(data => { resolve(data.path); })
                                    .catch(reject);
                            }, 'image/png');
                        }))
                        .then(path => {
                            return wire.setBannerImagePath(path);
                        })
                        .then(() => {
                            this.bannerSaved = true;
                            setTimeout(() => {
                                this.bannerSaved = false;
                                wire.closeAIModal();
                            }, 1500);
                        })
                        .catch(err => { console.error('Banner apply error:', err); alert('Erreur : ' + err); })
                        .finally(() => { this.isExporting = false; });
                },
            }));
        });
        </script>
    @endpush
</div>
