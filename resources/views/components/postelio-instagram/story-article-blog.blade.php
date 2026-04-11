@props([
    'tag'       => 'NOUVEL ARTICLE',
    'title'     => 'Titre de votre article de blog',
    'excerpt'   => null,
    'cta'       => 'Lire l\'article',
    'url'       => 'postelio.fr/blog',
    'image_url' => null,
])

@php
    $cleanTitle  = html_entity_decode($title,  ENT_QUOTES, 'UTF-8');
    $cleanTag    = html_entity_decode($tag,    ENT_QUOTES, 'UTF-8');
    $cleanCta    = html_entity_decode($cta,    ENT_QUOTES, 'UTF-8');
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
                    $mime      = 'image/' . strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
                    $finalImage = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($localPath));
                }
            } catch (\Exception $e) {}
        } else {
            $finalImage = $image_url;
        }
    }
@endphp

{{--
    Root: 360 × 640 px — all children use explicit px so Browsershot / Puppeteer
    never has to compute a relative width. No flex on the outer shell.
--}}
<div style="width:360px;height:640px;position:relative;overflow:hidden;font-family:'Outfit','Inter',-apple-system,sans-serif;background:#111827;">

    {{-- ── Full-bleed background image (CSS bg — most reliable in Puppeteer) ── --}}
    @if($finalImage)
    <div style="position:absolute;top:0;left:0;width:360px;height:640px;background-image:url('{{ $finalImage }}');background-size:cover;background-position:center center;"></div>
    @else
    {{-- Placeholder gradient when no image --}}
    <div style="position:absolute;top:0;left:0;width:360px;height:640px;background:linear-gradient(160deg,#1e293b 0%,#334155 50%,#1e293b 100%);"></div>
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
        <div style="width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,0.06);margin:0 auto 12px auto;display:flex;align-items:center;justify-content:center;">
            <svg style="width:28px;height:28px;color:#64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <span style="color:#64748b;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;">Ajoutez votre photo</span>
    </div>
    @endif

    {{-- ── Progressive dark overlay: transparent top → near-black bottom ──────── --}}
    <div style="position:absolute;top:0;left:0;width:360px;height:640px;background:linear-gradient(to bottom,rgba(0,0,0,0.04) 0%,rgba(0,0,0,0.18) 35%,rgba(0,0,0,0.68) 62%,rgba(0,0,0,0.88) 82%,rgba(0,0,0,0.96) 100%);"></div>

    {{-- ── Copper accent bar ───────────────────────────────────────────────────── --}}
    <div style="position:absolute;top:0;left:0;width:360px;height:4px;background:linear-gradient(90deg,#F59E0B,#D97706);"></div>

    {{-- ── Tag badge — top-left ───────────────────────────────────────────────── --}}
    <div style="position:absolute;top:22px;left:22px;background:rgba(245,158,11,0.92);border-radius:100px;padding:5px 14px;">
        <span style="color:#111827;font-size:9px;font-weight:800;letter-spacing:0.18em;text-transform:uppercase;white-space:nowrap;">{!! $cleanTag !!}</span>
    </div>

    {{-- ── URL — top-right ────────────────────────────────────────────────────── --}}
    <div style="position:absolute;top:26px;right:22px;">
        <span style="color:rgba(255,255,255,0.65);font-size:9px;font-weight:600;letter-spacing:0.05em;">{{ $url }}</span>
    </div>

    {{-- ── Bottom content block (pinned to bottom, explicit pixel heights) ──────── --}}
    <div style="position:absolute;bottom:0;left:0;width:360px;padding:30px 28px 36px 28px;box-sizing:border-box;">

        {{-- Title --}}
        <div style="margin-bottom:14px;">
            <h1 style="color:#FFFFFF;font-size:{{ $titleSize }};font-weight:900;line-height:1.2;letter-spacing:-0.025em;margin:0;text-shadow:0 2px 12px rgba(0,0,0,0.5);">{!! $cleanTitle !!}</h1>
        </div>

        {{-- Excerpt (optional) --}}
        @if($cleanExcerpt)
        <div style="margin-bottom:22px;">
            <p style="color:rgba(255,255,255,0.82);font-size:13px;font-weight:500;line-height:1.55;margin:0;">{!! $cleanExcerpt !!}</p>
        </div>
        @else
        <div style="margin-bottom:22px;"></div>
        @endif

        {{-- CTA row --}}
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div style="background:linear-gradient(135deg,#F59E0B,#D97706);color:#111827;font-size:12px;font-weight:900;padding:11px 22px;border-radius:10px;letter-spacing:-0.01em;white-space:nowrap;">
                {!! $cleanCta !!} →
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <div style="width:5px;height:5px;border-radius:50%;background:#F59E0B;flex-shrink:0;"></div>
                <span style="color:rgba(255,255,255,0.7);font-size:10px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;">Postelio</span>
            </div>
        </div>
    </div>

</div>
