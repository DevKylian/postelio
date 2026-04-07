@props([
    'quote'      => 'Depuis que j\'utilise Postelio, je publie deux fois par semaine sans effort. Mes clients me disent qu\'ils m\'ont trouvé sur Instagram.',
    'author'     => 'Marc D.',
    'profession' => 'Plombier chauffagiste — Lyon',
    'stars'      => 5,
])

@php
    $len = strlen($quote);
    if ($len < 65) {
        $fontSize = '26px';
        $lineHeight = '1.3';
        $fontWeight = '800';
    } elseif ($len < 120) {
        $fontSize = '20px';
        $lineHeight = '1.45';
        $fontWeight = '500';
    } else {
        $fontSize = '15.5px';
        $lineHeight = '1.65';
        $fontWeight = '400';
    }
@endphp

<div style="
    width: 360px; height: 450px;
    background: #F9FAFB;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Decorative grid pattern for depth --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(rgba(0,0,0,0.03) 1px, transparent 1px);
        background-size: 20px 20px;
        pointer-events: none;
    "></div>

    {{-- Top soft ambient glow --}}
    <div style="
        position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
        width: 320px; height: 240px; border-radius: 50%;
        background: radial-gradient(ellipse, rgba(245,158,11,0.1) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Decorative left bar - Full Height --}}
    <div style="
        position: absolute; left: 0; top: 0; bottom: 0; width: 5px;
        background: linear-gradient(180deg, #FACC15, #F59E0B, #D97706);
        z-index: 30;
    "></div>

    {{-- Huge quote mark - Moved to a more subtle background position --}}
    <div style="
        position: absolute; top: 60px; right: 20px;
        font-size: 140px; font-weight: 900; line-height: 1;
        color: rgba(245,158,11,0.05);
        font-family: 'Outfit', sans-serif;
        user-select: none; pointer-events: none;
        z-index: 5;
    ">”</div>

    {{-- Header: Tag + Stars --}}
    <div style="
        position: absolute; top: 32px; left: 28px; right: 28px;
        display: flex; justify-content: space-between; align-items: center;
        z-index: 20;
    ">
        <span style="
            background: rgba(245,158,11,0.1);
            border: 1px solid rgba(245,158,11,0.2);
            color: #D97706;
            font-size: 8.5px; font-weight: 800;
            letter-spacing: 0.15em; text-transform: uppercase;
            padding: 4px 12px; border-radius: 100px;
        ">Avis client</span>

        <div style="display: flex; gap: 3px;">
            @for($i = 0; $i < 5; $i++)
            <svg style="width: 14px; height: 14px; color: {{ $i < $stars ? '#F59E0B' : 'rgba(0,0,0,0.05)' }};" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            @endfor
        </div>
    </div>

    {{-- Quote text (Centered Container) --}}
    <div style="
        position: absolute; top: 80px; bottom: 140px; left: 32px; right: 32px;
        display: flex; align-items: center; justify-content: center;
        z-index: 10;
    ">
        <p style="
            color: #1F2937;
            font-size: {{ $fontSize }}; 
            font-weight: {{ $fontWeight }};
            line-height: {{ $lineHeight }};
            font-style: italic;
            margin: 0;
            text-align: left;
            text-shadow: 0 1px 2px rgba(0,0,0,0.02);
            white-space: pre-line;
        ">"{{ $quote }}"</p>
    </div>

    {{-- Author block --}}
    <div style="
        position: absolute; bottom: 65px; left: 28px; right: 28px;
        display: flex; align-items: center; gap: 14px;
        z-index: 20;
    ">
        {{-- Avatar initial --}}
        <div style="
            width: 42px; height: 42px; border-radius: 51%; flex-shrink: 0;
            background: linear-gradient(135deg, #F59E0B, #D97706);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 900; color: #FFFFFF;
            box-shadow: 0 4px 10px rgba(245,158,11,0.25);
        ">{{ strtoupper(substr($author, 0, 1)) }}</div>
        <div>
            <p style="
                color: #111827; font-size: 13.5px; font-weight: 800;
                margin: 0 0 2px 0;
            ">{{ $author }}</p>
            <p style="
                color: #6B7280; font-size: 11px; font-weight: 500;
                margin: 0;
            ">{{ $profession }}</p>
        </div>
    </div>

    {{-- Bottom branding --}}
    <div style="
        position: absolute; bottom: 20px; left: 28px; right: 28px;
        display: flex; align-items: center; justify-content: space-between;
        border-top: 1px solid rgba(0,0,0,0.05);
        padding-top: 14px;
        z-index: 20;
    ">
        <div style="display: flex; align-items: center; gap: 6px;">
            <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="color: #6B7280; font-size: 10px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase;">Postelio</span>
        </div>
        <span style="color: #9CA3AF; font-size: 10px; letter-spacing: 0.05em;">postelio.fr</span>
    </div>
</div>
