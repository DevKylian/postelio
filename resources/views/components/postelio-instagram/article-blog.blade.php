@props([
    'tag'     => 'ARTICLE DE BLOG',
    'title'   => 'Titre de votre article de blog',
    'excerpt' => 'Découvrez les conseils essentiels pour développer votre activité.',
    'cta'     => 'Lire l\'article',
    'url'     => 'postelio.fr/blog',
])

<div style="
    width: 360px; height: 450px;
    background: #0B1120;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- ═══ HERO IMAGE ZONE (top 48%) ═══ --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 216px;
        background: linear-gradient(135deg, #1A2540 0%, #0F1A30 40%, #1C1005 100%);
        overflow: hidden;
    ">
        {{-- Warm glow (simulates a backlit photo) --}}
        <div style="
            position: absolute; bottom: -30px; left: 50%; transform: translateX(-50%);
            width: 300px; height: 200px;
            background: radial-gradient(ellipse, rgba(245,158,11,0.22) 0%, transparent 70%);
        "></div>
        <div style="
            position: absolute; top: -20px; right: -20px;
            width: 180px; height: 180px; border-radius: 50%;
            background: radial-gradient(circle, rgba(251,146,60,0.15) 0%, transparent 65%);
        "></div>

        {{-- Decorative grid dots --}}
        <div style="position: absolute; top: 18px; left: 20px; opacity: 0.12;">
            @for($r = 0; $r < 3; $r++)
            <div style="display: flex; gap: 7px; margin-bottom: 7px;">
                @for($c = 0; $c < 5; $c++)
                <div style="width: 3px; height: 3px; border-radius: 50%; background: #F59E0B;"></div>
                @endfor
            </div>
            @endfor
        </div>

        {{-- Top bar --}}
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, #F59E0B, #FB923C);"></div>

        {{-- Blog label top-right --}}
        <div style="
            position: absolute; top: 16px; right: 20px;
            display: flex; align-items: center; gap: 5px;
        ">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" style="opacity:0.5;">
                <path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"
                    stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span style="color: rgba(245,158,11,0.6); font-size: 9px; font-weight: 700;
                letter-spacing: 0.18em; text-transform: uppercase;">Blog</span>
        </div>

        {{-- Article tag badge — bottom of image zone --}}
        <div style="
            position: absolute; bottom: 18px; left: 22px;
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(245,158,11,0.15);
            border: 1px solid rgba(245,158,11,0.5);
            padding: 4px 12px; border-radius: 100px;
        ">
            <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="color: #F59E0B; font-size: 9px; font-weight: 800;
                letter-spacing: 0.16em; text-transform: uppercase;">{{ $tag }}</span>
        </div>
    </div>

    {{-- ═══ CONTENT ZONE (bottom 52%) ═══ --}}
    <div style="
        position: absolute; top: 216px; left: 0; right: 0; bottom: 0;
        background: #0B1120;
        border-top: 1px solid rgba(245,158,11,0.2);
        padding: 20px 24px 18px 24px;
        display: flex; flex-direction: column; justify-content: space-between;
    ">
        <div>
            {{-- Title --}}
            <h1 style="
                color: #F8FAFC; font-size: 18px; font-weight: 900;
                line-height: 1.25; letter-spacing: -0.02em;
                margin: 0 0 10px 0;
            ">{{ $title }}</h1>

            {{-- Excerpt --}}
            <p style="
                color: #64748B; font-size: 11px; font-weight: 400;
                line-height: 1.65; margin: 0;
                display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
                overflow: hidden;
            ">{{ $excerpt }}</p>
        </div>

        {{-- Bottom row --}}
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="
                display: inline-flex; align-items: center; gap: 5px;
                background: #F59E0B; color: #0B1120;
                font-size: 10px; font-weight: 900; letter-spacing: 0.05em;
                padding: 8px 16px; border-radius: 7px;
            ">{{ $cta }} &#x2192;</span>

            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 4px; height: 4px; border-radius: 50%; background: rgba(245,158,11,0.5);"></div>
                <span style="color: rgba(255,255,255,0.2); font-size: 9px; font-weight: 600;
                    letter-spacing: 0.1em; text-transform: uppercase;">Postelio</span>
            </div>
        </div>
    </div>
</div>
