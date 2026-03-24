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
        <div class="bg-white p-10 shadow-2xl w-full max-w-[360px] relative border-2 border-red-600">

            <h3 class="text-[#0047AB] text-lg font-black font-mono-technical leading-tight mb-6 uppercase tracking-tighter">
                ERREUR À ÉVITER
            </h3>
            
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

    <div class="h-14 px-10 bg-white border-t-2 border-[#011430] flex items-center justify-between text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest">
        <span>{{ $companyLabel }}</span>
    </div>
</div>
