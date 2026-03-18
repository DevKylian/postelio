<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Statistiques Instagram
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Analyse de vos performances sur Instagram
                </p>
            </div>
        </div>
    </x-slot>

    <livewire:pages.instagram-stats />
</x-app-layout>
