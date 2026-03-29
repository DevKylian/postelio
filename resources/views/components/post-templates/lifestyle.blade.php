@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'imageUrl' => null,
    'text' => null,
    'preview' => false,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = $text ?? 'La passion du métier se lit dans chaque geste.';
    @endphp
@endif

<div class="w-full aspect-square bg-gray-700 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
    {{-- Full-bleed photo --}}
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
    @else
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-gray-400 select-none pointer-events-none">
                <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Votre photo</p>
            </div>
        </div>
    @endif

    {{-- Dark gradient on lower half --}}
    <div class="absolute inset-x-0 bottom-0 h-[50%] bg-gradient-to-t from-black/80 via-black/40 to-transparent z-[1]"></div>

    {{-- Company label — bottom bar --}}
    <div class="absolute inset-x-0 bottom-0 h-[28px] px-5 flex items-center z-10">
        <div class="w-full flex items-center gap-2" style="border-top: 2px solid {{ $brandColor }}; padding-top: 3px;">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded">
            @endif
            <p class="text-white font-bold text-[9px] uppercase tracking-widest font-montserrat truncate">{{ $companyLabel }}</p>
        </div>
    </div>

    {{-- Text overlay — centered in lower third, above company label --}}
    @if($text)
        @php
            $len = mb_strlen($text);
            $fontSize = 'text-sm';
            if ($len > 25) $fontSize = 'text-[10px]';
            elseif ($len > 18) $fontSize = 'text-xs';
        @endphp
        <div class="absolute inset-x-6 bottom-[42px] top-[60%] flex items-center justify-center z-10 overflow-hidden">
            <div class="bg-black/50 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/10">
                <h3 class="text-white font-black {{ $fontSize }} uppercase leading-snug font-montserrat text-center" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $text }}</h3>
            </div>
        </div>
    @endif
</div>
