@props([
    'image_url'   => null,
    'tag'         => 'FOCUS SOLUTION',
    'title'       => '3h gagnées par jour',
    'description' => 'Comment Postelio aide les artisans à automatiser leur prospection et gagner un temps précieux.',
    'benefit'     => '+40% visibilité',
])

<div style="
    width: 360px; height: 450px;
    background: #FFFFFF;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Background Image (Natural) --}}
    @if($image_url)
        <img src="{{ $image_url }}" style="
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover;
        ">
    @else
        <div style="
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
        "></div>
    @endif

    {{-- Decorative ambient glows --}}
    <div style="
        position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Central Glass Card --}}
    <div style="
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 300px;
        background: rgba(0, 0, 0, 0.18);
        backdrop-filter: blur(15px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 24px;
        padding: 32px 24px;
        display: flex; flex-direction: column; align-items: center;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    ">
        {{-- Postelio Logo Area --}}
        <div style="margin-bottom: 24px;">
            <div style="
                display: flex; align-items: center; gap: 8px;
                background: rgba(255,255,255,0.08);
                padding: 8px 16px; border-radius: 100px;
                border: 1px solid rgba(255,255,255,0.15);
            ">
                <span style="color: #FFFFFF; font-size: 11px; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase;">{!! html_entity_decode($tag, ENT_QUOTES, 'UTF-8') !!}</span>
            </div>
        </div>

        {{-- Title --}}
        <h2 style="
            color: #FFFFFF;
            font-size: 24px; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.02em;
            margin: 0 0 16px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        ">{{ $title }}</h2>

        {{-- Description --}}
        <p style="
            color: rgba(255,255,255,0.8);
            font-size: 13.5px; font-weight: 500;
            line-height: 1.5; margin: 0 0 24px 0;
        ">{{ $description }}</p>

        {{-- Benefit Badge --}}
        @if($benefit)
        <div style="
            display: inline-block;
            background: linear-gradient(135deg, #F59E0B, #D97706);
            color: #111827;
            font-size: 12px; font-weight: 900;
            padding: 8px 18px; border-radius: 12px;
            box-shadow: 0 8px 20px rgba(245,158,11,0.3);
        ">
            {{ $benefit }}
        </div>
        @endif
    </div>

    {{-- Corner Decoration --}}
    <div style="position: absolute; bottom: 20px; right: 20px; opacity: 0.3;">
        <div style="display: flex; gap: 4px; margin-bottom: 4px;">
            @for($i=0; $i<3; $i++) <div style="width: 3px; height: 3px; background: #FFF; border-radius: 50%;"></div> @endfor
        </div>
        <div style="display: flex; gap: 4px;">
            @for($i=0; $i<3; $i++) <div style="width: 3px; height: 3px; background: #FFF; border-radius: 50%;"></div> @endfor
        </div>
    </div>
</div>
