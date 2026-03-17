<x-app-layout>
    <x-slot name="header">
        <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
            {{ __('Générer un Post') }}
        </h1>
        <p class="text-gray-500 text-sm mt-1">Transformez vos idées en visuels premium.</p>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <livewire:pages.post-generator />
        </div>
    </div>
</x-app-layout>
