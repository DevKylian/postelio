@props([
    'quote'      => 'Depuis que j\'utilise Postelio, je publie deux fois par semaine sans effort. Mes clients me disent qu\'ils m\'ont trouvé sur Instagram.',
    'author'     => 'Marc D.',
    'profession' => 'Plombier chauffagiste — Lyon',
    'stars'      => 5,
])

<div style="
    width: 360px; height: 360px;
    background: #111827;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Top ambient glow --}}
    <div style="
        position: absolute; top: -80px; left: 50%; transform: translateX(-50%);
        width: 280px; height: 200px; border-radius: 50%;
        background: radial-gradient(ellipse, rgba(245,158,11,0.18) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Decorative left bar --}}
    <div style="
        position: absolute; left: 0; top: 40px; bottom: 40px; width: 3px;
        background: linear-gradient(180deg, transparent, #F59E0B, transparent);
    "></div>

    {{-- Huge quote mark --}}
    <div style="
        position: absolute; top: 16px; left: 20px;
        font-size: 90px; font-weight: 900; line-height: 1;
        color: rgba(245,158,11,0.12);
        font-family: Georgia, serif;
        user-select: none; pointer-events: none;
    ">"</div>

    {{-- Stars --}}
    <div style="
        position: absolute; top: 32px; right: 28px;
        display: flex; gap: 3px;
    ">
        @for($i = 0; $i < 5; $i++)
        <svg style="width: 14px; height: 14px; color: {{ $i < $stars ? '#F59E0B' : 'rgba(255,255,255,0.1)' }};" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        @endfor
    </div>

    {{-- Label --}}
    <div style="
        position: absolute; top: 32px; left: 28px;
    ">
        <span style="
            background: rgba(245,158,11,0.1);
            border: 1px solid rgba(245,158,11,0.3);
            color: #F59E0B;
            font-size: 8px; font-weight: 800;
            letter-spacing: 0.2em; text-transform: uppercase;
            padding: 3px 10px; border-radius: 100px;
        ">Avis client</span>
    </div>

    {{-- Quote text --}}
    <div style="
        position: absolute; top: 80px; left: 28px; right: 28px;
    ">
        <p style="
            color: #E5E7EB;
            font-size: 15px; font-weight: 400;
            line-height: 1.7;
            font-style: italic;
            margin: 0;
        ">"{{ $quote }}"</p>
    </div>

    {{-- Author block --}}
    <div style="
        position: absolute; bottom: 50px; left: 28px; right: 28px;
        display: flex; align-items: center; gap: 14px;
    ">
        {{-- Avatar initial --}}
        <div style="
            width: 42px; height: 42px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, #F59E0B, #D97706);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 900; color: #111827;
        ">{{ strtoupper(substr($author, 0, 1)) }}</div>
        <div>
            <p style="
                color: #FFFFFF; font-size: 13px; font-weight: 800;
                margin: 0 0 2px 0;
            ">{{ $author }}</p>
            <p style="
                color: #9CA3AF; font-size: 11px; font-weight: 400;
                margin: 0;
            ">{{ $profession }}</p>
        </div>
    </div>

    {{-- Bottom branding --}}
    <div style="
        position: absolute; bottom: 16px; left: 28px; right: 28px;
        display: flex; align-items: center; justify-content: space-between;
        border-top: 1px solid rgba(255,255,255,0.05);
        padding-top: 10px;
    ">
        <div style="display: flex; align-items: center; gap: 6px;">
            <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
            <span style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase;">Postelio</span>
        </div>
        <span style="color: rgba(255,255,255,0.2); font-size: 9px; letter-spacing: 0.05em;">postelio.fr</span>
    </div>
</div>
