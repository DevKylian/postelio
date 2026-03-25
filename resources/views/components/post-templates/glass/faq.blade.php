@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'question' => '',
    'logoUrl' => null,
    'preview' => false,
])

@if($preview)
    @php
        $question = $question ?: 'Quels sont les délais moyens pour une rénovation complète de toiture ?';
    @endphp
@endif

<div class="w-full aspect-square bg-slate-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full blur-[80px] opacity-20" style="background-color: {{ $brandColor }}"></div>

    <div class="flex-1 flex flex-col p-8 justify-center gap-6 relative z-10">
        
        <div class="space-y-2">
            <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] ml-1">Question Fréquente</span>
            <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-2xl p-5 shadow-xl">
                @php
                    $qLen = mb_strlen($question);
                    $qFontSize = 'text-lg';
                    if ($qLen > 100) $qFontSize = 'text-sm';
                    elseif ($qLen > 50) $qFontSize = 'text-base';
                @endphp
                <div class="max-h-[140px] overflow-hidden">
                    <p class="text-white {{ $qFontSize }} font-bold leading-tight">
                        {{ $question }}
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-2 translate-x-4 opacity-40 grayscale">
            <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] ml-1">La Réponse d'Expert</span>
            <div class="backdrop-blur-xl bg-white/10 border-l-4 rounded-2xl p-6 shadow-2xl" style="border-color: {{ $brandColor }}">
                <p class="text-white/90 text-sm leading-relaxed">
                    LA RÉPONSE DE VOTRE ARTISAN... 
                </p>
            </div>
        </div>

    </div>

    <div class="px-8 py-6 backdrop-blur-md bg-white/5 border-t border-white/10 flex items-center justify-between">
        <div class="flex items-center gap-2">
             <div class="w-2 h-2 rounded-full" style="background-color: {{ $brandColor }}"></div>
             <span class="text-white/40 text-[10px] font-bold uppercase tracking-widest">{{ $companyLabel }}</span>
        </div>
        <svg class="w-5 h-5 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
</div>
