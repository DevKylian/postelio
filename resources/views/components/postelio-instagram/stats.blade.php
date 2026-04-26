@props([
    'stat'             => '87%',
    'stat_label'       => 'des artisans',
    'stat_description' => 'n\'ont aucune présence active sur les réseaux sociaux. Ne faites pas partie de la majorité.',
    'insight'          => 'Et si vous étiez différent ?',
])

@php
    $cleanStat = html_entity_decode($stat, ENT_QUOTES, 'UTF-8');
    $cleanLabel = html_entity_decode($stat_label, ENT_QUOTES, 'UTF-8');
    $cleanDesc = html_entity_decode($stat_description, ENT_QUOTES, 'UTF-8');
    $cleanInsight = html_entity_decode($insight, ENT_QUOTES, 'UTF-8');

    // Stat number scaling
    $statLen = strlen($cleanStat);
    $statSize = $statLen > 4 ? '68px' : '84px';

    // Label scaling
    $labelLen = strlen($cleanLabel);
    $labelSize = $labelLen > 25 ? '10px' : '11.5px';

    // Description scaling
    $descLen = strlen($cleanDesc);
    $descSize = $descLen > 100 ? '11px' : '13.5px';
    $descLH = $descLen > 100 ? '1.45' : '1.55';

    // Insight scaling
    $insightLen = strlen($cleanInsight);
    $insightSize = $insightLen > 45 ? '10.5px' : '12px';
@endphp

<div style="
    width: 360px; height: 450px;
    background: radial-gradient(circle at top right, #1E293B 0%, #0F172A 100%);
    position: relative; overflow: hidden;
    color: #FFFFFF;
    font-family: 'Outfit', 'Inter', sans-serif;
">
    {{-- Abstract Data Pattern --}}
    <div style="position: absolute; inset: 0; opacity: 0.07; pointer-events: none;">
        <svg width="100%" height="100%" viewBox="0 0 360 450" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,350 Q90,300 180,350 T360,300" fill="none" stroke="#F59E0B" stroke-width="2"/>
            <path d="M0,380 Q90,330 180,380 T360,330" fill="none" stroke="#F59E0B" stroke-width="1"/>
            <path d="M0,410 Q90,360 180,410 T360,360" fill="none" stroke="#F59E0B" stroke-width="0.5"/>
            @for($i=0; $i<15; $i++)
                <circle cx="{{ rand(0, 360) }}" cy="{{ rand(0, 450) }}" r="{{ rand(1, 3) }}" fill="#F59E0B"/>
            @endfor
        </svg>
    </div>

    {{-- Subtle Blueprint Grid --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background-image: linear-gradient(rgba(245, 158, 11, 0.05) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(245, 158, 11, 0.05) 1px, transparent 1px);
        background-size: 30px 30px;
        pointer-events: none;
    "></div>

    {{-- Main Content Wrapper --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        display: flex; flex-direction: column; align-items: center;
        padding: 40px 32px 36px 32px;
    ">
        {{-- Top Badge --}}
        <div style="margin-bottom: 20px;">
            <span style="
                background: rgba(245, 158, 11, 0.12);
                border: 1px solid rgba(245, 158, 11, 0.3);
                color: #F59E0B;
                font-size: 8.5px; font-weight: 800;
                letter-spacing: 0.12em; text-transform: uppercase;
                padding: 4px 12px; border-radius: 100px;
            ">Analyse du marché</span>
        </div>

        {{-- Big Stat Value --}}
        <div style="position: relative; margin-bottom: 8px;">
            <div style="
                position: absolute; top: 4px; left: 4px;
                font-size: {{ $statSize }}; font-weight: 900; line-height: 1;
                letter-spacing: -0.04em; color: rgba(0,0,0,0.4);
                filter: blur(8px);
            ">{{ $cleanStat }}</div>
            <div style="
                position: relative;
                font-size: {{ $statSize }}; font-weight: 900; line-height: 1;
                letter-spacing: -0.04em;
                background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            ">{{ $cleanStat }}</div>
        </div>

        {{-- Stat Context --}}
        <div style="
            color: rgba(255,255,255,0.45);
            font-size: {{ $labelSize }}; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            margin-bottom: 18px; text-align: center;
        ">{{ $cleanLabel }}</div>

        {{-- Divider --}}
        <div style="width: 32px; height: 2px; background: linear-gradient(90deg, #F59E0B, #D97706); border-radius: 2px; margin-bottom: 18px;"></div>

        {{-- Description --}}
        <p style="
            color: #E2E8F0;
            font-size: {{ $descSize }}; font-weight: 400; line-height: {{ $descLH }};
            margin: 0 0 20px 0;
            text-align: center;
            white-space: pre-line;
        ">{!! $cleanDesc !!}</p>

        {{-- Insight Box (Premium Callout) --}}
        <div style="
            background: rgba(245, 158, 11, 0.05);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-left: 4px solid #F59E0B;
            padding: 18px 24px;
            border-radius: 0 12px 12px 0;
            width: 100%;
            flex-shrink: 0;
            margin-top: 24px;
            backdrop-filter: blur(4px);
        ">
            <div style="color: #F59E0B; font-size: 8px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 6px;">Point Expert</div>
            <p style="
                color: #FFFFFF;
                font-size: {{ $insightSize }}; font-weight: 700;
                line-height: 1.45; margin: 0;
            ">{!! $cleanInsight !!}</p>
        </div>
    </div>

    {{-- Wordmark --}}
    <div style="
        position: absolute; bottom: 20px; left: 0; right: 0;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        opacity: 0.4; pointer-events: none;
    ">
        <div style="width: 4px; height: 4px; border-radius: 50%; background: #F59E0B;"></div>
        <span style="color: #FFFFFF; font-size: 9px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase;">Postelio</span>
    </div>
</div>
