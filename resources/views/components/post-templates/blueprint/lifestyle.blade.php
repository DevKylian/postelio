@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
])

@php
    $len = mb_strlen($text);
    $fontSize = 'text-sm';
    if ($len > 25) $fontSize = 'text-[10px]';
    elseif ($len > 18) $fontSize = 'text-xs';
@endphp

<div class="w-full aspect-square bg-[#0047AB] relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">

    {{-- Background Grid --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none"
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 30px 30px;">
    </div>

    {{-- Photo frame — fixed position with absolute px values --}}
    <div class="absolute overflow-hidden bg-white" style="top: 28px; left: 28px; right: 28px; bottom: 88px; padding: 6px;">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
        @else
            <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" class="w-full h-full object-cover">
        @endif
    </div>

    {{-- Text band — between photo and footer, 44px tall --}}
    <div class="absolute overflow-hidden flex items-center justify-center" style="left: 28px; right: 28px; bottom: 44px; height: 44px;">
        <div class="bg-white/15 backdrop-blur-sm border border-white/30 px-5 py-1.5 max-w-full overflow-hidden">
            <span class="text-white {{ $fontSize }} font-black font-mono-technical uppercase tracking-[0.15em] leading-snug text-center block truncate">{{ $text }}</span>
        </div>
    </div>

    {{-- Footer — fixed 44px at bottom --}}
    <div class="absolute overflow-hidden bg-white flex items-center justify-between" style="left: 0; right: 0; bottom: 0; height: 44px; padding: 0 28px; border-top: 2px solid #0047AB;">
        <span class="text-[#0047AB] text-[10px] font-black font-mono-technical uppercase tracking-[0.2em] truncate max-w-[80%]">{{ $companyLabel }}</span>
        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $brandColor }}"></div>
    </div>
</div>
