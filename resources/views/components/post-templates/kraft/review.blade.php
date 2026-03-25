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

<div class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    {{-- Main Content --}}
    <div class="flex-1 p-10 flex flex-col items-center justify-center relative z-10">
        
        <div class="bg-white border-4 border-slate-900 p-10 shadow-[15px_15px_0px_0px_rgba(15,23,42,0.1)] relative w-full max-w-[380px] -rotate-1">
            
            @php
                $len = mb_strlen($text);
                $fontSize = 'text-2xl';
                if ($len > 150) $fontSize = 'text-base';
                elseif ($len > 80) $fontSize = 'text-lg';
                elseif ($len > 40) $fontSize = 'text-xl';
            @endphp

            <div class="max-h-[220px] overflow-hidden">
                <p class="text-slate-900 {{ $fontSize }} font-black leading-[1.1] font-montserrat uppercase text-center">
                    "{{ $text }}"
                </p>
            </div>

            <div class="pt-8 border-t-2 border-dashed border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-slate-900 text-sm font-black uppercase font-montserrat">{{ $customerName }}</span>
                </div>
                <div class="w-10 h-10 border-2 border-slate-900 flex items-center justify-center">
                    <div class="w-6 h-6 rounded-full" style="background-color: {{ $brandColor }}"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Branding --}}
    <div class="h-16 px-10 bg-slate-900 flex items-center justify-between z-20">
         <span class="text-white text-xs font-black font-mono-technical uppercase tracking-[0.3em]">{{ $companyLabel }}</span>
         <div class="flex gap-1">
              <div class="w-2 h-2" style="background-color: {{ $brandColor }}"></div>
              <div class="w-2 h-2 bg-white/20"></div>
              <div class="w-2 h-2 bg-white/10"></div>
         </div>
    </div>
</div>
