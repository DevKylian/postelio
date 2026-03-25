@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'customerName' => 'Client',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

@if($preview)
    @php
        $customerName = 'Jean-Pierre D.';
        $text = $text ?: 'Une expérience parfaite de A à Z. L\'équipe a su transformer notre vision en réalité avec un professionnalisme exemplaire. Les délais ont été respectés et les finitions sont impeccables. Nous recommandons vivement pour vos projets !';
    @endphp
@endif

<div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    {{-- Grid background --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 40px 40px;">
    </div>

    <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10">
        
        <div class="bg-white border-2 border-[#0047AB] p-10 shadow-2xl relative w-full max-w-[360px] z-20">
            {{-- Simplified Rating --}}
            <div class="flex items-center gap-1 mb-8">
                @for($i=0; $i<5; $i++)
                <div class="w-3.5 h-3.5" style="background-color: {{ $brandColor }}"></div>
                @endfor
                <span class="ml-4 text-[10px] font-black font-mono-technical text-[#0047AB] tracking-widest">5.0/5.0</span>
            </div>

            @php
                $len = mb_strlen($text);
                $fontSize = 'text-lg';
                if ($len > 150) $fontSize = 'text-xs';
                elseif ($len > 80) $fontSize = 'text-sm';
                elseif ($len > 40) $fontSize = 'text-base';
            @endphp

            <div class="max-h-[220px] overflow-hidden">
                <p class="text-[#0047AB] {{ $fontSize }} font-black leading-tight font-mono-technical mb-10 uppercase text-center">
                    "{{ $text }}"
                </p>
            </div>

            <div class="pt-6 border-t border-dashed border-[#0047AB]/20 flex items-center justify-between">
                <div>
                    <span class="text-[#0047AB] text-xs font-black font-mono-technical uppercase">{{ $customerName }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[#0047AB] text-[9px] font-black font-mono-technical uppercase">{{ $companyLabel }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
