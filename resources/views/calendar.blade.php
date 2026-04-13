<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                Calendrier éditorial
            </h1>
            <p class="text-gray-400 text-sm mt-0.5">Visualisez et planifiez vos publications. Glissez-déposez pour replanifier.</p>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <livewire:pages.calendar />
        </div>
    </div>
</x-app-layout>
