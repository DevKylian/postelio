<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Nouveautés & Mises à jour
                </h1>
                <p class="text-gray-400 text-sm mt-1 max-w-lg font-medium">
                    Suivez l'évolution de la plateforme et découvrez les dernières optimisations.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100/50">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Système en ligne</span>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        // Fetch real changelogs
        $changelogs = \App\Models\Changelog::orderBy('published_at', 'desc')->get();
        
        $latest = $changelogs->shift();
    @endphp

    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-16">
        
        @if($latest)
        {{-- ── Latest Update (Hero) ── --}}
        <section class="animate-fade-up relative">
            <div class="absolute -left-20 -top-20 w-96 h-96 bg-{{ $latest->type_color }}-500/10 rounded-full blur-[120px] pointer-events-none"></div>
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-copper/10 rounded-full blur-[100px] pointer-events-none"></div>

            <div class="glass-panel rounded-[2.5rem] overflow-hidden group shadow-2xl shadow-slate-200/50 border-white/80 relative">
                <div class="relative p-8 sm:p-12 flex flex-col lg:flex-row gap-12 items-center">
                    <div class="flex-1 space-y-8">
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="badge badge-{{ $latest->type === 'feature' ? 'success' : ($latest->type === 'fix' ? 'error' : 'warning') }} !font-black uppercase tracking-[0.15em]">
                                {{ $latest->type_label }}
                            </span>
                            <div class="flex items-center gap-1.5 text-slate-400 font-bold text-[11px] tracking-wide bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($latest->published_at)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <h2 class="font-display text-4xl sm:text-5xl font-extrabold text-midnight tracking-tight leading-[1.1]">
                                {{ $latest->title }}
                            </h2>
                            <div class="text-slate-500 text-lg sm:text-xl font-medium leading-relaxed max-w-2xl changelog-content">
                                {!! $latest->content !!}
                            </div>
                        </div>

                        @if($latest->version)
                            <div class="pt-2 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white shadow-lg shadow-slate-900/10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Version</p>
                                    <p class="text-sm font-black text-midnight tabular-nums leading-none">{{ $latest->version }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="w-full lg:w-2/5 shrink-0 flex items-center justify-center">
                        <div class="relative w-full aspect-square max-w-[320px]">
                            <div class="absolute inset-0 bg-gradient-to-br from-{{ $latest->type_color }}-100/50 to-copper/10 rounded-full blur-3xl animate-pulse"></div>
                            <div class="relative w-full h-full glass-panel rounded-[3rem] p-px overflow-hidden shadow-2xl group-hover:scale-[1.02] transition-transform duration-500">
                                <div class="w-full h-full rounded-[2.9rem] bg-gradient-to-br from-white via-slate-50/50 to-slate-100/50 flex items-center justify-center p-12">
                                    <div class="p-8 rounded-[2.5rem] bg-white shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] group-hover:shadow-[0_25px_60px_-12px_rgba(0,0,0,0.15)] transition-all duration-500 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-br from-{{ $latest->type_color }}-500/5 to-copper/5"></div>
                                        @if($latest->type === 'feature')
                                            <svg class="w-20 h-20 text-emerald-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                            </svg>
                                        @elseif($latest->type === 'fix')
                                            <svg class="w-20 h-20 text-rose-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                            </svg>
                                        @else
                                            <svg class="w-20 h-20 text-amber-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="absolute top-10 right-10 w-3 h-3 rounded-full bg-emerald-400/40 animate-bounce"></div>
                                    <div class="absolute bottom-12 left-10 w-2 h-2 rounded-full bg-copper/40 animate-bounce" style="animation-delay: 0.5s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── Previous Updates Grid ── --}}
        @if($changelogs->isNotEmpty())
        <section class="space-y-10">
            <div class="flex items-center gap-6 px-4">
                <h3 class="font-display font-bold text-midnight text-xl tracking-tight shrink-0">Historique des mises à jour</h3>
                <div class="h-px bg-slate-100 flex-1"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8 pb-32">
                @foreach($changelogs as $log)
                    <div class="glass-panel group p-8 rounded-[2.5rem] border-white/60 hover:border-{{ $log->type_color }}-200 transition-all duration-300 card-hover flex flex-col h-full bg-white/40">
                        <div class="flex-1 space-y-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="badge badge-{{ $log->type === 'feature' ? 'success' : ($latest->type === 'fix' ? 'error' : 'warning') }} !text-[9px] !font-black uppercase tracking-widest">
                                        {{ $log->type_label }}
                                    </span>
                                    @if($log->version)
                                        <span class="text-[10px] font-bold text-slate-400">
                                            {{ $log->version }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50/50 border border-slate-100/50">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($log->published_at)->diffForHumans() }}
                                </span>
                            </div>
                            
                            <div class="space-y-3">
                                <h4 class="font-display font-bold text-midnight text-xl tracking-tight group-hover:text-{{ $log->type_color }}-600 transition-colors">
                                    {{ $log->title }}
                                </h4>
                                <div class="text-sm font-medium text-slate-500 leading-relaxed max-w-sm changelog-content">
                                    {!! $log->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif
        @else
        <div class="py-20 text-center animate-fade-up">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 text-slate-200 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h2 class="font-display font-bold text-2xl text-midnight tracking-tight">Aucune nouveauté pour le moment</h2>
            <p class="text-slate-400 mt-2 max-w-sm mx-auto">Revenez bientôt pour découvrir les dernières mises à jour de Postelio.</p>
        </div>
        @endif
    </div>

    <style>
        .changelog-content ul { list-style-type: disc; margin-left: 1.5rem; margin-top: 0.5rem; margin-bottom: 0.5rem; }
        .changelog-content ol { list-style-type: decimal; margin-left: 1.5rem; margin-top: 0.5rem; margin-bottom: 0.5rem; }
        .changelog-content li { margin-bottom: 0.25rem; }
        .changelog-content strong { font-weight: 800; color: #0F172A; }
    </style>
</x-app-layout>
