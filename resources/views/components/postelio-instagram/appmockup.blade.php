@props([
    'image_url'  => null,
    'tag'        => 'EN ACTION',
    'headline'   => 'Générez ce post en 2 minutes.',
    'subheadline'=> 'Postelio crée des visuels Instagram professionnels pour les artisans, automatiquement.',
])

<div style="
    width: 360px; height: 360px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Ambient glow --}}
    <div style="
        position: absolute; top: -30px; right: -30px;
        width: 180px; height: 180px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.2) 0%, transparent 70%);
        pointer-events: none;
    "></div>
    <div style="
        position: absolute; bottom: -40px; left: -20px;
        width: 140px; height: 140px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.08) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Top copper bar --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #F59E0B, #D97706);
    "></div>

    {{-- Top row: tag + branding --}}
    <div style="
        position: absolute; top: 16px; left: 20px; right: 20px;
        display: flex; align-items: center; justify-content: space-between;
    ">
        <span style="
            background: rgba(245,158,11,0.12);
            border: 1px solid rgba(245,158,11,0.35);
            color: #F59E0B;
            font-size: 8px; font-weight: 800;
            letter-spacing: 0.2em; text-transform: uppercase;
            padding: 3px 10px; border-radius: 100px;
        ">{{ $tag }}</span>
        <div style="display: flex; align-items: center; gap: 5px;">
            <div style="width: 4px; height: 4px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="color: rgba(255,255,255,0.3); font-size: 8px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Postelio</span>
        </div>
    </div>

    {{-- Screenshot mockup --}}
    <div style="
        position: absolute; top: 46px; left: 20px; right: 20px;
        height: 176px;
    ">
        {{-- Outer frame --}}
        <div style="
            width: 100%; height: 100%;
            border-radius: 12px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(245,158,11,0.25);
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.03);
            position: relative;
        ">
            {{-- Fake browser / app top bar --}}
            <div style="
                height: 22px;
                background: rgba(0,0,0,0.5);
                border-bottom: 1px solid rgba(255,255,255,0.06);
                display: flex; align-items: center; padding: 0 10px; gap: 5px;
            ">
                <div style="width: 6px; height: 6px; border-radius: 50%; background: rgba(239,68,68,0.6);"></div>
                <div style="width: 6px; height: 6px; border-radius: 50%; background: rgba(245,158,11,0.6);"></div>
                <div style="width: 6px; height: 6px; border-radius: 50%; background: rgba(34,197,94,0.6);"></div>
                <div style="flex: 1; height: 10px; background: rgba(255,255,255,0.06); border-radius: 3px; margin: 0 8px;"></div>
                <span style="color: rgba(255,255,255,0.2); font-size: 7px; letter-spacing: 0.05em;">postelio.fr</span>
            </div>

            {{-- Screenshot image --}}
            @if($image_url)
                <img src="{{ $image_url }}" style="width: 100%; height: calc(100% - 22px); object-fit: cover; display: block;">
            @else
                <div style="width: 100%; height: calc(100% - 22px); background: linear-gradient(135deg, #1F2937, #0F172A); display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 8px;">
                    <svg style="width: 32px; height: 32px;" fill="none" stroke="rgba(245,158,11,0.25)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span style="color: rgba(245,158,11,0.3); font-size: 9px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase;">Glisse ton screenshot ici</span>
                </div>
            @endif

            {{-- Copper corner glow --}}
            <div style="
                position: absolute; bottom: 0; right: 0;
                width: 60px; height: 60px;
                background: radial-gradient(circle at bottom right, rgba(245,158,11,0.15), transparent);
                pointer-events: none;
            "></div>
        </div>
    </div>

    {{-- Text content below screenshot --}}
    <div style="
        position: absolute; bottom: 22px; left: 20px; right: 20px;
    ">
        {{-- Separator --}}
        <div style="
            width: 24px; height: 2px;
            background: #F59E0B; border-radius: 2px;
            margin-bottom: 10px;
        "></div>

        {{-- Headline --}}
        <h2 style="
            color: #FFFFFF;
            font-size: 18px; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.02em;
            margin: 0 0 8px 0;
        ">{{ $headline }}</h2>

        {{-- Subheadline --}}
        <p style="
            color: #6B7280;
            font-size: 11px; font-weight: 400;
            line-height: 1.55; margin: 0;
        ">{{ $subheadline }}</p>
    </div>
</div>
