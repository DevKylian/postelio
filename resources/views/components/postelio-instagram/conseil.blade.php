@props([
    'number'    => '01',
    'tip_title' => 'Publiez régulièrement',
    'tip_body'  => 'Les artisans qui publient 3 fois par semaine génèrent en moyenne 40 % de demandes de devis supplémentaires via Instagram.',
    'hashtags'  => '#artisan #instagram #conseil',
    'show_swipe' => false,
])

@php
    $cleanTitle = html_entity_decode($tip_title, ENT_QUOTES, 'UTF-8');
    $cleanBody  = html_entity_decode($tip_body, ENT_QUOTES, 'UTF-8');

    // Title scaling (Focus on impact)
    $titleLen = mb_strlen($cleanTitle);
    $titleSize = $titleLen > 50 ? '18px' : ($titleLen > 30 ? '20px' : '23px');

    // Body scaling (Ensure readability)
    $bodyLen = mb_strlen($cleanBody);
    $bodySize = $bodyLen > 180 ? '12px' : ($bodyLen > 100 ? '14px' : '15.5px');
    $bodyLH = $bodyLen > 100 ? '1.45' : '1.6';

    $cleanHashtags = html_entity_decode((string)$hashtags, ENT_QUOTES, 'UTF-8');
@endphp

<div style="
    width: 360px; height: 450px;
    background: #FDFEFE;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Background decorative elements: soft glow --}}
    <div style="
        position: absolute; top: -60px; right: -60px;
        width: 220px; height: 220px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.08) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Subtle Blueprint Grid --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(rgba(0,0,0,0.02) 1px, transparent 1px);
        background-size: 24px 24px;
        pointer-events: none;
    "></div>

    {{-- Left accent strip --}}
    <div style="
        position: absolute; top: 0; left: 0; bottom: 0; width: 5px;
        background: linear-gradient(180deg, #F59E0B 0%, #D97706 100%);
    "></div>

    {{-- Large background number (Watermark style) --}}
    <div style="
        position: absolute; bottom: 60px; right: -20px;
        font-size: 180px; font-weight: 900; line-height: 1;
        color: rgba(245,158,11,0.05);
        letter-spacing: -0.05em;
        user-select: none; pointer-events: none;
    ">{{ $number }}</div>

    {{-- Content Layout --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        padding: 40px 36px 48px 40px;
        display: flex; flex-direction: column;
    ">
        {{-- Top Badge: Number + Label --}}
        <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 32px;">
            <div style="
                background: linear-gradient(135deg, #F59E0B, #D97706);
                color: #FFFFFF;
                font-size: 12px; font-weight: 900;
                width: 32px; height: 32px;
                display: flex; align-items: center; justify-content: center;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(245,158,11,0.15);
            ">{{ $number }}</div>
            <div style="
                color: #9CA3AF;
                font-size: 9px; font-weight: 800;
                letter-spacing: 0.15em; text-transform: uppercase;
            ">Conseil d'expert</div>
        </div>

        {{-- Main Content Section --}}
        <div style="flex-grow: 1;">
            {{-- Title --}}
            <h2 style="
                color: #111827;
                font-size: {{ $titleSize }}; font-weight: 900;
                line-height: 1.2; letter-spacing: -0.02em;
                margin: 0 0 16px 0;
            ">{!! $cleanTitle !!}</h2>

            {{-- Separator Line --}}
            <div style="
                width: 32px; height: 3px;
                background: #F59E0B;
                border-radius: 100px;
                margin-bottom: 24px;
            "></div>

            {{-- Body --}}
            <p style="
                color: #374151; {{-- Gray 700 for much better contrast --}}
                font-size: {{ $bodySize }}; font-weight: 400;
                line-height: {{ $bodyLH }};
                margin: 0;
                white-space: pre-line;
            ">{!! $cleanBody !!}</p>
        </div>

        {{-- Branding Footer --}}
        <div style="
            display: flex; align-items: center; justify-content: space-between;
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 20px;
            margin-top: 24px;
        ">
            <div style="display: flex; align-items: center; gap: 8px;">
                <div style="width: 6px; height: 6px; border-radius: 50%; background: #F59E0B;"></div>
                <span style="
                    color: #111827;
                    font-size: 10px; font-weight: 900;
                    letter-spacing: 0.12em; text-transform: uppercase;
                ">Postelio</span>
            </div>
            
            @if(!empty($cleanHashtags))
            <span style="
                color: #9CA3AF;
                font-size: 8.5px; font-weight: 600;
                letter-spacing: 0.02em;
            ">{{ $cleanHashtags }}</span>
            @endif
        </div>

        {{-- Swipe Indicator --}}
        @if($show_swipe)
        <div style="
            position: absolute; right: 36px; bottom: 85px;
            display: flex; align-items: center; gap: 8px;
            background: rgba(245,115,11,0.05);
            padding: 6px 14px; border-radius: 100px;
            border: 1px solid rgba(245,158,11,0.2);
            animation: bounce-x 2s infinite ease-in-out;
        ">
            <span style="color: #D97706; font-size: 8.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">Suivant</span>
            <svg style="width: 12px; height: 12px;" fill="none" stroke="#D97706" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </div>

        <style>
            @keyframes bounce-x {
                0%, 100% { transform: translateX(0); }
                50% { transform: translateX(6px); }
            }
        </style>
        @endif
    </div>
</div>
