<?php

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

use function Livewire\Volt\{state, uses};

uses([WithFileUploads::class]);

state([
    'name'        => fn () => auth()->user()->company?->name ?? '',
    'city'        => fn () => auth()->user()->company?->city ?? '',
    'brand_color' => fn () => auth()->user()->company?->brand_color ?? '#F59E0B',
    'logo_path'   => fn () => auth()->user()->company?->logo_path,
    'logo'        => null,
]);

$updateCompanySettings = function () {
    $this->validate([
        'name'        => ['required', 'string', 'max:255'],
        'city'        => ['nullable', 'string', 'max:255'],
        'brand_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'logo'        => ['nullable', 'image', 'max:2048'],
    ]);

    $user = Auth::user();
    $company = $user->company;

    $data = [
        'name'        => $this->name,
        'city'        => $this->city,
        'brand_color' => $this->brand_color,
    ];

    if ($this->logo) {
        $data['logo_path'] = $this->logo->store('logos', 'public');
    }

    if ($company) {
        $company->update($data);
    } else {
        Company::create(array_merge($data, ['user_id' => $user->id]));
    }

    // Reload the relationship so the blade template picks up new values (logo, etc.)
    $user->load('company');

    $this->logo = null;

    // Re-sync state from the freshly loaded company
    $this->name        = $user->company->name;
    $this->city        = $user->company->city ?? '';
    $this->brand_color = $user->company->brand_color;
    $this->logo_path   = $user->company->logo_path;

    $this->dispatch('company-updated');
};

?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Paramètres de la société
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Ces informations apparaissent sur vos posts générés.
        </p>
    </header>

    <form wire:submit="updateCompanySettings" class="mt-6 space-y-6">

        <!-- Nom de la société -->
        <div>
            <x-input-label for="company_name" value="Nom de la société" />
            <x-text-input wire:model="name"
                          id="company_name"
                          name="name"
                          type="text"
                          class="mt-1 block w-full"
                          required
                          autocomplete="organization" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Ville -->
        <div>
            <x-input-label for="city" value="Ville" />
            <x-text-input wire:model="city"
                          id="city"
                          name="city"
                          type="text"
                          class="mt-1 block w-full"
                          autocomplete="address-level2" />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <!-- Couleur de marque -->
        <div>
            <x-input-label for="brand_color" value="Couleur de marque" />
            <div class="mt-1 flex items-center gap-3">
                <input wire:model="brand_color"
                       id="brand_color"
                       name="brand_color"
                       type="color"
                       class="h-10 w-16 cursor-pointer rounded-lg border border-gray-300 p-1 shadow-sm focus:outline-none focus:ring-2 focus:ring-copper" />
                <x-text-input wire:model="brand_color"
                              type="text"
                              class="w-36 uppercase"
                              placeholder="#F59E0B" />
            </div>
            <p class="mt-1 text-xs text-gray-500">Utilisée comme couleur principale dans vos visuels.</p>
            <x-input-error class="mt-2" :messages="$errors->get('brand_color')" />
        </div>

        <!-- Logo -->
        <div>
            <x-input-label for="logo" value="Logo de la société" />
            <div class="mt-1 flex items-center gap-4">
                @if ($logo_path)
                    <img src="{{ Storage::url($logo_path) }}"
                         alt="Logo actuel"
                         class="h-16 w-16 rounded-lg object-contain border border-gray-200 bg-gray-50 p-1" />
                @endif
                <input wire:model="logo"
                       id="logo"
                       name="logo"
                       type="file"
                       accept="image/*"
                       class="block w-full text-sm text-gray-700
                              file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-semibold file:bg-copper/10 file:text-copper
                              hover:file:bg-copper/20 cursor-pointer" />
            </div>
            @if ($logo)
                <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Aperçu : <img src="{{ $logo->temporaryUrl() }}" class="h-10 w-10 rounded object-contain border border-gray-200" />
                </div>
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('logo')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Enregistrer</x-primary-button>

            <x-action-message class="me-3" on="company-updated">
                Paramètres enregistrés.
            </x-action-message>
        </div>

    </form>
</section>
