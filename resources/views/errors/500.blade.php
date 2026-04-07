@extends('errors.layout')

@section('title', __('Erreur Serveur'))

@section('content')
    <div class="px-8 flex flex-col items-center">
        <h2 class="text-7xl font-display font-black text-midnight/10 tabular-nums">500</h2>
        <div class="mt-4">
            <h1 class="text-3xl font-display font-bold text-midnight tracking-tight">Oups ! Une petite erreur technique.</h1>
            <p class="mt-4 text-lg text-slate-500 font-medium max-w-sm mx-auto leading-relaxed">
                {{ __('Nos serveurs ont rencontré un problème inattendu. Nous sommes déjà en train de regarder ce qu\'il se passe.') }}
            </p>
        </div>
        
        <div class="pt-12">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-8 py-4 bg-midnight text-white text-base font-bold rounded-2xl shadow-xl shadow-midnight/20 hover:shadow-midnight/40 transition-all duration-300 transform active:scale-[0.98]">
                {{ __('Réessayer d\'accéder au site') }}
            </a>
        </div>
    </div>
@endsection
