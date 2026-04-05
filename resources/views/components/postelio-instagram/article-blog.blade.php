@props([
    'tag'     => 'NOUVEL ARTICLE',
    'title'   => 'Titre de votre article de blog',
    'excerpt' => 'Découvrez les conseils essentiels pour développer votre activité.',
    'cta'     => 'Lire l\'article',
    'url'     => 'postelio.fr/blog',
])

<div style="
    width: 360px; height: 450px;
    background: #0D1117;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Top copper gradient bar --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #F59E0B 0%, #FB923C 100%);
    "></div>

    {{-- Background decorative large quote mark --}}
    <div style="
        position: absolute; right: -10px; top: 30px;
        font-size: 220px; line-height: 1;
        font-family: Georgia, serif; font-weight: 900;
        color: rgba(245,158,11,0.045);
        user-select: none; pointer-events: none;
        letter-spacing: -0.05em;
    ">&ldquo;</div>

    {{-- Subtle glow top-left --}}
    <div style="
        position: absolute; top: -40px; left: -40px;
        width: 200px; height: 200px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.10) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Subtle glow bottom-right --}}
    <div style="
        position: absolute; bottom: -50px; right: -30px;
        width: 180px; height: 180px; border-radius: 50%;
        background: radial-gradient(circle, rgba(251,146,60,0.08) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Main content --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 54px;
        display: flex; flex-direction: column; justify-content: center;
        padding: 36px 32px 20px 32px;
    ">
        {{-- Tag badge --}}
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
            <div style="width: 16px; height: 2px; background: #F59E0B; border-radius: 2px;"></div>
            <span style="
                color: #F59E0B;
                font-size: 9px; font-weight: 800;
                letter-spacing: 0.22em; text-transform: uppercase;
            ">{{ $tag }}</span>
        </div>

        {{-- Article title — the hero element --}}
        <h1 style="
            color: #FFFFFF;
            font-size: 22px; font-weight: 900;
            line-height: 1.2; letter-spacing: -0.02em;
            margin: 0 0 18px 0;
        ">{{ $title }}</h1>

        {{-- Separator --}}
        <div style="
            width: 32px; height: 2px;
            background: linear-gradient(90deg, #F59E0B, #FB923C);
            border-radius: 2px; margin-bottom: 16px;
        "></div>

        {{-- Excerpt --}}
        <p style="
            color: #6B7280;
            font-size: 12px; font-weight: 400;
            line-height: 1.65; margin: 0 0 22px 0;
            white-space: pre-line;
        ">{{ $excerpt }}</p>

        {{-- CTA pill --}}
        <div>
            <span style="
                display: inline-flex; align-items: center; gap: 6px;
                background: rgba(245,158,11,0.12);
                border: 1px solid rgba(245,158,11,0.4);
                color: #F59E0B;
                font-size: 11px; font-weight: 800;
                letter-spacing: 0.05em;
                padding: 7px 16px; border-radius: 100px;
            ">{{ $cta }} &#x2192;</span>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 54px;
        background: rgba(245,158,11,0.06);
        border-top: 1px solid rgba(245,158,11,0.12);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 28px;
    ">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="color: #F59E0B; font-size: 10px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase;">Postelio</span>
        </div>
        <span style="color: rgba(255,255,255,0.2); font-size: 10px; letter-spacing: 0.06em;">{{ $url }}</span>
    </div>
</div>
