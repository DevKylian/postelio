@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
])

@php
    $len = mb_strlen($text);
    $fontSize = 'text-[13px]';
    if ($len > 25) $fontSize = 'text-[10px]';
    elseif ($len > 18) $fontSize = 'text-[11px]';
@endphp

{{-- 360x360 container, all children absolutely positioned in px --}}
<div style="width: 100%; aspect-ratio: 1/1; background: #0047AB; position: relative; overflow: hidden;" class="{{ $preview ? 'rounded-xl shadow-2xl' : '' }}">

    {{-- Grid background --}}
    <div style="position: absolute; inset: 0; opacity: 0.1; pointer-events: none; background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 30px 30px;"></div>

    {{-- Photo frame: top 24px, sides 24px, stops at 200px from top (leaving room below) --}}
    <div style="position: absolute; top: 24px; left: 24px; right: 24px; bottom: 100px; background: white; padding: 5px; overflow: hidden;">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @else
            <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @endif
    </div>

    {{-- Text zone: centered between photo bottom and footer top --}}
    {{-- Photo bottom = 360-100 = 260px, Footer top = 360-48 = 312px --}}
    {{-- So text zone: top 262px, bottom 50px → height ~48px, centered --}}
    <div style="position: absolute; top: 262px; left: 24px; right: 24px; height: 48px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
        <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.3); padding: 6px 16px; max-width: 100%; overflow: hidden;">
            <span style="color: white; font-size: {{ $len > 25 ? '10px' : ($len > 18 ? '11px' : '13px') }}; font-weight: 900; text-transform: uppercase; letter-spacing: 0.15em; line-height: 1.3; text-align: center; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Courier New', monospace;">{{ $text }}</span>
        </div>
    </div>

    {{-- Footer: 48px fixed at bottom --}}
    <div style="position: absolute; left: 0; right: 0; bottom: 0; height: 48px; background: white; border-top: 2px solid #0047AB; padding: 0 28px; display: flex; align-items: center; justify-content: space-between; overflow: hidden;">
        <span style="color: #0047AB; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.2em; font-family: 'Courier New', monospace; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80%;">{{ $companyLabel }}</span>
        <div style="width: 10px; height: 10px; border-radius: 50%; background-color: {{ $brandColor }};"></div>
    </div>
</div>
