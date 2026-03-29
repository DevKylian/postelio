<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('layouts.guest');

form(LoginForm::class);

$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="mb-10">
        <h1 class="text-3xl font-display font-bold text-midnight tracking-tight">Ravi de vous revoir !</h1>
        <p class="text-slate-500 mt-2 font-light">Connectez-vous pour gérer vos chantiers.</p>
    </div>

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Adresse e-mail')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-copper shadow-sm focus:ring-copper" name="remember">
                <span class="ms-2 text-sm text-gray-700 font-medium">{{ __('Se souvenir de moi') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-copper hover:text-amber-600 transition-colors" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Mot de passe oublié ?') }}
                </a>
            @endif
        </div>

        <div class="mt-8">
            <x-primary-button class="w-full justify-center py-4 text-base font-bold tracking-widest shadow-xl shadow-copper/20 hover:shadow-copper/40 transform active:scale-[0.98]">
                {{ __('Se connecter') }}
            </x-primary-button>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-500">
                Pas encore de compte ?
                <a href="{{ route('register') }}" wire:navigate class="font-bold text-copper hover:text-amber-600 transition-colors">
                    Créer un compte
                </a>
            </p>
        </div>
    </form>
</div>
