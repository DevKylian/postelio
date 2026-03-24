@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'text' => 'TRANSFORMATION',
    'preview' => false,
])

<div class="w-full aspect-square bg-slate-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/20 group-hover:-translate-y-2' : '' }}">
    
    {{-- Background logic --}}
    <div class="flex-1 flex min-h-0 relative">
        <div class="w-1/2 relative overflow-hidden bg-slate-800">
            @if ($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover grayscale-[0.3] brightness-75">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
        </div>
        <div class="w-1/2 relative overflow-hidden bg-slate-700">
            @if ($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover brightness-110">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
        </div>

        {{-- Glass Divider --}}
        <div class="absolute inset-y-0 left-1/2 -ml-px w-0.5 bg-white/30 backdrop-blur-md z-10"></div>
    </div>


    {{-- Glass Floor --}}
    <div class="shrink-0 backdrop-blur-xl bg-white/10 border-t border-white/20 p-5 flex flex-col items-center gap-1 shadow-2xl relative z-20">
        @php
            $cLen = mb_strlen($companyLabel);
            $cFontSize = 'text-[9px]';
            if ($cLen > 30) $cFontSize = 'text-[7px]';
            elseif ($cLen > 20) $cFontSize = 'text-[8px]';

            $tLen = mb_strlen($text ?: 'TRANSFORMATION');
            $tFontSize = 'text-base';
            if ($tLen > 40) $tFontSize = 'text-xs';
            elseif ($tLen > 25) $tFontSize = 'text-sm';
        @endphp
        <div class="flex flex-col items-center max-w-[200px]">
            <span class="text-white/60 {{ $cFontSize }} font-bold uppercase tracking-[0.2em] font-montserrat truncate w-full text-center">{{ $companyLabel }}</span>
            <div class="max-h-[30px] overflow-hidden flex items-center justify-center">
                <span class="text-white {{ $tFontSize }} font-black uppercase tracking-tighter font-montserrat text-center">{{ $text ?: 'TRANSFORMATION' }}</span>
            </div>
        </div>
        <div class="flex w-full items-center justify-between px-6">
            <div class="flex flex-col items-center">
                <span class="text-white/40 text-[8px] font-black uppercase tracking-widest font-montserrat mb-1">Avant</span>
                <div class="h-1 w-12 rounded-full bg-white/20"></div>
            </div>
            
            <div class="flex flex-col items-center">
                <span class="text-white text-[8px] font-black uppercase tracking-widest font-montserrat mb-1">Après</span>
                <div class="h-1 w-12 rounded-full" style="background-color: {{ $brandColor }}"></div>
            </div>
        </div>
    </div>
    
    {{-- Decorative glow --}}
    <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full blur-[80px] opacity-20 pointer-events-none" style="background-color: {{ $brandColor }}"></div>
    <div class="absolute -bottom-24 -left-24 w-48 h-48 rounded-full blur-[80px] opacity-10 pointer-events-none" style="background-color: {{ $brandColor }}"></div>
</div>
