@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
])

@php
    $displayText = strtoupper($text ?: 'SUR LE TERRAIN');
    $len = mb_strlen($displayText);
@endphp

{{-- Square container — all positions in % so it scales to any parent size --}}
<div style="width: 100%; aspect-ratio: 1/1; background: #0047AB; position: relative; overflow: hidden; font-size: 0;" class="{{ $preview ? 'rounded-xl shadow-2xl' : '' }}">

    {{-- Grid background --}}
    <div style="position: absolute; inset: 0; opacity: 0.1; pointer-events: none; background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 8.3% 8.3%;"></div>

    {{-- Photo frame: top 6%, sides 6%, height 62% --}}
    <div style="position: absolute; top: 6%; left: 6%; right: 6%; height: 62%; background: white; padding: 1.4%; overflow: hidden;">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @else
            <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @endif
    </div>

    {{-- Text label zone: photo ends at 68%, footer starts at ~86.5% → text in between --}}
    <div style="position: absolute; top: 70%; left: 6%; right: 6%; height: 14%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
        <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.3); padding: 2% 5%; max-width: 100%; overflow: hidden; white-space: nowrap;">
            <span class="{{ $len > 25 ? 'text-[10px] sm:text-[11px]' : ($len > 18 ? 'text-[11px] sm:text-[12px]' : 'text-[12px] sm:text-[13px]') }}" style="color: white; font-weight: 900; text-transform: uppercase; letter-spacing: 0.15em; line-height: 1.3; text-align: center; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Courier New', monospace;">{{ $displayText }}</span>
        </div>
    </div>

    {{-- Footer: bottom 0, height ~13.5% --}}
    <div style="position: absolute; left: 0; right: 0; bottom: 0; height: 13.5%; background: white; border-top: 2px solid #0047AB; padding: 0 7%; display: flex; align-items: center; justify-content: space-between; overflow: hidden;">
        <span class="text-[9px] sm:text-[10px]" style="color: #0047AB; font-weight: 900; text-transform: uppercase; letter-spacing: 0.2em; font-family: 'Courier New', monospace; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80%;">{{ $companyLabel }}</span>
        <div style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $brandColor }}; flex-shrink: 0;"></div>
    </div>
</div>
