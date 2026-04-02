@props([
    'image_url' => null,
    'tag'       => 'RÉALISATION',
    'headline'  => 'Un travail soigné, livré dans les délais.',
    'caption'   => 'Chaque chantier raconte notre savoir-faire.',
])

<div style="
    width: 360px; height: 450px;
    background: #1F2937;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Full-bleed image --}}
    @if($image_url)
        <img src="{{ $image_url }}" style="
            position: absolute; inset: 0;
            width: 100%; height: 100%;
            object-fit: cover; display: block;
        ">
    @else
        <div style="
            position: absolute; inset: 0;
            background: linear-gradient(135deg, #1F2937, #374151);
            display: flex; align-items: center; justify-content: center;
        ">
            <svg style="width: 56px; height: 56px;" fill="none" stroke="rgba(255,255,255,0.1)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
    @endif

    {{-- Strong gradient overlay bottom 75% --}}
    <div style="
        position: absolute; left: 0; right: 0; bottom: 0;
        height: 75%;
        background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.6) 55%, transparent 100%);
        pointer-events: none;
    "></div>

    {{-- Subtle top gradient for tag readability --}}
    <div style="
        position: absolute; left: 0; right: 0; top: 0;
        height: 100px;
        background: linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, transparent 100%);
        pointer-events: none;
    "></div>

    {{-- Top copper bar --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #F59E0B, #D97706);
    "></div>

    {{-- Tag badge top-left --}}
    <div style="position: absolute; top: 24px; left: 24px;">
        <span style="
            display: inline-block;
            background: #F59E0B;
            color: #111827;
            font-size: 10px; font-weight: 800;
            letter-spacing: 0.18em; text-transform: uppercase;
            padding: 4px 14px; border-radius: 6px;
        ">{{ $tag }}</span>
    </div>

    {{-- Postelio wordmark top-right --}}
    <div style="
        position: absolute; top: 26px; right: 24px;
        display: flex; align-items: center; gap: 6px;
    ">
        <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
        <span style="color: rgba(255,255,255,0.6); font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;">Postelio</span>
    </div>

    {{-- Bottom text content --}}
    <div style="
        position: absolute; bottom: 28px; left: 24px; right: 24px;
    ">
        {{-- Separator --}}
        <div style="
            width: 28px; height: 2px;
            background: #F59E0B;
            border-radius: 2px; margin-bottom: 14px;
        "></div>

        {{-- Headline --}}
        @php
            $len = mb_strlen($headline);
            $fs = $len > 60 ? '17px' : ($len > 40 ? '20px' : '24px');
        @endphp
        <h1 style="
            color: #FFFFFF;
            font-size: {{ $fs }}; font-weight: 900;
            line-height: 1.25; letter-spacing: -0.02em;
            margin: 0 0 10px 0;
        ">{{ $headline }}</h1>

        {{-- Caption --}}
        <p style="
            color: rgba(255,255,255,0.65);
            font-size: 13px; font-weight: 400;
            line-height: 1.55; margin: 0;
        ">{{ $caption }}</p>
    </div>
</div>
