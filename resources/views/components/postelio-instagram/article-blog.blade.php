@props([
    'tag'     => 'NOUVEL ARTICLE',
    'title'   => 'Titre de votre article de blog',
    'excerpt' => 'Découvrez les conseils essentiels pour développer votre activité.',
    'cta'     => 'Lire l\'article',
    'url'     => 'postelio.fr/blog',
])

<div style="
    width: 360px; height: 450px;
    background: #080F1C;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- === TOP EDITORIAL HEADER BAND === --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 100px;
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 60%, #92400E 100%);
        overflow: hidden;
    ">
        {{-- Noise texture overlay --}}
        <div style="
            position: absolute; inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 3px,
                rgba(0,0,0,0.06) 3px,
                rgba(0,0,0,0.06) 4px
            );
        "></div>

        {{-- "BLOG" giant watermark inside header --}}
        <div style="
            position: absolute; right: -8px; top: -18px;
            font-size: 110px; font-weight: 900; line-height: 1;
            color: rgba(0,0,0,0.12);
            letter-spacing: -0.04em;
            user-select: none;
        ">BLOG</div>

        {{-- Header content --}}
        <div style="
            position: relative; z-index: 1;
            padding: 20px 28px 0 28px;
        ">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                {{-- Mini book icon made of CSS --}}
                <div style="
                    width: 14px; height: 14px;
                    border: 2px solid rgba(0,0,0,0.4);
                    border-radius: 1px;
                    position: relative;
                    display: flex; align-items: center; justify-content: center;
                ">
                    <div style="
                        position: absolute; left: 50%; top: 1px; bottom: 1px;
                        width: 1px; background: rgba(0,0,0,0.4);
                        transform: translateX(-50%);
                    "></div>
                </div>
                <span style="
                    color: rgba(0,0,0,0.55);
                    font-size: 9px; font-weight: 900;
                    letter-spacing: 0.22em; text-transform: uppercase;
                ">{{ $tag }}</span>
            </div>
            <div style="
                width: 32px; height: 2px;
                background: rgba(0,0,0,0.3);
                border-radius: 2px;
            "></div>
        </div>
    </div>

    {{-- === MAIN CONTENT AREA === --}}
    <div style="
        position: absolute;
        top: 100px; left: 0; right: 0; bottom: 52px;
        padding: 22px 28px 16px 28px;
        display: flex; flex-direction: column; justify-content: space-between;
    ">
        {{-- Article title --}}
        <div>
            <h1 style="
                color: #FFFFFF;
                font-size: 20px; font-weight: 900;
                line-height: 1.22; letter-spacing: -0.02em;
                margin: 0 0 14px 0;
            ">{{ $title }}</h1>

            {{-- Decorative paragraph lines (suggest written content) --}}
            <div style="margin-bottom: 14px; opacity: 0.18;">
                <div style="height: 2px; background: #F59E0B; border-radius: 2px; margin-bottom: 6px; width: 100%;"></div>
                <div style="height: 2px; background: rgba(255,255,255,0.5); border-radius: 2px; margin-bottom: 6px; width: 88%;"></div>
                <div style="height: 2px; background: rgba(255,255,255,0.5); border-radius: 2px; width: 72%;"></div>
            </div>

            {{-- Excerpt --}}
            <p style="
                color: #6B7280;
                font-size: 11.5px; font-weight: 400;
                line-height: 1.65; margin: 0;
                white-space: pre-line;
            ">{{ $excerpt }}</p>
        </div>

        {{-- CTA row --}}
        <div style="
            display: flex; align-items: center;
            justify-content: space-between;
            margin-top: 12px;
        ">
            <span style="
                display: inline-flex; align-items: center; gap: 6px;
                background: #F59E0B;
                color: #080F1C;
                font-size: 11px; font-weight: 900;
                letter-spacing: 0.04em;
                padding: 8px 18px; border-radius: 8px;
            ">{{ $cta }} &#x2192;</span>

            {{-- Read time indicator --}}
            <span style="
                color: rgba(255,255,255,0.25);
                font-size: 10px; font-weight: 600;
                letter-spacing: 0.06em;
            ">~ 5 min</span>
        </div>
    </div>

    {{-- === BOTTOM BRANDING BAR === --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 52px;
        background: rgba(245,158,11,0.07);
        border-top: 1px solid rgba(245,158,11,0.15);
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
