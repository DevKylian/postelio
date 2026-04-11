@props([
    'tag'       => 'NOUVEL ARTICLE',
    'title'     => 'Titre de votre article de blog',
    'excerpt'   => null,
    'cta'       => 'Lire l\'article',
    'url'       => 'postelio.fr/blog',
    'image_url' => null,
])

@php
    $cleanTitle  = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    $cleanTag    = html_entity_decode($tag, ENT_QUOTES, 'UTF-8');
    $cleanCta    = html_entity_decode($cta, ENT_QUOTES, 'UTF-8');
    $cleanExcerpt = $excerpt ? html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8') : null;

    $titleLen  = mb_strlen($cleanTitle);
    $titleSize = $titleLen > 70 ? '20px' : ($titleLen > 50 ? '24px' : ($titleLen > 35 ? '28px' : '32px'));

    // Base64 logic for export reliability
    $base64Image = null;
    if ($image_url && str_contains($image_url, 'livewire-tmp')) {
        try {
            $pathParts = explode('/', parse_url($image_url, PHP_URL_PATH));
            $fileName  = end($pathParts);
            $localPath = storage_path('app/livewire-tmp/' . $fileName);
            if (file_exists($localPath)) {
                $type        = pathinfo($localPath, PATHINFO_EXTENSION);
                $data        = file_get_contents($localPath);
                $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        } catch (\Exception $e) {}
    }
    if ($image_url && str_starts_with($image_url, 'data:image')) {
        $base64Image = $image_url;
    }
    $finalImage = $base64Image ?? $image_url;
@endphp

<div style="
    width: 360px; height: 640px;
    background: #F9FAFB;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- ── Top image section (310px) ─────────────────────────────────────── --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 310px;
        background: #1F2937;
        overflow: hidden;
    ">
        @if($finalImage)
            <img src="{{ $finalImage }}" style="
                width: 100%; height: 100%;
                object-fit: cover;
                display: block;
            ">
        @else
            {{-- Placeholder --}}
            <div style="
                width: 100%; height: 100%;
                background: linear-gradient(135deg, #1F2937 0%, #374151 100%);
                display: flex; flex-direction: column;
                align-items: center; justify-content: center; gap: 12px;
            ">
                <svg style="width: 48px; height: 48px; color: #4B5563;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span style="color: #6B7280; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Ajoutez votre photo</span>
            </div>
        @endif

        {{-- Gradient overlay bottom of image --}}
        <div style="
            position: absolute; bottom: 0; left: 0; right: 0; height: 100px;
            background: linear-gradient(to bottom, transparent, rgba(249,250,251,0.95));
        "></div>

        {{-- Top copper accent bar --}}
        <div style="
            position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #F59E0B, #D97706);
        "></div>

        {{-- Blog label badge (top-left) --}}
        <div style="position: absolute; top: 20px; left: 20px;">
            <span style="
                background: rgba(245,158,11,0.9);
                color: #111827;
                font-size: 9px; font-weight: 800;
                letter-spacing: 0.18em; text-transform: uppercase;
                padding: 5px 14px; border-radius: 100px;
                backdrop-filter: blur(4px);
            ">{!! $cleanTag !!}</span>
        </div>

        {{-- URL label (top-right) --}}
        <div style="position: absolute; top: 24px; right: 20px;">
            <span style="
                color: rgba(255,255,255,0.75);
                font-size: 9px; font-weight: 600;
                letter-spacing: 0.05em;
                text-shadow: 0 1px 3px rgba(0,0,0,0.4);
            ">{{ $url }}</span>
        </div>
    </div>

    {{-- ── Bottom content section (330px) ─────────────────────────────────── --}}
    <div style="
        position: absolute; top: 310px; left: 0; right: 0; bottom: 0;
        display: flex; flex-direction: column;
        padding: 28px 28px 28px 28px;
        background: #F9FAFB;
    ">
        {{-- Grid pattern --}}
        <div style="
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(rgba(0,0,0,0.025) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
        "></div>

        {{-- Warm glow accent --}}
        <div style="
            position: absolute; right: -30px; bottom: -30px;
            width: 160px; height: 160px; border-radius: 50%;
            background: radial-gradient(circle, rgba(245,158,11,0.08) 0%, transparent 70%);
            pointer-events: none;
        "></div>

        {{-- Title --}}
        <div style="position: relative; flex: 1; display: flex; flex-direction: column; justify-content: flex-start;">
            <h1 style="
                color: #111827;
                font-size: {{ $titleSize }}; font-weight: 900;
                line-height: 1.2; letter-spacing: -0.03em;
                margin: 0 0 16px 0;
            ">{!! $cleanTitle !!}</h1>

            @if($cleanExcerpt)
            <p style="
                color: #4B5563;
                font-size: 13px; font-weight: 500;
                line-height: 1.55;
                margin: 0 0 20px 0;
                flex: 1;
            ">{!! $cleanExcerpt !!}</p>
            @endif
        </div>

        {{-- CTA + branding row --}}
        <div style="
            position: relative;
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 12px;
        ">
            <div style="
                background: linear-gradient(135deg, #F59E0B, #D97706);
                color: #111827;
                font-size: 12px; font-weight: 900;
                padding: 12px 22px;
                border-radius: 10px;
                box-shadow: 0 4px 12px -2px rgba(217,119,6,0.35);
                letter-spacing: -0.01em;
            ">
                {!! $cleanCta !!} →
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
                <span style="color: #6B7280; font-size: 10px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase;">Postelio</span>
            </div>
        </div>
    </div>
</div>
