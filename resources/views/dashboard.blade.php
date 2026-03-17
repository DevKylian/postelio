<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
                    {{ __('Tableau de Bord') }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">Gérez votre communication artisanale.</p>
            </div>
            <div class="flex items-center gap-3">
                <x-secondary-button class="!px-4 !py-2 !text-xs">
                    {{ __('Filtrer') }}
                </x-secondary-button>
                <x-primary-button :href="route('generate-post')" wire:navigate class="!px-4 !py-2 !text-xs">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Générer un post') }}
                </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100/50 transition-all duration-300 hover:shadow-lg hover:scale-[1.02] group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-lg">+12%</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Posts Générés</p>
                    <h3 class="text-3xl font-bold text-midnight mt-1">128</h3>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100/50 transition-all duration-300 hover:shadow-lg hover:scale-[1.02] group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-copper/10 text-copper rounded-xl group-hover:bg-copper group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-lg">+5%</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Vues Totales</p>
                    <h3 class="text-3xl font-bold text-midnight mt-1">3.4k</h3>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100/50 transition-all duration-300 hover:shadow-lg hover:scale-[1.02] group sm:col-span-2 xl:col-span-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">Stable</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Engagement</p>
                    <h3 class="text-3xl font-bold text-midnight mt-1">4.2%</h3>
                </div>
            </div>

            <!-- Welcome Card -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100/50">
                <div class="p-10">
                    <div class="flex items-center gap-4 mb-6">
                        <h2 class="text-2xl font-bold text-midnight">Ravi de vous revoir, {{ auth()->user()->name }} !</h2>
                        <span class="px-3 py-1 bg-copper/10 text-copper text-xs font-bold rounded-full uppercase tracking-tighter">Artisan Pro</span>
                    </div>
                    <p class="text-gray-500 text-lg max-w-2xl leading-relaxed">
                        {{ __("Votre assistant Postelio est prêt à générer vos prochains contenus pour attirer de nouveaux clients. Que voulez-vous faire aujourd'hui ?") }}
                    </p>
                    
                    <div class="mt-10 flex flex-wrap gap-4">
                        <x-primary-button :href="route('generate-post')" wire:navigate class="!rounded-2xl">
                            Démarrer une création
                        </x-primary-button>
                        <x-secondary-button :href="route('posts.index')" wire:navigate class="!rounded-2xl">
                            Consulter mes archives
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
