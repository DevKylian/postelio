@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'question' => '',
    'preview' => false,
])

<div class="w-full aspect-square bg-[#CBD5E1] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    @php
        $qLen = mb_strlen($question);
        $qFontSize = 'text-xl';
        if ($qLen > 80) $qFontSize = 'text-base';
        elseif ($qLen > 40) $qFontSize = 'text-lg';
    @endphp

    <div class="flex-1 flex flex-col p-10 gap-6 relative z-10 overflow-hidden">
        {{-- Question --}}
        <div class="bg-slate-900 p-6 rounded-xl shadow-xl shrink-0">
            <div class="flex items-center gap-4 mb-3">
                 <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-white text-sm" style="background-color: {{ $brandColor }}">Q</div>
                 <span class="text-white/40 text-[9px] font-black uppercase tracking-widest">Question Client</span>
            </div>
            <div class="max-h-[120px] overflow-hidden">
                <p class="text-white {{ $qFontSize }} font-black font-montserrat leading-tight uppercase">
                    {{ $question }}
                </p>
            </div>
        </div>

        {{-- Answer --}}
        <div class="bg-white border-2 border-slate-900 p-6 rounded-xl shadow-xl ml-8">
            <div class="flex items-center gap-4 mb-3">
                 <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center font-black text-white text-sm">R</div>
                 <span class="text-slate-900/40 text-[9px] font-black uppercase tracking-widest">Réponse Expert</span>
            </div>
            <p class="text-slate-900 text-sm font-bold font-montserrat leading-snug uppercase">
                COMPTEZ 24H POUR LA CIRCULATION PIÉTONNE ET 21 JOURS POUR UN SÉCHAGE À CŒUR OPTIMAL.
            </p>
        </div>
    </div>

    <div class="h-16 px-10 border-t border-slate-900/10 flex items-center justify-between">
         <span class="text-slate-900 text-xs font-black font-montserrat uppercase tracking-[0.2em]">{{ $companyLabel }}</span>
         <div class="w-10 h-1 rounded-full bg-slate-900 opacity-20"></div>
    </div>
</div>
