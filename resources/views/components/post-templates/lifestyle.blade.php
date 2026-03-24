@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'imageUrl' => null,
    'text' => null, // optional title
    'preview' => false,
])

<div class="w-full aspect-square bg-slate-700 relative overflow-hidden flex items-center justify-center {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
    @else
        <div class="text-center text-slate-400 select-none pointer-events-none">
            <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Votre photo</p>
        </div>
    @endif

    @if($text)
        @php
            $len = mb_strlen($text);
            $fontSize = 'text-[11px]';
            if ($len > 80) $fontSize = 'text-[8px]';
            elseif ($len > 40) $fontSize = 'text-[9px]';
        @endphp
        <div class="absolute inset-x-4 bottom-16 bg-black/70 backdrop-blur-sm p-3.5 rounded-xl border border-white/10 shadow-xl z-10 text-center">
            <div class="max-h-[40px] overflow-hidden">
                <h3 class="text-white font-black {{ $fontSize }} uppercase leading-snug font-montserrat">{{ $text }}</h3>
            </div>
        </div>
    @endif
    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent pt-10 pb-4 px-5">
        <div class="w-full" style="border-top: 3px solid {{ $brandColor }}; padding-top: 8px;">
            <div class="flex items-center gap-2">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-5 w-5 object-contain rounded">
                @endif
                <p class="text-white font-bold text-xs uppercase tracking-widest font-montserrat">{{ $companyLabel }}</p>
            </div>
        </div>
    </div>
</div>
