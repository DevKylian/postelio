@props([
    'tag'       => 'NOUVEL ARTICLE',
    'title'     => 'Titre de votre article de blog',
    'excerpt'   => null,
    'cta'       => 'Lire l\'article',
    'url'       => 'postelio.fr/blog',
    'image_url' => null,
])

@php
    $cleanTitle   = html_entity_decode($title,  ENT_QUOTES, 'UTF-8');
    $cleanTag     = html_entity_decode($tag,    ENT_QUOTES, 'UTF-8');
    $cleanCta     = html_entity_decode($cta,    ENT_QUOTES, 'UTF-8');
    $cleanExcerpt = $excerpt ? html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8') : null;

    $titleLen  = mb_strlen($cleanTitle);
    $titleSize = $titleLen > 70 ? '18px' : ($titleLen > 50 ? '22px' : ($titleLen > 32 ? '26px' : '30px'));

    // Resolve image to base64 for Browsershot reliability
    $finalImage = null;
    if ($image_url) {
        if (str_starts_with($image_url, 'data:image')) {
            $finalImage = $image_url;
        } elseif (str_contains($image_url, 'livewire-tmp')) {
            try {
                $pathParts = explode('/', parse_url($image_url, PHP_URL_PATH));
                $fileName  = end($pathParts);
                $localPath = storage_path('app/livewire-tmp/' . $fileName);
                if (file_exists($localPath)) {
                    $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
                    $mime = match($ext) { 'jpg','jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', default => 'image/jpeg' };
                    $finalImage = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($localPath));
                }
            } catch (\Exception $e) {}
        } else {
            $finalImage = $image_url;
        }
    }

    $bgImage = $finalImage ? "background-image:url('" . $finalImage . "');" : '';
@endphp

{{--
    Canvas: 360 × 640 px  (exported at ×3 = 1080 × 1920 px)

    Instagram story safe zones (at 1080×1920, scaled back to 360×640):
      • Top danger zone  :   0 – 83 px  (progress bars, profile, username)
      • Bottom danger zone: 560 – 640 px  (reply input, home indicator)
      • Safe zone        :  83 – 560 px  (477 px of usable space)

    Layout:
      0    – 4    copper bar
      4    – 84   top safe: tag badge + URL + breathing room
      84   – 290  landscape image (206 px tall, ~1.75:1 matches common blog thumbnails)
      290  – 308  copper separator
      308  – 500  text content: title + excerpt
      500  – 555  CTA row
      555  – 640  bottom safe buffer (85 px)
--}}
<div style="width:360px;height:640px;position:relative;overflow:hidden;font-family:'Outfit','Inter',-apple-system,sans-serif;background:#0f1117;">

    {{-- ── Subtle radial glow (background texture) ───────────────────────────── --}}
    <div style="position:absolute;top:0;left:0;width:360px;height:640px;background:radial-gradient(ellipse 260px 260px at 180px 420px,rgba(245,158,11,0.07) 0%,transparent 100%);"></div>

    {{-- ── Copper accent bar ───────────────────────────────────────────────────── --}}
    <div style="position:absolute;top:0;left:0;width:360px;height:4px;background:linear-gradient(90deg,#F59E0B,#D97706);"></div>

    {{-- ── TOP SAFE ZONE: tag + URL (y: 14 – 46) ──────────────────────────────── --}}
    <div style="position:absolute;top:14px;left:20px;display:flex;align-items:center;gap:8px;">
        <div style="width:5px;height:5px;border-radius:50%;background:#F59E0B;flex-shrink:0;"></div>
        <span style="color:#F59E0B;font-size:9px;font-weight:800;letter-spacing:0.22em;text-transform:uppercase;">{!! $cleanTag !!}</span>
    </div>
    <div style="position:absolute;top:17px;right:20px;">
        <span style="color:rgba(255,255,255,0.35);font-size:9px;font-weight:600;letter-spacing:0.08em;">{{ $url }}</span>
    </div>

    {{-- ── LANDSCAPE IMAGE (y: 84 – 290, h: 206px) ────────────────────────────── --}}
    <div style="position:absolute;top:84px;left:0;width:360px;height:206px;overflow:hidden;">
        @if($finalImage)
            {{-- CSS background ensures cover+center regardless of image aspect ratio --}}
            <div style="width:360px;height:206px;{{ $bgImage }}background-size:cover;background-position:center center;"></div>
        @else
            <div style="width:360px;height:206px;background:linear-gradient(135deg,#1e293b 0%,#2d3748 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;">
                <svg style="width:36px;height:36px;color:#4a5568;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span style="color:#4a5568;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;">Miniature de l'article</span>
            </div>
        @endif

        {{-- soft vignette edges --}}
        <div style="position:absolute;top:0;left:0;width:360px;height:206px;box-shadow:inset 0 0 30px rgba(15,17,23,0.5);"></div>
    </div>

    {{-- ── SEPARATOR (y: 290 – 308) ────────────────────────────────────────────── --}}
    <div style="position:absolute;top:290px;left:20px;width:40px;height:2px;background:linear-gradient(90deg,#F59E0B,#D97706);"></div>

    {{-- ── TEXT CONTENT (y: 308 – ~500) ───────────────────────────────────────── --}}
    <div style="position:absolute;top:308px;left:20px;width:320px;">

        {{-- Title --}}
        <div style="margin-bottom:16px;">
            <h1 style="color:#FFFFFF;font-size:{{ $titleSize }};font-weight:900;line-height:1.2;letter-spacing:-0.02em;margin:0;">{!! $cleanTitle !!}</h1>
        </div>

        {{-- Excerpt --}}
        @if($cleanExcerpt)
        <div>
            <p style="color:rgba(255,255,255,0.58);font-size:13px;font-weight:500;line-height:1.6;margin:0;">{!! $cleanExcerpt !!}</p>
        </div>
        @endif

    </div>

    {{-- ── CTA ROW (y: 500 – 555) ──────────────────────────────────────────────── --}}
    <div style="position:absolute;top:500px;left:20px;width:320px;display:flex;align-items:center;justify-content:space-between;">

        <div style="background:linear-gradient(135deg,#F59E0B,#D97706);color:#111827;font-size:12px;font-weight:900;padding:12px 24px;border-radius:10px;letter-spacing:-0.01em;white-space:nowrap;">
            {!! $cleanCta !!} →
        </div>

        <div style="display:flex;align-items:center;gap:6px;">
            <div style="width:5px;height:5px;border-radius:50%;background:#F59E0B;flex-shrink:0;"></div>
            <span style="color:rgba(255,255,255,0.45);font-size:10px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;">Postelio</span>
        </div>

    </div>

    {{-- ── BOTTOM SAFE ZONE BUFFER (y: 555 – 640, 85 px) ─────────────────────── --}}
    {{-- No content here — Instagram reply bar overlays this area --}}

</div>
