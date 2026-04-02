@props([
    'question' => 'Combien de clients perdez-vous chaque mois faute de visibilité sur Instagram ?',
    'teaser'   => 'La réponse va vous surprendre. Les artisans visibles sur les réseaux reçoivent 3× plus de demandes.',
    'tag'      => 'BONNE QUESTION',
])

<div style="
    width: 360px; height: 450px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Background pattern: subtle circles --}}
    <div style="
        position: absolute; top: -40px; right: -40px;
        width: 220px; height: 220px; border-radius: 50%;
        border: 1px solid rgba(245,158,11,0.08);
        pointer-events: none;
    "></div>
    <div style="
        position: absolute; top: -10px; right: -10px;
        width: 150px; height: 150px; border-radius: 50%;
        border: 1px solid rgba(245,158,11,0.05);
        pointer-events: none;
    "></div>
    <div style="
        position: absolute; bottom: -60px; left: -60px;
        width: 200px; height: 200px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Giant ? mark --}}
    <div style="
        position: absolute; right: 24px; top: 50%; transform: translateY(-60%);
        font-size: 160px; font-weight: 900; line-height: 1;
        color: rgba(245,158,11,0.07);
        font-family: Georgia, serif;
        user-select: none; pointer-events: none;
        letter-spacing: -0.05em;
    ">?</div>

    {{-- Content --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        width: 100%; height: 100%;
        display: flex; flex-direction: column; justify-content: center;
        padding: 40px 36px 64px 36px;
    ">
        {{-- Tag --}}
        <div style="
            display: inline-flex; align-items: center; gap: 6px;
            margin-bottom: 24px;
        ">
            <div style="width: 20px; height: 2px; background: #F59E0B; border-radius: 2px;"></div>
            <span style="
                color: #F59E0B;
                font-size: 9px; font-weight: 800;
                letter-spacing: 0.2em; text-transform: uppercase;
            ">{{ $tag }}</span>
        </div>

        {{-- Question --}}
        <h1 style="
            color: #FFFFFF;
            font-size: 22px; font-weight: 900;
            line-height: 1.25; letter-spacing: -0.02em;
            margin: 0 0 24px 0;
        ">{{ $question }}</h1>

        {{-- Teaser --}}
        <p style="
            color: #6B7280;
            font-size: 12px; font-weight: 400;
            line-height: 1.65;
            margin: 0;
            white-space: pre-line;
        ">{{ $teaser }}</p>
    </div>

    {{-- Bottom branding --}}
    <div style="
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 54px;
        background: rgba(245,158,11,0.08);
        border-top: 1px solid rgba(245,158,11,0.15);
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 28px;
    ">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="color: #F59E0B; font-size: 10px; font-weight: 800; letter-spacing: 0.15em; text-transform: uppercase;">Postelio</span>
        </div>
        <span style="color: rgba(255,255,255,0.2); font-size: 10px; letter-spacing: 0.08em;">Répondez en commentaire 👇</span>
    </div>
</div>
