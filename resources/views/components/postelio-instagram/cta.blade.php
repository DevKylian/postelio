@props([
    'hook'        => 'Arrêtez de perdre des heures sur vos réseaux.',
    'description' => 'Postelio crée vos posts Instagram professionnels en 2 minutes. Conçu pour les artisans.',
    'cta'         => 'Commencer gratuitement →',
    'url'         => 'postelio.fr',
])

<div style="
    width: 360px; height: 450px;
    background: linear-gradient(165deg, #92400E 0%, #B45309 35%, #D97706 70%, #F59E0B 100%);
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Ambient Glows (Modern "Blobs") --}}
    <div style="
        position: absolute; top: -80px; right: -60px;
        width: 300px; height: 300px; border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        pointer-events: none; filter: blur(30px);
    "></div>
    <div style="
        position: absolute; bottom: -40px; left: -30px;
        width: 260px; height: 260px; border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 75%);
        pointer-events: none; filter: blur(25px);
    "></div>

    {{-- Postelio Branding --}}
    <div style="
        position: absolute; top: 32px; left: 32px;
        display: flex; align-items: center; gap: 8px;
    ">
        <div style="width: 24px; height: 2px; background: rgba(255,255,255,0.5); border-radius: 1px;"></div>
        <span style="
            color: rgba(255,255,255,0.7);
            font-size: 9px; font-weight: 800;
            letter-spacing: 0.25em; text-transform: uppercase;
        ">Postelio</span>
    </div>

    {{-- Main content --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        width: 100%; height: 100%;
        display: flex; flex-direction: column; justify-content: center;
        padding: 42px; z-index: 10;
    ">
        {{-- Hook --}}
        <h1 style="
            color: #FFFFFF;
            font-size: 28px; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.02em;
            margin: 0 0 16px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        ">{{ $hook }}</h1>

        {{-- Description --}}
        <p style="
            color: rgba(255,255,255,0.85);
            font-size: 14.5px; font-weight: 500; line-height: 1.6;
            margin: 0 0 32px 0;
        ">{{ $description }}</p>

        {{-- CTA button --}}
        <div style="display: flex;">
            <span style="
                display: inline-block;
                background: #FFFFFF;
                color: #92400E;
                font-size: 13.5px; font-weight: 800;
                letter-spacing: -0.01em;
                padding: 13px 26px; border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.12);
                border: 1px solid rgba(255,255,255,0.3);
            ">{{ $cta }}</span>
        </div>
    </div>

    {{-- Bottom URL bar --}}
    <div style="
        position: absolute; bottom: 32px; left: 42px; right: 42px;
        display: flex; align-items: center; justify-content: space-between;
    ">
        <span style="
            color: rgba(255,255,255,0.4);
            font-size: 10px; font-weight: 700;
            letter-spacing: 0.15em; text-transform: uppercase;
        ">{{ $url }}</span>
        <div style="width: 40px; height: 1px; background: rgba(255,255,255,0.25);"></div>
    </div>
</div>

