@props([
    'headline'  => 'Ce que Postelio fait pour vous',
    'items'     => [
        'Posts Instagram prêts en 2 minutes',
        'Contenu pro sans compétences graphiques',
        'IA formée pour les artisans français',
        'Publication automatique programmée',
    ],
    'tag'       => 'POURQUOI POSTELIO ?',
])

<div style="
    width: 360px; height: 360px;
    background: #F9FAFB;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Top copper stripe --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #F59E0B 0%, #D97706 100%);
    "></div>

    {{-- Background decoration: large faded circle --}}
    <div style="
        position: absolute; right: -60px; bottom: -60px;
        width: 200px; height: 200px; border-radius: 50%;
        background: rgba(245,158,11,0.06);
        pointer-events: none;
    "></div>

    {{-- Tag + headline --}}
    <div style="padding: 28px 28px 0 28px;">
        <div style="
            display: inline-block;
            background: rgba(245,158,11,0.1);
            border: 1px solid rgba(245,158,11,0.3);
            color: #D97706;
            font-size: 8px; font-weight: 800;
            letter-spacing: 0.2em; text-transform: uppercase;
            padding: 3px 10px; border-radius: 100px;
            margin-bottom: 14px;
        ">{{ $tag }}</div>

        <h2 style="
            color: #111827;
            font-size: 21px; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.02em;
            margin: 0 0 22px 0;
        ">{{ $headline }}</h2>

        {{-- Items list --}}
        <div style="display: flex; flex-direction: column; gap: 13px;">
            @foreach(array_slice((array)$items, 0, 4) as $item)
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                {{-- Check icon --}}
                <div style="
                    width: 22px; height: 22px; border-radius: 6px;
                    background: #F59E0B;
                    display: flex; align-items: center; justify-content: center;
                    flex-shrink: 0; margin-top: 1px;
                ">
                    <svg style="width: 12px; height: 12px;" fill="none" stroke="#111827" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span style="
                    color: #374151;
                    font-size: 13px; font-weight: 500;
                    line-height: 1.4;
                ">{{ $item }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Bottom bar --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 48px;
        background: #111827;
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 28px;
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
            font-size: 9px; letter-spacing: 0.1em;
        ">postelio.fr</span>
    </div>
</div>
