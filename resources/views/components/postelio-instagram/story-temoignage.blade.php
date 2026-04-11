@props([
    'quote'      => 'Depuis que j\'utilise Postelio, je publie deux fois par semaine sans effort. Mes clients me disent qu\'ils m\'ont trouvé sur Instagram.',
    'author'     => 'Marc D.',
    'profession' => 'Plombier chauffagiste — Lyon',
    'stars'      => 5,
])

@php
    $len = mb_strlen($quote);
    if ($len < 65) {
        $fontSize = '30px'; $lineHeight = '1.25'; $fontWeight = '800';
    } elseif ($len < 120) {
        $fontSize = '23px'; $lineHeight = '1.45'; $fontWeight = '600';
    } else {
        $fontSize = '18px'; $lineHeight = '1.65'; $fontWeight = '400';
    }
    $initial = strtoupper(mb_substr($author, 0, 1));
@endphp

{{--
    Canvas: 360 × 640 px  ·  exported at ×3 → 1080 × 1920 px

    Safe zones:
      Top danger    0 –  84 px  (progress bars + profile)
      SAFE ZONE    84 – 555 px
      Bottom danger 555 – 640 px  (reply bar)
--}}
<div style="width:360px;height:640px;position:relative;overflow:hidden;font-family:'Outfit','Inter',-apple-system,sans-serif;background:#F9FAFB;">

    {{-- ░░ Dot grid ░░ --}}
    <div style="position:absolute;top:0;left:0;width:360px;height:640px;background-image:radial-gradient(rgba(0,0,0,0.03) 1px,transparent 1px);background-size:20px 20px;"></div>

    {{-- ░░ Amber top glow ░░ --}}
    <div style="position:absolute;top:-80px;left:50%;margin-left:-160px;width:320px;height:280px;background:radial-gradient(ellipse,rgba(245,158,11,0.10) 0%,transparent 70%);"></div>

    {{-- ░░ Left amber bar — full height ░░ --}}
    <div style="position:absolute;top:0;left:0;width:5px;height:640px;background:linear-gradient(180deg,#FACC15,#F59E0B,#D97706);"></div>

    {{-- ══════════════════════════════════════════════
         TOP AREA (y: 14) — light branding only
    ══════════════════════════════════════════════ --}}
    <div style="position:absolute;top:14px;left:28px;width:304px;display:flex;align-items:center;justify-content:space-between;">
        <span style="background:rgba(245,158,11,0.10);border:1px solid rgba(245,158,11,0.20);color:#D97706;font-size:8px;font-weight:800;letter-spacing:0.15em;text-transform:uppercase;padding:4px 12px;border-radius:100px;">Avis client</span>
        <span style="color:#9CA3AF;font-size:8px;font-weight:600;letter-spacing:0.06em;">postelio.fr</span>
    </div>

    {{-- ══════════════════════════════════════════════
         SAFE ZONE (y: 84 – 555)
    ══════════════════════════════════════════════ --}}

    {{-- Stars (y: 122) --}}
    <div style="position:absolute;top:122px;left:28px;display:flex;gap:5px;">
        @for($i = 0; $i < 5; $i++)
        <svg style="width:22px;height:22px;color:{{ $i < $stars ? '#F59E0B' : 'rgba(0,0,0,0.07)' }};" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        @endfor
    </div>

    {{-- ── Content block: quote (top) + author / branding (bottom) ─────────── --}}
    {{-- Flex-column with spacer pushes author to the bottom of the safe zone --}}
    <div style="position:absolute;top:178px;left:28px;width:304px;">

        {{-- Quote --}}
        <p style="color:#1F2937;font-size:{{ $fontSize }};font-weight:{{ $fontWeight }};line-height:{{ $lineHeight }};font-style:italic;margin:0 0 28px 0;">"{{ $quote }}"</p>

        {{-- Author block --}}
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
            {{-- Avatar circle --}}
            <div style="width:48px;height:48px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#F59E0B,#D97706);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#FFFFFF;box-shadow:0 4px 12px rgba(245,158,11,0.28);">{{ $initial }}</div>
            <div style="flex:1;">
                <p style="color:#111827;font-size:14px;font-weight:800;margin:0 0 3px 0;">{{ $author }}</p>
                <p style="color:#6B7280;font-size:12px;font-weight:500;margin:0;">{{ $profession }}</p>
            </div>
        </div>

        {{-- Separator + branding --}}
        <div style="border-top:1px solid rgba(0,0,0,0.07);padding-top:14px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:6px;">
                <div style="width:5px;height:5px;border-radius:50%;background:#F59E0B;flex-shrink:0;"></div>
                <span style="color:#6B7280;font-size:10px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;">Postelio</span>
            </div>
            <span style="color:#9CA3AF;font-size:10px;letter-spacing:0.05em;">postelio.fr</span>
        </div>

    </div>

    {{-- Bottom safe zone (y: 555–640) — intentionally empty --}}

</div>
