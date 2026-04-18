@props([
    'tag'      => 'RÉALISATION',
    'headline' => 'Rénovation complète',
    'caption'  => 'Un projet mené avec passion par nos artisans experts.',
    'image_url' => null,
])

<div style="
    width: 360px; height: 450px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Background Image --}}
    @if($image_url)
        <img src="{{ $image_url }}" style="
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover;
        ">
    @else
        <div style="
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, #1f2937, #111827);
            display: flex; align-items: center; justify-content: center;
        ">
            <svg style="width: 48px; height: 48px; color: rgba(255,255,255,0.1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
    @endif

    {{-- Gradient Overlay for readability --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0; height: 75%;
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);
        pointer-events: none;
    "></div>

    {{-- Top decoration --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #F59E0B 0%, #D97706 50%, #92400E 100%);
        z-index: 20;
    "></div>

    {{-- Main content --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        display: flex; flex-direction: column; justify-content: flex-end;
        padding: 40px 28px; z-index: 10;
    ">
        {{-- Tag --}}
        <div style="display: flex; margin-bottom: 20px;">
            <div style="
                background: #F59E0B;
                color: #111827;
                font-size: 10px; font-weight: 900;
                letter-spacing: 0.15em; text-transform: uppercase;
                padding: 6px 16px; border-radius: 6px;
                box-shadow: 0 4px 15px rgba(245,158,11,0.3);
            ">
                {{ $tag }}
            </div>
        </div>

        {{-- Headline --}}
        <h1 style="
            color: #FFFFFF;
            font-size: 28px; font-weight: 900;
            line-height: 1.1; letter-spacing: -0.02em;
            margin: 0 0 12px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        ">{{ $headline }}</h1>

        {{-- Caption --}}
        <p style="
            color: rgba(255,255,255,0.85);
            font-size: 15px; font-weight: 500; line-height: 1.5;
            margin: 0 0 24px 0;
            max-width: 280px;
        ">{{ $caption }}</p>

        {{-- Branding Footer --}}
        <div style="
            display: flex; align-items: center; gap: 8px;
            padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.1);
        ">
            <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="
                color: rgba(255,255,255,0.5);
                font-size: 10px; font-weight: 800;
                letter-spacing: 0.2em; text-transform: uppercase;
            ">Postelio.fr</span>
        </div>
    </div>
</div>
