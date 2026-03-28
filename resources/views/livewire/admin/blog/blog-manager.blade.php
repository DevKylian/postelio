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
            <button wire:click="toggleForm"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold transition-all shadow-lg {{ $showForm ? 'bg-slate-100 text-slate-600 hover:bg-slate-200 shadow-slate-200/50' : 'bg-midnight text-white hover:bg-slate-800 shadow-slate-900/10' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}" />
                </svg>
                {{ $showForm ? 'Fermer' : 'Nouvel article' }}
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
                                <img src="{{ $featured_image_upload->temporaryUrl() }}" alt="Preview" class="w-32 h-20 object-cover rounded-xl border border-slate-100">
                            @elseif($existing_featured_image)
                                <img src="{{ Storage::url($existing_featured_image) }}" alt="Current" class="w-32 h-20 object-cover rounded-xl border border-slate-100">
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
                                        <img src="{{ Storage::url($post->featured_image) }}" alt="" class="w-16 h-12 object-cover rounded-xl border border-slate-100 shrink-0">
                                    @else
                                        <div class="w-16 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0">
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
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Publié
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-50 text-slate-400 border border-slate-100">
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
    </div>

    <style>
        .animate-in { animation: animate-in 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes animate-in {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    @push('styles')
        <style>
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
    @endpush
</div>
