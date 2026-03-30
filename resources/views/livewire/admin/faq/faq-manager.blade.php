<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight">Centre d'Aide (FAQ)</h1>
                <p class="text-gray-400 text-sm mt-0.5">Gérez les thèmes et les questions de votre centre d'aide.</p>
            </div>
            <button wire:click="createCategory" class="inline-flex items-center gap-2 px-4 py-2.5 bg-copper text-white rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm shadow-copper/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau Thème
            </button>
        </div>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-10">

    @if($categories->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-midnight">Aucun thème pour le moment</h3>
            <p class="text-gray-400 mt-1 max-w-sm mx-auto">Créez votre première catégorie pour commencer à structurer votre centre d'aide.</p>
            <button wire:click="createCategory" class="mt-6 text-copper font-medium hover:text-amber-600">Créer mon premier thème &rarr;</button>
        </div>
    @else
        <div class="space-y-6">
            @foreach($categories as $category)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" wire:key="cat-{{ $category->id }}">
                {{-- Category Header --}}
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex flex-1 items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center text-copper shrink-0">
                            {!! $category->icon ?: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>' !!}
                        </div>
                        <div>
                            <h3 class="font-bold text-midnight text-lg">{{ $category->name }}</h3>
                            @if($category->description)
                                <p class="text-sm text-gray-400 mt-0.5">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button wire:click="createQuestion({{ $category->id }})" class="px-3 py-1.5 bg-midnight text-white text-xs font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                            + Sujet
                        </button>
                        <button wire:click="editCategory({{ $category->id }})" class="p-1.5 text-gray-400 hover:text-copper transition-colors bg-white rounded-md border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button wire:confirm="Êtes-vous sûr de vouloir supprimer {{ $category->name }} et toutes ses questions ?" wire:click="deleteCategory({{ $category->id }})" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors bg-white rounded-md border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Table of Questions --}}
                <div class="overflow-x-auto">
                    @if($category->questions->isEmpty())
                        <div class="p-6 text-center text-gray-400 text-sm">
                            Aucune question dans ce thème. 
                            <button wire:click="createQuestion({{ $category->id }})" class="text-copper hover:underline font-medium">En ajouter une</button>
                        </div>
                    @else
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <tbody class="divide-y divide-gray-50">
                                @foreach($category->questions as $question)
                                <tr class="hover:bg-gray-50/50 transition-colors group" wire:key="q-{{ $question->id }}">
                                    <td class="px-6 py-4 w-full">
                                        <div class="font-medium text-midnight">{{ $question->title }}</div>
                                        <div class="text-xs text-gray-400 mt-1 truncate max-w-xl">{{ $question->summary ?? 'Pas de résumé' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400">
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-md">Ordre: {{ $question->sort_order }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('help.show', $question->slug) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-blue-500 bg-white border border-gray-100 rounded-md shadow-sm transition-colors" title="Aperçu public">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <button wire:click="editQuestion({{ $question->id }})" class="p-1.5 text-gray-400 hover:text-copper bg-white border border-gray-100 rounded-md shadow-sm transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button wire:confirm="Supprimer ce sujet ?" wire:click="deleteQuestion({{ $question->id }})" class="p-1.5 text-gray-400 hover:text-red-500 bg-white border border-gray-100 rounded-md shadow-sm transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
    </div>

    {{-- MODAL : THÈME --}}
    @if($showCategoryModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center">
        <div class="fixed inset-0 bg-midnight/60 backdrop-blur-sm" wire:click="$set('showCategoryModal', false)"></div>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative z-10 m-4 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                <h3 class="font-bold text-lg text-midnight">{{ $categoryId ? 'Modifier le Thème' : 'Nouveau Thème' }}</h3>
                <button wire:click="$set('showCategoryModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la catégorie <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="categoryName" class="w-full rounded-xl border-gray-200 focus:border-copper focus:ring focus:ring-copper/20 text-sm">
                    @error('categoryName') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (sous-titre)</label>
                    <input type="text" wire:model="categoryDescription" class="w-full rounded-xl border-gray-200 focus:border-copper focus:ring focus:ring-copper/20 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code SVG de l'icône</label>
                    <textarea wire:model="categoryIcon" rows="3" class="w-full rounded-xl border-gray-200 focus:border-copper focus:ring focus:ring-copper/20 text-sm font-mono" placeholder="<svg>...</svg>"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" wire:model="categorySortOrder" class="w-full rounded-xl border-gray-200 focus:border-copper focus:ring focus:ring-copper/20 text-sm">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 shrink-0 rounded-b-2xl">
                <button wire:click="$set('showCategoryModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Annuler</button>
                <button wire:click="saveCategory" class="px-4 py-2 text-sm font-medium text-white bg-copper rounded-lg hover:bg-amber-600 shadow-sm">Enregistrer</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL : QUESTION --}}
    @if($showQuestionModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center">
        <div class="fixed inset-0 bg-midnight/60 backdrop-blur-sm" wire:click="$set('showQuestionModal', false)"></div>
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl relative z-10 m-4 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                <h3 class="font-bold text-lg text-midnight">{{ $questionId ? 'Modifier un sujet' : 'Nouveau sujet' }}</h3>
                <button wire:click="$set('showQuestionModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thème rattaché <span class="text-red-500">*</span></label>
                        <select wire:model="questionCategoryId" class="w-full rounded-xl border-gray-200 focus:border-copper focus:ring focus:ring-copper/20 text-sm">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('questionCategoryId') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ordre</label>
                        <input type="number" wire:model="questionSortOrder" class="w-full rounded-xl border-gray-200 focus:border-copper focus:ring focus:ring-copper/20 text-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre de la question <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="questionTitle" class="w-full rounded-xl border-gray-200 focus:border-copper focus:ring focus:ring-copper/20 text-sm" placeholder="Ex: Comment connecter Instagram ?">
                    @error('questionTitle') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Résumé (Visible sur la carte du thème)</label>
                    <input type="text" wire:model="questionSummary" class="w-full rounded-xl border-gray-200 focus:border-copper focus:ring focus:ring-copper/20 text-sm">
                </div>
                
                <div wire:ignore x-data="{
                    content: @entangle('questionContent'),
                    init() {
                        ClassicEditor
                            .create(this.$refs.editorArea, {
                                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
                            })
                            .then(editor => {
                                editor.setData(this.content || '');
                                editor.model.document.on('change:data', () => {
                                    this.content = editor.getData();
                                    @this.set('questionContent', editor.getData());
                                });
                                // Listen to external events to reload contents (when opening modal for edit)
                                window.addEventListener('initEditor', event => {
                                    if(event.detail && editor.getData() !== event.detail[0]) {
                                        editor.setData(event.detail[0] || '');
                                    }
                                });
                            })
                            .catch(error => { console.error('Erreur CKEditor', error); });
                    }
                }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contenu complet <span class="text-red-500">*</span></label>
                    <div class="ck-editor-container" style="color: #333;">
                        <div x-ref="editorArea" class="prose max-w-none"></div>
                    </div>
                </div>
                @error('questionContent') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 shrink-0 rounded-b-2xl">
                <button wire:click="$set('showQuestionModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Annuler</button>
                <button wire:click="saveQuestion" class="px-4 py-2 text-sm font-medium text-white bg-copper rounded-lg hover:bg-amber-600 shadow-sm">Enregistrer le sujet</button>
            </div>
        </div>
    </div>
    @endif

    <style>
        /* CKEditor Custom Styles to match Postelio */
        .ck-editor__editable { min-height: 250px; border-radius: 0 0 0.75rem 0.75rem !important; }
        .ck-toolbar { border-radius: 0.75rem 0.75rem 0 0 !important; background: #f8fafc !important; }
    </style>
    
    <!-- Load CKEditor if not already loaded globally -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
</div>
