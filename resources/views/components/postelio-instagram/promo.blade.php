@props([
    'tag'         => 'OFFRE LIMITÉE',
    'headline'    => 'Profitez de Postelio',
    'discount'    => '-30%',
    'subheadline' => 'Sur tous nos abonnements',
    'cta'         => 'Voir l\'offre →',
])

<div style="
    width: 360px; height: 450px;
    background: linear-gradient(165deg, #92400E 0%, #B45309 35%, #D97706 70%, #F59E0B 100%);
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Ambient Glows --}}
    <div style="
        position: absolute; top: -60px; right: -40px;
        width: 320px; height: 320px; border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        pointer-events: none; filter: blur(40px);
    "></div>
    <div style="
        position: absolute; bottom: -80px; left: -60px;
        width: 280px; height: 280px; border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 75%);
        pointer-events: none; filter: blur(30px);
    "></div>

    {{-- Main content --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        width: 100%; height: 100%;
        display: flex; flex-direction: column; justify-content: center;
        align-items: center; text-align: center;
        padding: 42px; z-index: 10;
    ">
        {{-- Tag --}}
        {{-- Tag --}}
        <div style="
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            color: #FFFFFF;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 100px;
            margin-bottom: 24px;
        ">
            {{ $tag }}
        </div>

        {{-- Headline --}}
        <h1 style="
            color: #FFFFFF;
            font-size: 26px; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.02em;
            margin: 0 0 12px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        ">{{ $headline }}</h1>

        {{-- Discount --}}
        <div style="
            font-size: 82px; font-weight: 950;
            color: #FFFFFF;
            line-height: 0.9;
            letter-spacing: -0.05em;
            margin-bottom: 12px;
            text-shadow: 0 10px 40px rgba(0,0,0,0.15);
        ">
            {{ $discount }}
        </div>

        {{-- Subheadline --}}
        <p style="
            color: rgba(255,255,255,0.9);
            font-size: 15px; font-weight: 600; line-height: 1.4;
            margin: 0 0 32px 0;
            max-width: 240px;
        ">{{ $subheadline }}</p>

        {{-- CTA button --}}
        <div style="display: flex; flex-direction: column; align-items: center; gap: 14px;">
            <span style="
                display: inline-block;
                background: #FFFFFF;
                color: #92400E;
                font-size: 14px; font-weight: 900;
                letter-spacing: -0.01em;
                padding: 14px 28px; border-radius: 14px;
                box-shadow: 0 15px 35px rgba(0,0,0,0.15);
                border: 1px solid rgba(255,255,255,0.5);
            ">{{ $cta }}</span>

            <span style="
                color: #FFFFFF;
                font-size: 11px; font-weight: 800;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                opacity: 0.9;
            ">Disponible sur postelio.fr</span>
        </div>
    </div>

    {{-- Bottom bar decoration --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 6px; background: rgba(255,255,255,0.2);
    "></div>
</div>
