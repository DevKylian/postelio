@props([
    'image_url'           => null,
    'feature_tag'         => 'FONCTIONNALITÉ',
    'feature_name'        => 'Générateur de Posts IA',
    'feature_description' => 'Publiez des visuels professionnels en 30 secondes, sans compétences en design.',
    'feature_cta'         => 'Essayer gratuitement',
])

<div style="
    width: 360px; height: 450px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- ── Image zone : top 55 % ──────────────────────────────────────────── --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0;
        height: 248px; overflow: hidden;
    ">
        @if($image_url)
            <img src="{{ $image_url }}" style="
                width: 100%; height: 100%; object-fit: cover; display: block;
            ">
        @else
            <div style="
                width: 100%; height: 100%;
                background: linear-gradient(135deg, #1C2634 0%, #0D1520 100%);
                display: flex; align-items: center; justify-content: center;
                flex-direction: column; gap: 10px;
            ">
                <svg style="width: 38px; height: 38px;" fill="none" stroke="rgba(245,158,11,0.22)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span style="
                    color: rgba(245,158,11,0.22); font-size: 9.5px; font-weight: 700;
                    letter-spacing: 0.14em; text-transform: uppercase;
                ">Screenshot de la feature</span>
            </div>
        @endif

        {{-- Copper top bar --}}
        <div style="
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, #FCD34D 0%, #F59E0B 50%, #D97706 100%);
        "></div>

        {{-- Soft gradient fade → dark at bottom of image --}}
        <div style="
            position: absolute; bottom: 0; left: 0; right: 0; height: 90px;
            background: linear-gradient(to bottom, transparent 0%, rgba(17,24,39,0.65) 100%);
            pointer-events: none;
        "></div>

    </div>

    {{-- ── Text content : bottom zone ─────────────────────────────────────── --}}
    <div style="
        position: absolute; top: 248px; left: 0; right: 0; bottom: 0;
        padding: 0 24px 28px 24px;
        display: flex; flex-direction: column; justify-content: flex-end;
    ">
        {{-- Copper accent bar --}}
        <div style="
            width: 28px; height: 2px;
            background: linear-gradient(90deg, #F59E0B, #D97706);
            border-radius: 2px; margin-bottom: 13px;
        "></div>

        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <span style="color: #F59E0B; font-size: 9px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase;">{!! html_entity_decode($feature_tag, ENT_QUOTES, 'UTF-8') !!}</span>
        </div>

        {{-- Feature name --}}
        <h2 style="
            color: #FFFFFF;
            font-size: 22px; font-weight: 900;
            line-height: 1.15; letter-spacing: -0.02em;
            margin: 0 0 10px 0;
        ">{!! html_entity_decode($feature_name, ENT_QUOTES, 'UTF-8') !!}</h2>

        {{-- Feature description --}}
        <p style="
            color: #9CA3AF;
            font-size: 12px; font-weight: 400;
            line-height: 1.65; margin: 0 0 20px 0;
            white-space: pre-line;
        ">{!! html_entity_decode($feature_description, ENT_QUOTES, 'UTF-8') !!}</p>

        {{-- CTA row --}}
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="
                display: inline-block;
                background: #F59E0B;
                color: #111827;
                font-size: 11px; font-weight: 800;
                letter-spacing: 0.04em;
                padding: 9px 20px; border-radius: 9px;
            ">{!! html_entity_decode($feature_cta, ENT_QUOTES, 'UTF-8') !!}</span>
            <span style="
                color: rgba(255,255,255,0.18); font-size: 9px; font-weight: 600;
                letter-spacing: 0.1em; text-transform: uppercase;
            ">postelio.fr</span>
        </div>
    </div>

    {{-- Ambient copper glow bottom-left --}}
    <div style="
        position: absolute; bottom: -35px; left: -35px;
        width: 130px; height: 130px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.07) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Decorative dots grid – right edge at image/text junction --}}
    <div style="position: absolute; top: 322px; right: -10px; opacity: 0.18;">
        @for($r = 0; $r < 5; $r++)
            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                @for($c = 0; $c < 4; $c++)
                    <div style="width: 4px; height: 4px; border-radius: 50%; background: #F59E0B;"></div>
                @endfor
            </div>
        @endfor
    </div>
</div>
