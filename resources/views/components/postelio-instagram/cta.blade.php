@props([
    'hook'        => 'Arrêtez de perdre des heures sur vos réseaux.',
    'description' => 'Postelio crée vos posts Instagram professionnels en 2 minutes. Conçu pour les artisans.',
    'cta'         => 'Commencer gratuitement →',
    'url'         => 'postelio.fr',
])

<div style="
    width: 360px; height: 450px;
    background: linear-gradient(145deg, #92400E 0%, #B45309 25%, #D97706 55%, #F59E0B 100%);
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Diagonal light streaks --}}
    <div style="
        position: absolute; top: -80px; right: -80px;
        width: 300px; height: 300px;
        background: rgba(255,255,255,0.08);
        transform: rotate(35deg);
        border-radius: 40px;
        pointer-events: none;
    "></div>
    <div style="
        position: absolute; bottom: -60px; left: -40px;
        width: 200px; height: 200px;
        background: rgba(0,0,0,0.12);
        transform: rotate(25deg);
        border-radius: 30px;
        pointer-events: none;
    "></div>

    {{-- Subtle dot grid --}}
    <div style="position: absolute; top: 24px; left: 24px; opacity: 0.15; pointer-events: none;">
        @for($r = 0; $r < 3; $r++)
            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                @for($c = 0; $c < 3; $c++)
                    <div style="width: 3px; height: 3px; border-radius: 50%; background: white;"></div>
                @endfor
            </div>
        @endfor
    </div>

    {{-- Postelio logo top --}}
    <div style="
        position: absolute; top: 28px; right: 28px;
        display: flex; align-items: center; gap: 6px;
    ">
        <div style="width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.8);"></div>
        <span style="
            color: rgba(255,255,255,0.8);
            font-size: 10px; font-weight: 800;
            letter-spacing: 0.12em; text-transform: uppercase;
        ">Postelio</span>
    </div>

    {{-- Main content --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        width: 100%; height: 100%;
        display: flex; flex-direction: column; justify-content: center;
        padding: 40px;
    ">
        {{-- Hook --}}
        <h1 style="
            color: #FFFFFF;
            font-size: 26px; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.02em;
            margin: 0 0 20px 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.15);
        ">{{ $hook }}</h1>

        {{-- Description --}}
        <p style="
            color: #9CA3AF;
            font-size: 13.5px; font-weight: 400; line-height: 1.6;
            margin: 0 0 24px 0;
            white-space: pre-line;
        ">{{ $description }}</p>

        {{-- CTA button --}}
        <div>
            <span style="
                display: inline-block;
                background: #111827;
                color: #F59E0B;
                font-size: 12px; font-weight: 800;
                letter-spacing: 0.04em;
                padding: 11px 22px; border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            ">{{ $cta }}</span>
        </div>
    </div>

    {{-- Bottom URL --}}
    <div style="
        position: absolute; bottom: 24px; left: 0; right: 0;
        display: flex; justify-content: center;
    ">
        <span style="
            color: rgba(255,255,255,0.45);
            font-size: 10px; font-weight: 600;
            letter-spacing: 0.15em; text-transform: uppercase;
        ">{{ $url }}</span>
    </div>
</div>
