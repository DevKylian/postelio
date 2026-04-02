@props([
    'tag'         => 'NOUVEAUTÉ',
    'headline'    => 'Votre expertise mérite d\'être vue.',
    'subheadline' => 'Créez des posts Instagram professionnels en 2 minutes.',
    'cta'         => 'Essayer gratuitement',
])

<div style="
    width: 360px; height: 360px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Ambient glow top-right --}}
    <div style="
        position: absolute; top: -60px; right: -60px;
        width: 220px; height: 220px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.35) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Ambient glow bottom-left --}}
    <div style="
        position: absolute; bottom: -40px; left: -40px;
        width: 160px; height: 160px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Top copper gradient bar --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #F59E0B 0%, #D97706 50%, #92400E 100%);
    "></div>

    {{-- Decorative corner grid dots --}}
    <div style="position: absolute; bottom: 70px; right: 24px; opacity: 0.08;">
        @for($r = 0; $r < 4; $r++)
            <div style="display: flex; gap: 6px; margin-bottom: 6px;">
                @for($c = 0; $c < 4; $c++)
                    <div style="width: 3px; height: 3px; border-radius: 50%; background: #F59E0B;"></div>
                @endfor
            </div>
        @endfor
    </div>

    {{-- Main content --}}
    <div style="
        position: absolute; inset: 0;
        display: flex; flex-direction: column; justify-content: center;
        padding: 36px 36px 50px 36px;
    ">
        {{-- Tag badge --}}
        <div style="display: inline-flex; margin-bottom: 20px;">
            <span style="
                display: inline-block;
                background: rgba(245,158,11,0.12);
                border: 1px solid rgba(245,158,11,0.5);
                color: #F59E0B;
                font-size: 9px; font-weight: 800;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                padding: 4px 12px; border-radius: 100px;
            ">{{ $tag }}</span>
        </div>

        {{-- Headline --}}
        <h1 style="
            color: #FFFFFF;
            font-size: 28px; font-weight: 900;
            line-height: 1.15; letter-spacing: -0.02em;
            margin: 0 0 16px 0;
        ">{{ $headline }}</h1>

        {{-- Separator line --}}
        <div style="
            width: 36px; height: 2px;
            background: linear-gradient(90deg, #F59E0B, #D97706);
            border-radius: 2px; margin-bottom: 16px;
        "></div>

        {{-- Subheadline --}}
        <p style="
            color: #9CA3AF;
            font-size: 13px; font-weight: 400; line-height: 1.65;
            margin: 0 0 28px 0;
        ">{{ $subheadline }}</p>

        {{-- CTA button --}}
        <div>
            <span style="
                display: inline-block;
                background: #F59E0B;
                color: #111827;
                font-size: 11px; font-weight: 800;
                letter-spacing: 0.04em;
                padding: 9px 18px; border-radius: 10px;
            ">{{ $cta }}</span>
        </div>
    </div>

    {{-- Postelio wordmark bottom right --}}
    <div style="
        position: absolute; bottom: 18px; right: 24px;
        display: flex; align-items: center; gap: 6px;
    ">
        <div style="
            width: 5px; height: 5px; border-radius: 50%;
            background: #F59E0B;
        "></div>
        <span style="
            color: rgba(255,255,255,0.35);
            font-size: 9px; font-weight: 700; letter-spacing: 0.15em;
            text-transform: uppercase;
        ">postelio.fr</span>
    </div>
</div>
