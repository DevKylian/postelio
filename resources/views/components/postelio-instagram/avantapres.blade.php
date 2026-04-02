@props([
    'image_before_url' => null,
    'image_after_url'  => null,
    'headline'         => 'Transformation complète',
    'tag'              => 'AVANT / APRÈS',
])

<div style="
    width: 360px; height: 360px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- BEFORE side (left half) --}}
    <div style="
        position: absolute; top: 0; left: 0; bottom: 52px;
        width: 179px; overflow: hidden;
    ">
        @if($image_before_url)
            <img src="{{ $image_before_url }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @else
            <div style="width: 100%; height: 100%; background: #1F2937; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 36px; height: 36px; color: rgba(255,255,255,0.1);" fill="none" stroke="rgba(255,255,255,0.1)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        {{-- Dark overlay on before --}}
        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.35);"></div>

        {{-- AVANT label --}}
        <div style="
            position: absolute; top: 14px; left: 14px;
            background: rgba(0,0,0,0.7);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 4px 10px; border-radius: 6px;
        ">
            <span style="color: rgba(255,255,255,0.7); font-size: 9px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase;">Avant</span>
        </div>
    </div>

    {{-- Center divider --}}
    <div style="
        position: absolute; top: 0; bottom: 52px; left: 50%; transform: translateX(-50%);
        width: 2px; background: #111827; z-index: 10;
    "></div>

    {{-- Center badge --}}
    <div style="
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -60%);
        z-index: 20;
        width: 34px; height: 34px; border-radius: 50%;
        background: #F59E0B;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 0 3px #111827;
    ">
        <svg style="width: 14px; height: 14px;" fill="none" stroke="#111827" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
    </div>

    {{-- AFTER side (right half) --}}
    <div style="
        position: absolute; top: 0; right: 0; bottom: 52px;
        width: 179px; overflow: hidden;
    ">
        @if($image_after_url)
            <img src="{{ $image_after_url }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        @else
            <div style="width: 100%; height: 100%; background: #1F2937; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 36px; height: 36px;" fill="none" stroke="rgba(255,255,255,0.1)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        {{-- APRÈS label --}}
        <div style="
            position: absolute; top: 14px; right: 14px;
            background: #F59E0B;
            padding: 4px 10px; border-radius: 6px;
        ">
            <span style="color: #111827; font-size: 9px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase;">Après</span>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 52px;
        background: #111827;
        border-top: 2px solid #F59E0B;
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 20px;
    ">
        {{-- Tag + headline --}}
        <div style="display: flex; flex-direction: column; gap: 2px; overflow: hidden; max-width: 240px;">
            <span style="color: #F59E0B; font-size: 8px; font-weight: 800; letter-spacing: 0.18em; text-transform: uppercase;">{{ $tag }}</span>
            <span style="color: #FFFFFF; font-size: 12px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $headline }}</span>
        </div>
        {{-- Branding --}}
        <div style="display: flex; align-items: center; gap: 5px; flex-shrink: 0;">
            <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="color: rgba(255,255,255,0.35); font-size: 9px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;">Postelio</span>
        </div>
    </div>
</div>
