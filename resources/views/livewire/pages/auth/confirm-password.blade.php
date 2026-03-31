<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state(['password' => '']);

rules(['password' => ['required', 'string']]);

$confirmPassword = function () {
    $this->validate();

    if (! Auth::guard('web')->validate([
        'email' => Auth::user()->email,
        'password' => $this->password,
    ])) {
        throw ValidationException::withMessages([
            'password' => __('auth.password'),
        ]);
    }

    session(['auth.password_confirmed_at' => time()]);

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<div>
    <div class="mb-10">
        <h1 class="text-3xl font-display font-bold text-midnight tracking-tight">Confirmez votre identité</h1>
        <p class="text-slate-500 mt-2 font-light">Cette zone sécurisée nécessite une confirmation de votre mot de passe.</p>
    </div>

    <form wire:submit="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" />

            <x-text-input wire:model="password"
                          id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirmer') }}
            </x-primary-button>
        </div>
    </form>
</div>
