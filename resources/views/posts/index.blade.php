<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
                    {{ __('Mes Créations') }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">Gérez et téléchargez vos visuels générés.</p>
            </div>
            <x-primary-button :href="route('generate-post')" wire:navigate class="!rounded-2xl shadow-lg shadow-copper/20">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                NOUVEAU POST
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @if($posts->isEmpty())
                <!-- State Vide -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-16 text-center max-w-2xl mx-auto">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-midnight mb-2">Vous n'avez pas encore de créations</h3>
                    <p class="text-gray-500 mb-8">Commencez par envoyer un mémo vocal pour générer votre premier post premium.</p>
                    <x-primary-button :href="route('generate-post')" wire:navigate class="!px-8 !py-4 shadow-xl shadow-copper/20">
                        CRÉER MON PREMIER POST
                    </x-primary-button>
                </div>
            @else
                <!-- Grille de Posts -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($posts as $post)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group hover:shadow-xl transition-all duration-300 hover:-translate-y-1 transform-gpu">
                            <!-- Image -->
                            <div class="aspect-square relative overflow-hidden bg-gray-100 rounded-t-2xl isolation-auto">
                                @if($post->image_path)
                                    <img src="{{ Storage::url($post->image_path) }}" alt="Post" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-200 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <!-- Badge Type -->
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 bg-white/90 backdrop-blur-sm shadow-sm rounded-full text-[10px] font-black uppercase tracking-widest text-midnight">
                                        {{ $post->template === 'review' ? 'Avis Client' : 'Réalisation' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Infos & Actions -->
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div class="mb-6">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">
                                        Généré le {{ $post->created_at->format('d/m/Y') }}
                                    </p>
                                    <h4 class="font-bold text-midnight truncate text-sm">
                                        {{ $post->refined_content ?? 'Post sans titre' }}
                                    </h4>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ Storage::url($post->image_path) }}" download class="flex-1 flex items-center justify-center gap-2 py-3 px-3 rounded-xl border border-gray-200 text-midnight text-xs font-bold hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Télécharger
                                    </a>
                                    <button onclick="navigator.clipboard.writeText('{{ addslashes($post->refined_content) }}')" class="flex items-center justify-center p-3 rounded-xl border border-gray-100 text-copper hover:bg-copper hover:text-white transition-all group/copy relative">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-midnight text-white text-[10px] rounded opacity-0 group-hover/copy:opacity-100 pointer-events-none transition-opacity">
                                            Copier
                                        </div>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette création ? Cette action est irréversible.')" class="contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center justify-center p-3 rounded-xl border border-gray-100 text-red-500 hover:bg-red-500 hover:text-white transition-all group/delete relative">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-red-600 text-white text-[10px] rounded opacity-0 group-hover/delete:opacity-100 pointer-events-none transition-opacity">
                                                Supprimer
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@800;900&display=swap');
        .font-montserrat {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
    @endpush
</x-app-layout>
