@props([
    'stat'             => '87%',
    'stat_label'       => 'des artisans',
    'stat_description' => 'n\'ont aucune présence active sur les réseaux sociaux. Ne faites pas partie de la majorité.',
    'insight'          => 'Et si vous étiez différent ?',
])

<div style="
    width: 360px; height: 360px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Horizontal lines decoration --}}
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; opacity: 0.04;">
        @for($i = 0; $i < 8; $i++)
            <div style="position: absolute; left: 0; right: 0; height: 1px; background: #F59E0B; top: {{ $i * 50 }}px;"></div>
        @endfor
    </div>

    {{-- Background big glow behind stat --}}
    <div style="
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -60%);
        width: 300px; height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.18) 0%, transparent 65%);
        pointer-events: none;
    "></div>

    {{-- Top label --}}
    <div style="
        position: absolute; top: 28px; left: 0; right: 0;
        display: flex; justify-content: center;
    ">
        <div style="
            display: flex; align-items: center; gap: 8px;
            background: rgba(245,158,11,0.1);
            border: 1px solid rgba(245,158,11,0.25);
            padding: 5px 16px; border-radius: 100px;
        ">
            <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="
                color: #F59E0B;
                font-size: 9px; font-weight: 800;
                letter-spacing: 0.2em; text-transform: uppercase;
            ">Chiffre clé · Postelio</span>
        </div>
    </div>

    {{-- Big stat number --}}
    <div style="
        position: absolute; top: 80px; left: 0; right: 0;
        text-align: center;
    ">
        <div style="
            font-size: 96px; font-weight: 900; line-height: 1;
            letter-spacing: -0.04em;
            background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 40%, #D97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
        ">{{ $stat }}</div>
        <div style="
            color: rgba(255,255,255,0.5);
            font-size: 13px; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            margin-top: 4px;
        ">{{ $stat_label }}</div>
    </div>

    {{-- Thin separator --}}
    <div style="
        position: absolute; top: 230px; left: 50%; transform: translateX(-50%);
        width: 40px; height: 2px;
        background: linear-gradient(90deg, transparent, #F59E0B, transparent);
    "></div>

    {{-- Description --}}
    <div style="
        position: absolute; top: 244px; left: 32px; right: 32px;
        text-align: center;
    ">
        <p style="
            color: #9CA3AF;
            font-size: 12px; font-weight: 400;
            line-height: 1.65; margin: 0 0 14px 0;
        ">{{ $stat_description }}</p>
        <p style="
            color: #F59E0B;
            font-size: 12px; font-weight: 700;
            letter-spacing: 0.02em; margin: 0;
        ">{{ $insight }}</p>
    </div>

    {{-- Bottom branding --}}
    <div style="
        position: absolute; bottom: 18px; left: 0; right: 0;
        display: flex; justify-content: center; align-items: center; gap: 8px;
    ">
        <div style="width: 16px; height: 1px; background: rgba(255,255,255,0.15);"></div>
        <span style="
            color: rgba(255,255,255,0.2);
            font-size: 9px; font-weight: 700;
            letter-spacing: 0.18em; text-transform: uppercase;
        ">postelio.fr</span>
        <div style="width: 16px; height: 1px; background: rgba(255,255,255,0.15);"></div>
    </div>
</div>
