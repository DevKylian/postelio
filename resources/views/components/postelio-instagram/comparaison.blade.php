@props([
    'headline'      => 'La différence est réelle.',
    'col_sans'      => [
        'Heures perdues chaque semaine',
        'Posts amateurs ou inexistants',
        'Zéro stratégie de contenu',
        'Clients qui ne vous trouvent pas',
    ],
    'col_avec'      => [
        '2 minutes par post, point final',
        'Visuels pro au branding artisan',
        'Contenu cohérent et régulier',
        'Visibilité = nouveaux clients',
    ],
])

<div style="
    width: 360px; height: 450px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Top bar with headline --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0;
        padding: 32px 24px 24px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    ">
        <h2 style="
            color: #FFFFFF;
            font-size: 17px; font-weight: 900;
            letter-spacing: -0.02em; margin: 0;
            text-align: center;
        ">{{ $headline }}</h2>
    </div>

    {{-- Two columns --}}
    <div style="
        position: absolute; top: 84px; left: 0; right: 0; bottom: 64px;
        display: flex;
    ">
        {{-- SANS column --}}
        <div style="
            flex: 1;
            background: rgba(239,68,68,0.04);
            border-right: 1px solid rgba(255,255,255,0.06);
            padding: 24px 16px;
            display: flex; flex-direction: column; gap: 14px;
        ">
            <div style="
                display: flex; align-items: center; gap: 6px;
                margin-bottom: 6px;
            ">
                <div style="width: 16px; height: 16px; border-radius: 4px; background: rgba(239,68,68,0.2); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 9px; height: 9px;" fill="none" stroke="#F87171" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <span style="color: #F87171; font-size: 9px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase;">Sans Postelio</span>
            </div>

            @foreach(array_slice((array)$col_sans, 0, 4) as $item)
            <div style="display: flex; align-items: flex-start; gap: 8px;">
                <div style="width: 3px; height: 3px; border-radius: 50%; background: rgba(239,68,68,0.5); flex-shrink: 0; margin-top: 5px;"></div>
                <span style="
                    color: #6B7280;
                    font-size: 11px; font-weight: 400; line-height: 1.4;
                ">{{ $item }}</span>
            </div>
            @endforeach
        </div>

        {{-- AVEC column --}}
        <div style="
            flex: 1;
            background: rgba(245,158,11,0.04);
            padding: 24px 16px;
            display: flex; flex-direction: column; gap: 14px;
        ">
            <div style="
                display: flex; align-items: center; gap: 6px;
                margin-bottom: 6px;
            ">
                <div style="width: 16px; height: 16px; border-radius: 4px; background: rgba(245,158,11,0.25); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 9px; height: 9px;" fill="none" stroke="#F59E0B" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span style="color: #F59E0B; font-size: 9px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase;">Avec Postelio</span>
            </div>

            @foreach(array_slice((array)$col_avec, 0, 4) as $item)
            <div style="display: flex; align-items: flex-start; gap: 8px;">
                <div style="width: 3px; height: 3px; border-radius: 50%; background: #F59E0B; flex-shrink: 0; margin-top: 5px;"></div>
                <span style="
                    color: #D1D5DB;
                    font-size: 11px; font-weight: 500; line-height: 1.4;
                ">{{ $item }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Bottom branding --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 64px;
        background: #F59E0B;
        display: flex; align-items: center; justify-content: center; gap: 10px;
    ">
        <div style="width: 5px; height: 5px; border-radius: 50%; background: #111827; opacity: 0.4;"></div>
        <span style="
            color: #111827;
            font-size: 10px; font-weight: 900;
            letter-spacing: 0.15em; text-transform: uppercase;
        ">Essayez Postelio gratuitement</span>
        <div style="width: 5px; height: 5px; border-radius: 50%; background: #111827; opacity: 0.4;"></div>
    </div>
</div>
