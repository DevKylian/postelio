@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'imageUrl' => null,
    'text' => null, // optional title
    'preview' => false,
])

<div class="w-full aspect-square bg-slate-700 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
    {{-- Full-bleed photo --}}
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
    @else
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-slate-400 select-none pointer-events-none">
                <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Votre photo</p>
            </div>
        </div>
    @endif

    {{-- Bottom zone: fixed 80px band at bottom for text + company --}}
    <div class="absolute inset-x-0 bottom-0 h-[80px] bg-gradient-to-t from-black/90 via-black/70 to-transparent z-10">
        {{-- Company label — pinned to very bottom --}}
        <div class="absolute inset-x-0 bottom-0 h-[30px] px-5 flex items-center">
            <div class="w-full" style="border-top: 2px solid {{ $brandColor }}; padding-top: 4px;">
                <div class="flex items-center gap-2">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded">
                    @endif
                    <p class="text-white font-bold text-[10px] uppercase tracking-widest font-montserrat truncate">{{ $companyLabel }}</p>
                </div>
            </div>
        </div>

        {{-- Text overlay — max 2 lines, above company label --}}
        @if($text)
            @php
                $displayText = mb_substr($text, 0, 45);
                $len = mb_strlen($displayText);
                $fontSize = 'text-[11px]';
                if ($len > 35) $fontSize = 'text-[9px]';
                elseif ($len > 25) $fontSize = 'text-[10px]';
            @endphp
            <div class="absolute inset-x-4 top-1 bottom-[32px] flex items-center justify-center overflow-hidden">
                <h3 class="text-white font-black {{ $fontSize }} uppercase leading-snug font-montserrat text-center" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $displayText }}</h3>
            </div>
        @endif
    </div>
</div>
