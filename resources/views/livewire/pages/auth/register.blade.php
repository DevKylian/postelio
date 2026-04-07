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
    'password_confirmation' => '',
    'newsletter_opt_in' => false,
    'terms' => false,
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    'newsletter_opt_in' => ['boolean'],
    'terms' => ['accepted', 'required'],
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

$redirectToGoogle = function () {
    $this->validate(['terms' => 'accepted']);
    
    session(['register_newsletter_opt_in' => $this->newsletter_opt_in]);
    
    return redirect()->route('auth.google');
};

?>

<div>
    <div class="mb-10 text-left">
        <h1 class="text-3xl font-display font-bold text-midnight tracking-tight">Bienvenue sur Postelio</h1>
        <p class="text-slate-500 mt-2 font-light">Commencez à transformer vos chantiers en clients.</p>
    </div>

    <!-- Social Register -->
    <div class="mb-8 space-y-4">
        <button wire:click="redirectToGoogle" type="button" class="w-full flex items-center justify-center gap-3 py-4 px-4 bg-white border border-gray-200 rounded-2xl shadow-sm hover:bg-gray-50 transition-all duration-200 group transform active:scale-[0.98]">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span class="text-slate-700 font-bold tracking-tight">Continuer avec Google</span>
        </button>

        <div class="relative flex items-center py-2">
            <div class="flex-grow border-t border-gray-100"></div>
            <span class="flex-shrink mx-4 text-xs font-black uppercase tracking-[0.2em] text-slate-300">Ou</span>
            <div class="flex-grow border-t border-gray-100"></div>
        </div>
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

        <!-- Legal & Newsletter -->
        <div class="mt-8 space-y-4 pt-4 border-t border-gray-50">
            <!-- Terms & Conditions (Required) -->
            <div>
                <label for="terms" class="inline-flex items-start group cursor-pointer">
                    <div class="relative flex items-center mt-0.5">
                        <input id="terms" type="checkbox" wire:model="terms" name="terms" class="w-5 h-5 rounded border-gray-300 text-copper shadow-sm focus:ring-copper transition-all duration-200">
                    </div>
                    <span class="ms-3 text-sm font-medium text-slate-600 group-hover:text-midnight transition-colors leading-tight">
                        {{ __('J\'accepte les') }} <a href="{{ route('legal.cgv') }}" target="_blank" class="text-copper border-b border-copper/30 hover:border-copper transition-all">{{ __('Conditions Générales de Vente') }}</a> {{ __('et la') }} <a href="{{ route('legal.confidentialite') }}" target="_blank" class="text-copper border-b border-copper/30 hover:border-copper transition-all">{{ __('Politique de Confidentialité') }}</a>
                    </span>
                </label>
                <x-input-error :messages="$errors->get('terms')" class="mt-2" />
            </div>

            <!-- Newsletter Opt-in (Optional) -->
            <div>
                <label for="newsletter_opt_in" class="inline-flex items-start group cursor-pointer">
                    <div class="relative flex items-center mt-0.5">
                        <input id="newsletter_opt_in" type="checkbox" wire:model ="newsletter_opt_in" name="newsletter_opt_in" class="w-5 h-5 rounded border-gray-300 text-copper shadow-sm focus:ring-copper transition-all duration-200">
                    </div>
                    <span class="ms-3 text-sm font-medium text-slate-500 group-hover:text-midnight transition-colors leading-tight">
                        {{ __('Je souhaite recevoir les actualités de Postelio par e-mail') }}
                    </span>
                </label>
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
