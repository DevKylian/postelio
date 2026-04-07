@extends('errors.layout')

@section('title', __('Maintenance en cours'))

@section('content')
    <div class="px-8 flex flex-col items-center">
        <!-- Maintenance Icon/Status -->
        <div class="mb-10 relative">
            <div class="w-20 h-20 bg-copper/10 rounded-3xl flex items-center justify-center animate-pulse">
                <svg class="w-10 h-10 text-copper" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.456-2.454L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
            </div>
            <div class="absolute -top-1 -right-1 w-4 h-4 bg-copper border-4 border-white rounded-full"></div>
        </div>

        <div class="max-w-md mx-auto">
            <h1 class="text-3xl font-display font-bold text-midnight tracking-tight leading-tight">Mise à jour en cours</h1>
            <p class="mt-4 text-lg text-slate-500 font-medium leading-relaxed">
                {{ __('Nous effectuons quelques mises à jour indispensables pour vous offrir la meilleure expérience possible. Nous serons de retour dans quelques instants !') }}
            </p>
        </div>
        
        <!-- Subtle Progress hint -->
        <div class="mt-12 w-48 h-1 bg-slate-100 rounded-full overflow-hidden mx-auto">
            <div class="h-full bg-copper/40 rounded-full animate-loading-bar" style="width: 40%"></div>
        </div>
        <p class="mt-3 text-[10px] font-bold text-copper uppercase tracking-[0.2em] opacity-80">Mise à jour en cours</p>
    </div>

    <style>
        .animate-loading-bar {
            animation: loadingBar 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        @keyframes loadingBar {
            0% { transform: translateX(-110%); }
            100% { transform: translateX(260%); }
        }
    </style>
@endsection
