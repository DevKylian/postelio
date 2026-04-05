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
    {{-- Subtle warm glow --}}
    <div style="
        position: absolute; bottom: -60px; right: -60px;
        width: 260px; height: 260px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.10) 0%, transparent 65%);
        pointer-events: none;
    "></div>

    {{-- Main layout --}}
    <div style="
        position: absolute; inset: 0;
        padding: 36px 32px 32px 32px;
        display: flex; flex-direction: column;
    ">
        {{-- Publication bar --}}
        <div style="
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        ">
            <div style="display: flex; align-items: center; gap: 8px;">
                {{-- Pen icon --}}
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"
                        stroke="#F59E0B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span style="
                    color: #F59E0B; font-size: 9px; font-weight: 800;
                    letter-spacing: 0.2em; text-transform: uppercase;
                ">{{ $tag }}</span>
            </div>
            <span style="color: rgba(255,255,255,0.18); font-size: 9px;
                letter-spacing: 0.08em;">{{ $url }}</span>
        </div>

        {{-- Title block — hero --}}
        <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
            {{-- Reading time pill --}}
            <div style="
                display: inline-flex; align-items: center; gap: 5px;
                margin-bottom: 16px; width: fit-content;
            ">
                <div style="width: 18px; height: 1px; background: rgba(245,158,11,0.5);"></div>
                <span style="
                    color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 600;
                    letter-spacing: 0.12em; text-transform: uppercase;
                ">Lecture · 5 min</span>
            </div>

            <h1 style="
                color: #F8FAFC; font-size: 24px; font-weight: 900;
                line-height: 1.2; letter-spacing: -0.025em;
                margin: 0 0 18px 0;
            ">{{ $title }}</h1>

            <p style="
                color: #475569; font-size: 12px; font-weight: 400;
                line-height: 1.7; margin: 0;
            ">{{ $excerpt }}</p>
        </div>

        {{-- CTA row --}}
        <div style="
            display: flex; align-items: center;
            justify-content: space-between;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
        ">
            <span style="
                display: inline-flex; align-items: center; gap: 6px;
                background: #F59E0B; color: #0B1120;
                font-size: 11px; font-weight: 900; letter-spacing: 0.04em;
                padding: 9px 20px; border-radius: 8px;
            ">{{ $cta }} &#x2192;</span>

            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 4px; height: 4px; border-radius: 50%; background: #F59E0B; opacity:0.6;"></div>
                <span style="color: rgba(255,255,255,0.2); font-size: 9px;
                    font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Postelio</span>
            </div>
        </div>
    </div>
</div>
