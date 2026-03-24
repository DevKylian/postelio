@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'question' => '',
    'logoUrl' => null,
    'preview' => false,
])

<div class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    <div class="flex-1 flex flex-col p-10 justify-center gap-8 relative z-10">
        
        {{-- Question --}}
        <div class="relative pl-16">
            <div class="absolute left-0 top-0 w-12 h-12 bg-slate-900 flex items-center justify-center font-montserrat text-white text-xl font-black shadow-xl -rotate-3 border-2 border-white">Q</div>
            <div class="bg-white border-4 border-slate-900 p-6 shadow-[10px_10px_0px_0px_rgba(15,23,42,0.1)]">
                @php
                    $qLen = mb_strlen($question);
                    $qFontSize = 'text-lg';
                    if ($qLen > 80) $qFontSize = 'text-sm';
                    elseif ($qLen > 40) $qFontSize = 'text-base';
                @endphp
                <div class="max-h-[140px] overflow-hidden">
                    <p class="text-slate-900 {{ $qFontSize }} font-black font-montserrat leading-tight uppercase">
                        {{ $question }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Reponse --}}
        <div class="relative pl-16 ml-6 opacity-40 grayscale">
             <div class="absolute left-0 top-0 w-12 h-12 flex items-center justify-center font-black text-white text-xl font-montserrat shadow-xl rotate-3 border-2 border-white" style="background-color: {{ $brandColor }}">R</div>
             <div class="bg-white border-4 border-slate-900 p-6 shadow-[10px_10px_0px_0px_rgba(15,23,42,0.1)] border-l-[12px] border-l-slate-900">
                <p class="text-slate-900 text-sm font-black font-mono-technical leading-snug uppercase">
                    LA RÉPONSE DE VOTRE ARTISAN... 
                </p>
             </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="h-16 px-10 border-t-4 border-slate-900 bg-[#CBB198] flex items-center justify-between">
         <span class="text-slate-900 text-[11px] font-black uppercase tracking-[0.2em] font-montserrat">{{ $companyLabel }}</span>
    </div>
</div>
