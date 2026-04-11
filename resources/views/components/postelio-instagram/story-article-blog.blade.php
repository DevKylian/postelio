@props([
    'tag'     => 'ARTICLE DE BLOG',
    'title'   => 'Titre de votre article',
    'excerpt' => null,
    'cta'     => 'Lire l\'article',
    'url'     => 'postelio.fr/blog',
])

@php
    $cleanTitle   = html_entity_decode($title,  ENT_QUOTES, 'UTF-8');
    $cleanTag     = html_entity_decode($tag,    ENT_QUOTES, 'UTF-8');
    $cleanCta     = html_entity_decode($cta,    ENT_QUOTES, 'UTF-8');
    $cleanExcerpt = $excerpt ? html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8') : null;

    // Adaptive title size — safe zone width ≈ 304 px (360 - 2×28)
    $titleLen  = mb_strlen($cleanTitle);
    $titleSize = $titleLen > 80 ? '22px' : ($titleLen > 60 ? '26px' : ($titleLen > 40 ? '30px' : '34px'));
    $titleLH   = '1.18';
@endphp

{{--
    Canvas: 360 × 640 px  ·  exported at ×3 → 1080 × 1920 px

    Instagram story safe zones (1080×1920 → scaled to 360×640):
      Top danger    0 –  83 px  (progress bars + profile header)
      SAFE ZONE    84 – 555 px  (471 px usable)
      Bottom danger 555 – 640 px  (swipe-up / reply bar)
--}}
<div style="width:360px;height:640px;position:relative;overflow:hidden;font-family:'Outfit','Inter',-apple-system,sans-serif;background:#0D1017;">

    {{-- ░░ Background texture: subtle dot grid ░░ --}}
    <div style="position:absolute;top:0;left:0;width:360px;height:640px;background-image:radial-gradient(rgba(255,255,255,0.035) 1px,transparent 1px);background-size:18px 18px;"></div>

    {{-- ░░ Warm radial glow — bottom-center ░░ --}}
    <div style="position:absolute;bottom:-60px;left:50%;margin-left:-160px;width:320px;height:320px;background:radial-gradient(circle,rgba(245,158,11,0.13) 0%,transparent 70%);"></div>

    {{-- ░░ Top copper bar ░░ --}}
    <div style="position:absolute;top:0;left:0;width:360px;height:4px;background:linear-gradient(90deg,#F59E0B,#D97706);"></div>

    {{-- ══════════════════════════════════════════════════════════════
         TOP SAFE ZONE  (y: 4 – 84)
         Only light branding — Instagram profile bar will overlap this
    ══════════════════════════════════════════════════════════════ --}}
    <div style="position:absolute;top:16px;left:22px;display:flex;align-items:center;gap:7px;">
        <div style="width:4px;height:4px;border-radius:50%;background:#F59E0B;"></div>
        <span style="color:#F59E0B;font-size:8px;font-weight:800;letter-spacing:0.22em;text-transform:uppercase;">{!! $cleanTag !!}</span>
    </div>
    <div style="position:absolute;top:18px;right:22px;">
        <span style="color:rgba(255,255,255,0.28);font-size:8px;font-weight:600;letter-spacing:0.08em;">{{ $url }}</span>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SAFE ZONE  (y: 84 – 555)
    ══════════════════════════════════════════════════════════════ --}}

    {{-- ── Icon block  (y: 96 – 184) ──────────────────────────────────────────── --}}
    {{-- Outer ring --}}
    <div style="position:absolute;top:96px;left:50%;margin-left:-44px;width:88px;height:88px;border-radius:50%;border:1px solid rgba(245,158,11,0.25);"></div>
    {{-- Inner filled circle --}}
    <div style="position:absolute;top:104px;left:50%;margin-left:-36px;width:72px;height:72px;border-radius:50%;background:rgba(245,158,11,0.1);"></div>
    {{-- Book SVG icon --}}
    <div style="position:absolute;top:122px;left:50%;margin-left:-18px;width:36px;height:36px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:36px;height:36px;">
            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
    </div>

    {{-- ── Decorative separator (y: 200) ──────────────────────────────────────── --}}
    <div style="position:absolute;top:200px;left:28px;width:304px;height:1px;background:linear-gradient(90deg,transparent,rgba(245,158,11,0.4) 20%,rgba(245,158,11,0.4) 80%,transparent);"></div>

    {{-- ── NOUVEAU badge (y: 214) ──────────────────────────────────────────────── --}}
    <div style="position:absolute;top:214px;left:50%;transform:translateX(-50%);">
        <span style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.35);color:#F59E0B;font-size:8px;font-weight:800;letter-spacing:0.25em;text-transform:uppercase;padding:4px 14px;border-radius:100px;white-space:nowrap;">NOUVEL ARTICLE</span>
    </div>

    {{-- ── CONTENT BLOCK: titre + extrait + CTA, en flux naturel (y: 252) ──────── --}}
    <div style="position:absolute;top:252px;left:28px;width:304px;">

        {{-- Title --}}
        <h1 style="color:#FFFFFF;font-size:{{ $titleSize }};font-weight:900;line-height:{{ $titleLH }};letter-spacing:-0.025em;margin:0 0 18px 0;">{!! $cleanTitle !!}</h1>

        {{-- Excerpt --}}
        @if($cleanExcerpt)
        <p style="color:rgba(255,255,255,0.55);font-size:13px;font-weight:500;line-height:1.6;margin:0 0 24px 0;">{!! $cleanExcerpt !!}</p>
        @else
        <div style="height:24px;"></div>
        @endif

        {{-- CTA row --}}
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div style="background:linear-gradient(135deg,#F59E0B,#D97706);color:#111827;font-size:12px;font-weight:900;padding:12px 24px;border-radius:10px;letter-spacing:-0.01em;white-space:nowrap;">
                {!! $cleanCta !!} →
            </div>
            <div style="display:flex;align-items:center;gap:5px;">
                <div style="width:5px;height:5px;border-radius:50%;background:#F59E0B;flex-shrink:0;"></div>
                <span style="color:rgba(255,255,255,0.4);font-size:10px;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;">Postelio</span>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         BOTTOM SAFE ZONE  (y: 555 – 640) — intentionally empty
         Instagram reply bar / home indicator lives here
    ══════════════════════════════════════════════════════════════ --}}

</div>
