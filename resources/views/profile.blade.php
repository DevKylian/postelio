<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                {{ __('Mon Profil') }}
            </h1>
            <p class="text-gray-400 text-sm mt-0.5">Gérez vos informations personnelles et la sécurité de votre compte.</p>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">
            <div class="glass-panel rounded-2xl p-6 lg:p-8">
                <livewire:profile.update-profile-information-form />
            </div>

            <div class="glass-panel rounded-2xl p-6 lg:p-8">
                <livewire:profile.update-password-form />
            </div>

            <div class="glass-panel rounded-2xl p-6 lg:p-8">
                <livewire:profile.newsletter-preferences />
            </div>

            <div class="glass-panel rounded-2xl p-6 lg:p-8">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
