@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'text' => 'TRANSFORMATION',
    'preview' => false,
])

<div class="w-full aspect-square bg-white flex flex-col relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
    <div class="flex-1 flex min-h-0">
        <div class="w-1/2 relative overflow-hidden border-r-2 border-white bg-slate-300 flex items-center justify-center {{ $preview ? 'group-hover:bg-slate-200 transition-colors duration-500' : '' }}">
            @if ($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="text-center text-slate-500 select-none pointer-events-none">
                    <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <p class="text-xs font-bold uppercase tracking-widest">Avant</p>
                </div>
            @endif
        </div>
        <div class="w-1/2 relative overflow-hidden bg-slate-400 flex items-center justify-center {{ $preview ? 'group-hover:bg-slate-300 transition-colors duration-500' : '' }}">
            @if ($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="text-center text-slate-100 select-none pointer-events-none">
                    <svg class="w-10 h-10 mx-auto mb-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <p class="text-xs font-bold uppercase tracking-widest">Après</p>
                </div>
            @endif
        </div>
    </div>
    <div class="shrink-0 flex flex-col items-center justify-center px-6 py-2 gap-1" style="background-color: {{ $brandColor }}">
        @php
            $cLen = mb_strlen($companyLabel);
            $cFontSize = 'text-[8px]';
            if ($cLen > 35) $cFontSize = 'text-[6px]';
            elseif ($cLen > 20) $cFontSize = 'text-[7px]';

            $tLen = mb_strlen($text);
            $tFontSize = 'text-sm';
            if ($tLen > 40) $tFontSize = 'text-xs';
            elseif ($tLen > 25) $tFontSize = 'text-[13px]';
        @endphp
        <div class="flex flex-col items-center max-w-full">
            <p class="text-white/70 font-bold uppercase tracking-widest {{ $cFontSize }} font-montserrat leading-none mb-1 truncate w-full text-center">{{ $companyLabel }}</p>
            <div class="max-h-[30px] overflow-hidden">
                <h3 class="text-white font-black uppercase tracking-tighter {{ $tFontSize }} font-montserrat leading-none text-center">{{ $text }}</h3>
            </div>
        </div>
    </div>
</div>
