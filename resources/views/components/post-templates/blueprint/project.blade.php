@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'text' => 'TRANSFORMATION',
    'preview' => false,
])

<div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    {{-- Background Grid --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
    </div>

    <div class="flex-1 grid grid-cols-2 gap-px bg-white/20 p-6 relative z-10">
        {{-- Avant --}}
        <div class="relative overflow-hidden bg-blue-900 border border-white/10">
            @if($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-blue-900 border border-white/10 flex items-center justify-center">
                    <span class="text-white/20 text-[7px] font-black font-mono-technical uppercase">AVANT</span>
                </div>
            @endif
            <div class="absolute top-0 left-0 px-3 py-1 bg-white text-[#0047AB] text-[9px] font-black font-mono-technical uppercase shadow-xl">AVANT</div>
        </div>
        {{-- Après --}}
        <div class="relative overflow-hidden bg-blue-900 border border-white/10">
            @if($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover text-[#0047AB]">
            @else
                <div class="w-full h-full bg-blue-900 border border-white/10 flex items-center justify-center">
                    <span class="text-white/20 text-[7px] font-black font-mono-technical uppercase tracking-widest">APRÈS</span>
                </div>
            @endif
            <div class="absolute top-0 left-0 px-3 py-1 bg-white text-[#0047AB] text-[9px] font-black font-mono-technical uppercase shadow-xl">APRÈS</div>
        </div>
    </div>

    {{-- Simplified Footer --}}
    <div class="h-20 bg-white px-10 flex items-center justify-between border-t-2 border-[#0047AB] relative z-20">
        @php
            $cLen = mb_strlen($companyLabel);
            $cFontSize = 'text-xs';
            if ($cLen > 30) $cFontSize = 'text-[9px]';
            elseif ($cLen > 20) $cFontSize = 'text-[10px]';

            $tLen = mb_strlen($text);
            $tFontSize = 'text-base';
            if ($tLen > 40) $tFontSize = 'text-xs';
            elseif ($tLen > 25) $tFontSize = 'text-sm';
        @endphp
        <div class="flex flex-col max-w-[300px]">
            <span class="text-[#0047AB] {{ $cFontSize }} font-black font-mono-technical tracking-widest uppercase mb-1 truncate">{{ $companyLabel }}</span>
            <div class="max-h-[40px] overflow-hidden">
                <span class="text-[#0047AB] {{ $tFontSize }} font-black font-mono-technical tracking-tighter uppercase leading-tight">{{ $text }}</span>
            </div>
        </div>
        <div class="flex items-center gap-6">
             <div class="w-8 h-8 rounded-full border-2 border-[#0047AB] flex items-center justify-center p-1">
                  <div class="w-full h-full rounded-full" style="background-color: {{ $brandColor }}"></div>
             </div>
        </div>
    </div>
</div>
