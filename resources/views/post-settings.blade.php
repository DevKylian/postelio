<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
                Paramètres des posts
            </h1>
            <p class="text-gray-500 text-sm mt-1">Personnalisez les informations utilisées pour générer vos visuels.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Aperçu de la couleur de marque -->
            @if(auth()->user()->company)
            <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Aperçu de votre identité visuelle</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl shadow-md"
                         style="background-color: {{ auth()->user()->company->brand_color ?? '#F59E0B' }}"></div>
                    <div>
                        <p class="font-semibold text-midnight">{{ auth()->user()->company->name }}</p>
                        @if(auth()->user()->company->city)
                            <p class="text-sm text-gray-500">{{ auth()->user()->company->city }}</p>
                        @endif
                    </div>
                    @if(auth()->user()->company->logo_path)
                        <img src="{{ Storage::url(auth()->user()->company->logo_path) }}"
                             alt="Logo"
                             class="h-12 w-12 rounded-lg object-contain border border-gray-200 bg-gray-50 p-1 ml-auto" />
                    @endif
                </div>
            </div>
            @endif

            <!-- Formulaire paramètres société -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:settings.update-company-settings-form />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
