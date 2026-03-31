<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Centre d'Aide
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Trouvez les réponses à toutes vos questions.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($categories as $category)
                    <div class="glass-panel flex flex-col rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 relative group">
                        
                        {{-- Background decorative blur --}}
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-copper/5 rounded-full blur-3xl group-hover:bg-copper/10 transition-colors duration-500"></div>

                        <div class="p-6 border-b border-gray-50 flex gap-4 items-start relative z-10">
                            <div class="w-12 h-12 rounded-xl bg-copper/10 text-copper flex items-center justify-center shrink-0">
                                {!! $category['icon'] !!}
                            </div>
                            <div>
                                <h3 class="font-bold text-midnight text-lg">{{ $category['name'] }}</h3>
                                <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $category['description'] }}</p>
                            </div>
                        </div>

                        <div class="flex-1 p-2 bg-white/50 relative z-10">
                            <ul class="divide-y divide-gray-50/50">
                                @foreach($category['questions'] as $question)
                                <li>
                                    <a href="{{ route('help.show', $question['slug']) }}" wire:navigate class="flex items-center gap-4 p-4 rounded-xl hover:bg-white hover:shadow-sm transition-all group/item">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover/item:text-copper transition-colors truncate">{{ $question['title'] }}</h4>
                                            @if(!empty($question['summary']))
                                                <p class="text-xs text-gray-400 mt-1 truncate">{{ $question['summary'] }}</p>
                                            @endif
                                        </div>
                                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center shrink-0 group-hover/item:bg-copper/10 group-hover/item:text-copper text-gray-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Empty State fallback --}}
            @if(empty($categories))
                <div class="text-center py-12">
                    <p class="text-gray-500">Aucune catégorie d'aide n'est disponible pour le moment.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
