<?php

use Illuminate\Support\Facades\Auth;
use function Livewire\Volt\state;

state([
    'newsletter_opt_in' => fn () => (bool) auth()->user()->newsletter_opt_in,
]);

$toggle = function () {
    $user = Auth::user();
    $user->update(['newsletter_opt_in' => ! $user->newsletter_opt_in]);
    $this->newsletter_opt_in = (bool) $user->newsletter_opt_in;
    $this->dispatch('notify', $this->newsletter_opt_in
        ? 'Vous êtes abonné à la newsletter.'
        : 'Vous avez été désabonné de la newsletter.'
    );
};

?>

<section>
    <header class="mb-6">
        <h2 class="text-base font-semibold text-midnight">
            Newsletter & Communications
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            Gérez vos préférences d'emails de la part de Postelio.
        </p>
    </header>

    <div class="flex items-center justify-between gap-6 p-5 rounded-2xl border transition-all
        {{ $newsletter_opt_in ? 'bg-amber-50 border-amber-100' : 'bg-slate-50 border-slate-100' }}">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-xl
                {{ $newsletter_opt_in ? 'bg-amber-100' : 'bg-slate-100' }}">
                {{ $newsletter_opt_in ? '📬' : '📭' }}
            </div>
            <div>
                <p class="text-sm font-semibold text-midnight">
                    Actualités & nouveautés Postelio
                </p>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $newsletter_opt_in
                        ? 'Vous recevez nos newsletters et mises à jour.'
                        : 'Vous ne recevez pas nos newsletters.' }}
                </p>
            </div>
        </div>

        <button
            wire:click="toggle"
            type="button"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-copper/30
                {{ $newsletter_opt_in ? 'bg-copper' : 'bg-slate-200' }}"
            role="switch"
            aria-checked="{{ $newsletter_opt_in ? 'true' : 'false' }}"
        >
            <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200
                    {{ $newsletter_opt_in ? 'translate-x-5' : 'translate-x-0' }}"
            ></span>
        </button>
    </div>
</section>
