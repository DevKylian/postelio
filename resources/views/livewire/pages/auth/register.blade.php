<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
]);

$register = function () {
    $validated = $this->validate();

    $validated['password'] = Hash::make($validated['password']);

    event(new Registered($user = User::create($validated)));

    // Création automatique de l'entreprise
    Company::create([
        'user_id' => $user->id,
    ]);

    Auth::login($user);

    $this->redirect(route('onboarding', absolute: false), navigate: true);
};

?>

<div>
    <div class="mb-10 text-left">
        <h1 class="text-3xl font-display font-bold text-midnight tracking-tight">Bienvenue sur Postelio</h1>
        <p class="text-slate-500 mt-2 font-light">Commencez à transformer vos chantiers en clients.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom complet')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Adresse e-mail')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="password" :value="__('Mot de passe')" />
                <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmation')" />
                <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="pt-6">
            <x-primary-button class="w-full justify-center py-4 text-base font-bold tracking-widest shadow-xl shadow-copper/20 hover:shadow-copper/40 transform active:scale-[0.98]">
                {{ __('Créer mon compte') }}
            </x-primary-button>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-500 font-medium">
                Déjà un compte ?
                <a class="font-bold text-copper hover:text-amber-600 transition-colors" href="{{ route('login') }}" wire:navigate>
                    {{ __('Se connecter') }}
                </a>
            </p>
        </div>
    </form>
</div>
