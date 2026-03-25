@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

@if($preview)
    @php
        $text = $text ?: '3 gestes essentiels pour protéger votre toiture avant l\'hiver.';
    @endphp
@endif

<div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 30px 30px;">
    </div>

    <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10">
        
        <div class="w-20 h-20 border-2 border-[#0047AB] bg-white flex items-center justify-center mb-10 shadow-xl">
             <svg class="w-10 h-10 text-[#0047AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
             </svg>
        </div>

        <div class="bg-white p-10 shadow-2xl w-full max-w-[360px] relative border-l-4 border-l-[#003380]">
            <h3 class="text-[#0047AB] text-lg font-black font-mono-technical leading-tight mb-2 uppercase tracking-tighter">CONSEIL D'EXPERT</h3>
            @php
                $len = mb_strlen($text);
                $fontSize = 'text-sm';
                if ($len > 250) $fontSize = 'text-[10px]';
                elseif ($len > 120) $fontSize = 'text-xs';
            @endphp
            <div class="max-h-[160px] overflow-hidden">
                <p class="text-[#0047AB] {{ $fontSize }} font-black font-mono-technical leading-relaxed uppercase">
                    {{ $text }}
                </p>
            </div>
        </div>

    </div>

    <div class="h-14 bg-white px-10 border-t-2 border-[#0047AB] flex items-center justify-between text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest">
        <span>{{ $companyLabel }}</span>
        <div class="flex gap-2">
             <div class="w-2 h-2 rounded-full" style="background-color: {{ $brandColor }}"></div>
             <div class="w-2 h-2 rounded-full bg-blue-100"></div>
        </div>
    </div>
</div>
