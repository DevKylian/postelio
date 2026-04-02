@props([
    'image_before_url' => null,
    'image_after_url'  => null,
    'headline'         => 'La différence est visible.',
    'tag'              => 'AVANT / APRÈS POSTELIO',
])

<div style="
    width: 360px; height: 360px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- SANS POSTELIO side (left) --}}
    <div style="
        position: absolute; top: 0; left: 0; bottom: 52px;
        width: 179px; overflow: hidden;
    ">
        @if($image_before_url)
            <img src="{{ $image_before_url }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @else
            <div style="width: 100%; height: 100%; background: #1a1a2e; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 8px;">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="rgba(239,68,68,0.3)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <span style="color: rgba(239,68,68,0.4); font-size: 9px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Screenshot</span>
            </div>
        @endif

        {{-- Red overlay --}}
        <div style="position: absolute; inset: 0; background: rgba(127,29,29,0.45);"></div>

        {{-- SANS POSTELIO label --}}
        <div style="
            position: absolute; bottom: 14px; left: 0; right: 0;
            display: flex; justify-content: center;
        ">
            <div style="
                background: rgba(0,0,0,0.75);
                border: 1px solid rgba(239,68,68,0.4);
                padding: 4px 10px; border-radius: 6px;
                display: flex; align-items: center; gap: 5px;
            ">
                <svg style="width: 9px; height: 9px;" fill="none" stroke="#F87171" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span style="color: #F87171; font-size: 8px; font-weight: 800; letter-spacing: 0.14em; text-transform: uppercase;">Sans Postelio</span>
            </div>
        </div>
    </div>

    {{-- Center divider --}}
    <div style="
        position: absolute; top: 0; bottom: 52px; left: 50%; transform: translateX(-50%);
        width: 2px; background: #111827; z-index: 10;
    "></div>

    {{-- Center arrow badge --}}
    <div style="
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -60%);
        z-index: 20;
        width: 32px; height: 32px; border-radius: 50%;
        background: #F59E0B;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 0 3px #111827, 0 0 12px rgba(245,158,11,0.4);
    ">
        <svg style="width: 13px; height: 13px;" fill="none" stroke="#111827" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
    </div>

    {{-- AVEC POSTELIO side (right) --}}
    <div style="
        position: absolute; top: 0; right: 0; bottom: 52px;
        width: 179px; overflow: hidden;
    ">
        @if($image_after_url)
            <img src="{{ $image_after_url }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @else
            <div style="width: 100%; height: 100%; background: #111827; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 8px;">
                <svg style="width: 28px; height: 28px;" fill="none" stroke="rgba(245,158,11,0.3)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span style="color: rgba(245,158,11,0.4); font-size: 9px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Screenshot</span>
            </div>
        @endif

        {{-- Subtle copper overlay --}}
        <div style="position: absolute; inset: 0; background: rgba(120,53,15,0.2);"></div>

        {{-- AVEC POSTELIO label --}}
        <div style="
            position: absolute; bottom: 14px; left: 0; right: 0;
            display: flex; justify-content: center;
        ">
            <div style="
                background: #F59E0B;
                padding: 4px 10px; border-radius: 6px;
                display: flex; align-items: center; gap: 5px;
            ">
                <svg style="width: 9px; height: 9px;" fill="none" stroke="#111827" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span style="color: #111827; font-size: 8px; font-weight: 800; letter-spacing: 0.14em; text-transform: uppercase;">Avec Postelio</span>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 52px;
        background: #111827;
        border-top: 1px solid rgba(255,255,255,0.06);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 18px;
    ">
        <div style="display: flex; flex-direction: column; gap: 2px; overflow: hidden; max-width: 230px;">
            <span style="color: #F59E0B; font-size: 8px; font-weight: 800; letter-spacing: 0.16em; text-transform: uppercase;">{{ $tag }}</span>
            <span style="color: #FFFFFF; font-size: 12px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $headline }}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 5px; flex-shrink: 0;">
            <div style="width: 4px; height: 4px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="color: rgba(255,255,255,0.3); font-size: 8px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;">Postelio</span>
        </div>
    </div>
</div>
