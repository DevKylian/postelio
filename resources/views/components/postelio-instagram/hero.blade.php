@props([
    'tag'      => 'AÉROPORTÉ',
    'headline' => 'Postelio est enfin sur Instagram !',
    'body'     => 'Découvrez comment booster votre visibilité en 2 minutes.',
])

@php
    $titleLen = strlen($headline);
    $titleSize = $titleLen > 60 ? '22px' : ($titleLen > 40 ? '26px' : '32px');
@endphp

<div style="
    width: 360px; height: 450px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    text-align: center;
">
    {{-- Decorative glow: Center --}}
    <div style="
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 300px; height: 300px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- App Logo: Postelio SVG --}}
    <div style="margin-bottom: 24px; width: 42px; height: 42px; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 25px rgba(245,158,11,0.25);">
        <svg width="42" height="42" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FBBF24"/>
                    <stop offset="100%" stop-color="#F97316"/>
                </linearGradient>
            </defs>
            <rect width="512" height="512" fill="url(#logoGrad)"/>
            <line x1="136" y1="218" x2="136" y2="294" stroke="white" stroke-width="36" stroke-linecap="round"/>
            <line x1="196" y1="158" x2="196" y2="354" stroke="white" stroke-width="36" stroke-linecap="round"/>
            <line x1="256" y1="98" x2="256" y2="414" stroke="white" stroke-width="36" stroke-linecap="round"/>
            <line x1="316" y1="162" x2="316" y2="350" stroke="white" stroke-width="36" stroke-linecap="round"/>
            <line x1="376" y1="224" x2="376" y2="288" stroke="white" stroke-width="36" stroke-linecap="round"/>
        </svg>
    </div>

    {{-- Content --}}
    <div style="padding: 0 32px; position: relative; z-index: 10;">

        <h1 style="
            color: #FFFFFF;
            font-size: {{ $titleSize }}; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.015em;
            margin: 0 0 14px 0;
            white-space: pre-line;
        ">{!! html_entity_decode($headline, ENT_QUOTES, 'UTF-8') !!}</h1>

        <div style="width: 32px; height: 3px; background: #F59E0B; border-radius: 1.5px; margin: 0 auto 16px auto; opacity: 0.6;"></div>

        <p style="
            color: #9CA3AF;
            font-size: 14.5px; font-weight: 400;
            line-height: 1.55; margin: 0;
            white-space: pre-line;
        ">{!! html_entity_decode($body, ENT_QUOTES, 'UTF-8') !!}</p>
    </div>

    {{-- Bottom branding --}}
    <div style="
        position: absolute; bottom: 28px; left: 0; right: 0;
        opacity: 0.3;
    ">
        <span style="color: #FFFFFF; font-size: 10px; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase;">Postelio.fr</span>
    </div>
</div>
