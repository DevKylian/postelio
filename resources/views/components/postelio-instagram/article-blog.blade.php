@props([
    'tag'     => 'NOUVEL ARTICLE',
    'title'   => 'Titre de votre article de blog',
    'excerpt' => 'Découvrez les conseils essentiels pour développer votre activité.',
    'cta'     => 'Lire l\'article',
    'url'     => 'postelio.fr/blog',
])

<div style="
    width: 360px; height: 450px;
    background: #0F1629;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Top accent line --}}
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #F59E0B, #FB923C);"></div>

    {{-- Subtle ambient glow --}}
    <div style="position: absolute; top: -80px; right: -80px; width: 280px; height: 280px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 65%);
        pointer-events: none;"></div>

    {{-- Left vertical accent bar --}}
    <div style="position: absolute; left: 32px; top: 50px; bottom: 70px; width: 2px;
        background: linear-gradient(to bottom, #F59E0B, rgba(245,158,11,0));
        border-radius: 2px;"></div>

    {{-- Main content --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        padding: 44px 32px 44px 52px;
        display: flex; flex-direction: column; justify-content: space-between;
    ">
        <div>
            {{-- Tag --}}
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <span style="
                    color: #F59E0B; font-size: 9px; font-weight: 800;
                    letter-spacing: 0.22em; text-transform: uppercase;
                ">{{ $tag }}</span>
            </div>

            {{-- Title --}}
            <h1 style="
                color: #FFFFFF; font-size: 21px; font-weight: 900;
                line-height: 1.22; letter-spacing: -0.02em;
                margin: 0 0 20px 0;
            ">{{ $title }}</h1>

            {{-- Separator --}}
            <div style="width: 28px; height: 2px; background: #F59E0B;
                border-radius: 2px; margin-bottom: 16px;"></div>

            {{-- Excerpt --}}
            <p style="
                color: #64748B; font-size: 12px; font-weight: 400;
                line-height: 1.7; margin: 0;
            ">{{ $excerpt }}</p>
        </div>

        {{-- Bottom: CTA + read time --}}
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="
                display: inline-flex; align-items: center; gap: 6px;
                background: #F59E0B; color: #0F1629;
                font-size: 11px; font-weight: 900; letter-spacing: 0.04em;
                padding: 9px 20px; border-radius: 8px;
            ">{{ $cta }} &#x2192;</span>

            <div style="text-align: right;">
                <div style="color: rgba(255,255,255,0.18); font-size: 9px;
                    font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
                    margin-bottom: 3px;">Postelio</div>
                <div style="color: rgba(255,255,255,0.13); font-size: 9px;
                    letter-spacing: 0.06em;">{{ $url }}</div>
            </div>
        </div>
    </div>
</div>
