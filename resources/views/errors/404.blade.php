@extends('errors.layout')

@section('title', __('Page Introuvable'))

@section('content')
    <div class="space-y-6">
        <h2 class="text-7xl font-display font-black text-midnight/10 tabular-nums">404</h2>
        <div class="px-8 flex flex-col items-center">
            <h1 class="text-3xl font-display font-bold text-midnight tracking-tight">Oups ! Cette page s'est envolée.</h1>
            <p class="mt-4 text-lg text-slate-500 font-medium max-w-sm">
                {{ __('Il semble que le lien que vous avez suivi n\'existe plus ou a été déplacé.') }}
            </p>
        </div>
        
        <div class="pt-8">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-8 py-4 bg-midnight text-white text-base font-bold rounded-2xl shadow-xl shadow-midnight/20 hover:shadow-midnight/40 transition-all duration-300 transform active:scale-[0.98]">
                {{ __('Retourner à l\'accueil') }}
            </a>
        </div>
    </div>
@endsection
