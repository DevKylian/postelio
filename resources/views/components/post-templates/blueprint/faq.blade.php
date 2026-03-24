@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'question' => '',
    'logoUrl' => null,
    'preview' => false,
])

<div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
    </div>

    <div class="flex-1 flex flex-col p-12 justify-center gap-10 relative z-10">
        
        <div class="relative pl-14">
            <div class="absolute left-0 top-0 w-10 h-10 border-2 border-[#0047AB] bg-white flex items-center justify-center font-mono-technical text-[#0047AB] font-black z-20">Q</div>
            <div class="bg-white p-5 shadow-xl ring-1 ring-black/5">
                @php
                    $qLen = mb_strlen($question);
                    $qFontSize = 'text-[13px]';
                    if ($qLen > 100) $qFontSize = 'text-[10px]';
                    elseif ($qLen > 50) $qFontSize = 'text-[11px]';
                @endphp
                <div class="max-h-[140px] overflow-hidden">
                    <p class="text-[#0047AB] {{ $qFontSize }} font-black font-mono-technical leading-tight uppercase">
                        {{ $question }}
                    </p>
                </div>
            </div>
        </div>

        <div class="relative pl-14 ml-8 opacity-40 grayscale">
             <div class="absolute left-0 top-0 w-10 h-10 border-2 border-white flex items-center justify-center font-mono-technical text-white font-black z-20 shadow-lg" style="background-color: {{ $brandColor }}">R</div>
             <div class="bg-white p-5 shadow-xl ring-1 ring-black/5 border-l-4" style="border-left-color: {{ $brandColor }}">
                <p class="text-[#0047AB] text-[11px] font-black font-mono-technical leading-snug uppercase">
                    LA RÉPONSE DE VOTRE ARTISAN...
                </p>
             </div>
        </div>

    </div>

    <div class="h-14 px-10 bg-white border-t-2 border-[#0047AB] flex items-center justify-between text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest">
        <span>{{ $companyLabel }}</span>
    </div>
</div>
