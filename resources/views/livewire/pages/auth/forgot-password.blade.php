<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state(['email' => '']);

rules(['email' => ['required', 'string', 'email']]);

$sendPasswordResetLink = function () {
    $this->validate();

    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Password::sendResetLink(
        $this->only('email')
    );

    if ($status != Password::RESET_LINK_SENT) {
        $this->addError('email', __($status));

        return;
    }

    $this->reset('email');

    Session::flash('status', __($status));
};

?>

<div>
    <div class="mb-10">
        <h1 class="text-3xl font-display font-bold text-midnight tracking-tight">Mot de passe oublié ?</h1>
        <p class="text-slate-500 mt-2 font-light">Indiquez votre e-mail, nous vous enverrons un lien de réinitialisation.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Adresse e-mail')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-8 space-y-4">
            <x-primary-button class="w-full justify-center py-3 shadow-lg shadow-copper/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                {{ __('Envoyer le lien de réinitialisation') }}
            </x-primary-button>

            <a href="{{ route('login') }}" wire:navigate class="block text-center text-sm text-gray-500 hover:text-midnight transition-colors font-medium">
                {{ __('Retour à la connexion') }}
            </a>
        </div>
    </form>
</div>
