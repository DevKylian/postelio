@props([
    'number'    => '01',
    'tip_title' => 'Publiez régulièrement',
    'tip_body'  => 'Les artisans qui publient 3 fois par semaine génèrent en moyenne 40 % de demandes de devis supplémentaires via Instagram.',
    'hashtags'  => '#artisan #instagram #conseil',
])

<div style="
    width: 360px; height: 360px;
    background: #F9FAFB;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Left copper accent strip --}}
    <div style="
        position: absolute; top: 0; left: 0; bottom: 0; width: 4px;
        background: linear-gradient(180deg, #F59E0B 0%, #D97706 100%);
    "></div>

    {{-- Large background number --}}
    <div style="
        position: absolute; top: -10px; right: -10px;
        font-size: 160px; font-weight: 900; line-height: 1;
        color: rgba(245,158,11,0.07);
        letter-spacing: -0.04em;
        user-select: none; pointer-events: none;
    ">{{ $number }}</div>

    {{-- Top section: number + label --}}
    <div style="
        position: absolute; top: 32px; left: 32px; right: 24px;
        display: flex; align-items: flex-start; gap: 14px;
    ">
        <div style="
            background: #F59E0B;
            color: #111827;
            font-size: 13px; font-weight: 900; letter-spacing: 0.05em;
            padding: 6px 14px; border-radius: 8px;
            flex-shrink: 0;
        ">{{ $number }}</div>
        <div style="
            color: #9CA3AF;
            font-size: 9px; font-weight: 700;
            letter-spacing: 0.2em; text-transform: uppercase;
            padding-top: 9px;
        ">Conseil Postelio</div>
    </div>

    {{-- Main content --}}
    <div style="
        position: absolute; top: 105px; left: 32px; right: 32px;
    ">
        {{-- Tip title --}}
        <h2 style="
            color: #111827;
            font-size: 24px; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.02em;
            margin: 0 0 18px 0;
        ">{{ $tip_title }}</h2>

        {{-- Separator --}}
        <div style="
            width: 28px; height: 2px;
            background: #F59E0B;
            border-radius: 2px; margin-bottom: 18px;
        "></div>

        {{-- Tip body --}}
        <p style="
            color: #4B5563;
            font-size: 13px; font-weight: 400;
            line-height: 1.7; margin: 0;
        ">{{ $tip_body }}</p>
    </div>

    {{-- Bottom bar --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 52px;
        background: #111827;
        display: flex; align-items: center;
        padding: 0 32px;
        justify-content: space-between;
    ">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 6px; height: 6px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="
                color: #FFFFFF;
                font-size: 10px; font-weight: 800;
                letter-spacing: 0.12em; text-transform: uppercase;
            ">Postelio</span>
        </div>
        <span style="
            color: rgba(255,255,255,0.3);
            font-size: 9px; font-weight: 500;
            letter-spacing: 0.05em;
        ">{{ $hashtags }}</span>
    </div>
</div>
