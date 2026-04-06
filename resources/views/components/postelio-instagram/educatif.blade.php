@props([
    'tag'     => 'ÉDUCATION',
    'title'   => 'Le saviez-vous ?',
    'body'    => 'Expliquez ici un concept clé pour votre audience.',
    'insight' => 'Le point essentiel à retenir.',
])

@php
    // Title scaling (More compact and professional)
    $titleLen = strlen($title);
    $titleSize = $titleLen > 45 ? '17px' : ($titleLen > 25 ? '19px' : '22px');

    // Body scaling (Refined for a professional look)
    $bodyLen = strlen($body);
    $bodySize = $bodyLen > 180 ? '11.5px' : ($bodyLen > 100 ? '12.5px' : '13.5px');
    $bodyLH = $bodyLen > 100 ? '1.5' : '1.6';

    // Insight scaling (Subtle)
    $insightLen = strlen($insight);
    $insightSize = $insightLen > 80 ? '11px' : ($insightLen > 40 ? '12px' : '12.5px');
@endphp

<div style="
    width: 360px; height: 450px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Decorative element: Top right glow --}}
    <div style="
        position: absolute; top: -50px; right: -50px;
        width: 180px; height: 180px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.22) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Left accent line --}}
    <div style="
        position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
        background: linear-gradient(180deg, #F59E0B, #D97706);
    "></div>

    {{-- Content wrapper --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        width: 100%; height: 100%;
        padding: 32px 34px 32px 40px;
        display: flex; flex-direction: column;
    ">
        {{-- Tag --}}
        <div style="margin-bottom: 14px;">
            <span style="
                background: rgba(245,158,11,0.08);
                border: 1px solid rgba(245,158,11,0.3);
                color: #F59E0B;
                font-size: 8px; font-weight: 800;
                letter-spacing: 0.2em; text-transform: uppercase;
                padding: 4px 10px; border-radius: 4px;
            ">{{ $tag }}</span>
        </div>

        {{-- Title --}}
        <h1 style="
            color: #FFFFFF;
            font-size: {{ $titleSize }}; font-weight: 800;
            line-height: 1.3; letter-spacing: -0.01em;
            margin: 0 0 18px 0;
        ">{!! html_entity_decode($title, ENT_QUOTES, 'UTF-8') !!}</h1>

        {{-- Body --}}
        <div style="margin-bottom: 18px;">
            <p style="
                color: #9CA3AF;
                font-size: {{ $bodySize }}; font-weight: 400;
                line-height: {{ $bodyLH }}; margin: 0;
                white-space: pre-line;
            ">{!! html_entity_decode($body, ENT_QUOTES, 'UTF-8') !!}</p>
        </div>

        {{-- Insight Box --}}
        <div style="
            background: rgba(245, 158, 11, 0.05);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-left: 4px solid #F59E0B;
            padding: 18px 24px;
            border-radius: 0 12px 12px 0;
            width: 100%;
            background: rgba(245,158,11,0.1);
            border-left: 3px solid #F59E0B;
            padding: 12px 18px; border-radius: 4px 12px 12px 4px;
        ">
            <span style="display: block; color: #F59E0B; font-size: 10px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 4px;">À retenir</span>
            <p style="color: #F3F4F6; font-size: 11.5px; font-weight: 600; line-height: 1.5; margin: 0;">{!! html_entity_decode($insight, ENT_QUOTES, 'UTF-8') !!}</p>
        </div>
    </div>

    {{-- Bottom branding --}}
    <div style="
        position: absolute; bottom: 20px; right: 28px;
        opacity: 0.3;
    ">
        <span style="color: #FFFFFF; font-size: 9px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase;">Postelio.fr</span>
    </div>
</div>
