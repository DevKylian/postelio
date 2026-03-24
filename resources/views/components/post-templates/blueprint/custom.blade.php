@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

<div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
    </div>

    <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10">
        
        <div class="w-full h-full border border-white/40 relative flex flex-col items-center justify-center p-10 bg-white shadow-2xl">
            <div class="text-center relative z-20 w-full">
                <span class="text-[#0047AB]/40 text-[9px] font-black font-mono-technical uppercase tracking-widest block mb-8">Spécification Technique</span>
                @php
                    $len = mb_strlen($text);
                    $fontSize = 'text-3xl';
                    if ($len > 100) $fontSize = 'text-base';
                    elseif ($len > 50) $fontSize = 'text-xl';
                    elseif ($len > 20) $fontSize = 'text-2xl';
                @endphp
                <div class="space-y-8 w-full">
                    <div class="max-h-[160px] overflow-hidden flex items-center justify-center">
                        <p class="text-[#0047AB] {{ $fontSize }} font-black font-mono-technical leading-tight uppercase text-center w-full">{{ $text ?: 'VOTRE TEXTE ICI' }}</p>
                    </div>
                    <div class="w-16 h-1 mx-auto" style="background-color: {{ $brandColor }}"></div>
                    <p class="text-[#0047AB]/60 text-[10px] font-black font-mono-technical uppercase italic tracking-[0.2em]">Spécification sur mesure</p>
                </div>
            </div>

            {{-- Minimalist Dimension lines --}}
            <div class="absolute top-1/2 -left-8 flex items-center gap-1 -rotate-90">
                 <div class="w-5 h-px bg-[#0047AB]/20"></div>
                 <span class="text-[#0047AB]/30 text-[6px] font-mono-technical font-black tracking-widest">HT_VAR</span>
            </div>
            <div class="absolute -top-8 left-1/2 -translate-x-1/2 flex items-center gap-1">
                 <div class="w-5 h-px bg-[#0047AB]/20"></div>
                 <span class="text-[#0047AB]/30 text-[6px] font-mono-technical font-black tracking-widest">WD_MAX</span>
            </div>
        </div>

    </div>

    <div class="h-14 px-10 bg-white border-t-2 border-[#0047AB] flex items-center justify-between z-20">
        <span class="text-[#0047AB] text-[9px] font-black uppercase tracking-widest font-mono-technical leading-none">{{ $companyLabel }}</span>
        <div class="bg-[#0047AB] px-3 py-1.5 text-white text-[9px] font-black font-mono-technical uppercase shadow-lg italic">
             BP - PLAN {{ date('Y') }}
        </div>
    </div>
</div>
