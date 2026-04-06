@props([
    'tag'       => 'ARTICLE DE BLOG',
    'title'     => 'Titre de votre article de blog',
    'excerpt'   => null,
    'cta'       => 'Lire l\'article',
    'url'       => 'postelio.fr/blog',
    'image_url' => null,
])

@php
    $cleanTitle = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    $titleLen = mb_strlen($cleanTitle);
    $titleSize = $titleLen > 60 ? '18px' : ($titleLen > 40 ? '20px' : '22px');

    // Base64 logic for export reliability
    $base64Image = null;
    if ($image_url && str_contains($image_url, 'livewire-tmp')) {
        try {
            $pathParts = explode('/', parse_url($image_url, PHP_URL_PATH));
            $fileName = end($pathParts);
            $localPath = storage_path('app/livewire-tmp/' . $fileName);
            if (file_exists($localPath)) {
                $type = pathinfo($localPath, PATHINFO_EXTENSION);
                $data = file_get_contents($localPath);
                $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        } catch (\Exception $e) {}
    }
@endphp

<div style="
    width: 360px; height: 450px;
    background: #F9FAFB;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Decorative element: Top accent --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #F59E0B, #D97706);
    "></div>

    {{-- Grid pattern --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(rgba(0,0,0,0.03) 1px, transparent 1px);
        background-size: 20px 20px;
        pointer-events: none;
    "></div>

    {{-- Subtle warm glow --}}
    <div style="
        position: absolute; right: -40px; top: -40px;
        width: 180px; height: 180px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.06) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Content Container --}}
    <div style="
        position: absolute; inset: 0;
        display: flex; flex-direction: column;
        padding: 52px 32px 32px 32px;
    ">
        {{-- Header: Tag & URL --}}
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <span style="
                background: rgba(245,158,11,0.1);
                border: 1px solid rgba(245,158,11,0.3);
                color: #D97706;
                font-size: 8px; font-weight: 800;
                letter-spacing: 0.2em; text-transform: uppercase;
                padding: 4px 12px; border-radius: 100px;
            ">{{ $tag }}</span>
            
            <span style="color: #9CA3AF; font-size: 9px; font-weight: 600; letter-spacing: 0.05em;">{{ $url }}</span>
        </div>

        {{-- Title Area --}}
        <div style="margin-bottom: 16px;">
            <h1 style="
                color: #111827;
                font-size: {{ $titleSize }}; font-weight: 900;
                line-height: 1.25; letter-spacing: -0.025em;
                margin: 0;
            ">{!! $cleanTitle !!}</h1>
        </div>

        {{-- Visual Area (Centered Image) --}}
        <div style="
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        ">
            @if($base64Image || $image_url)
                <div style="
                    width: 100%;
                    height: 168px;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 15px -3px rgba(0,0,0,0.08);
                ">
                    <img src="{{ $base64Image ?? $image_url }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            @else
                <div style="
                    width: 100%;
                    height: 168px;
                    border-radius: 16px;
                    background: #F3F4F6;
                    border: 2px dashed #E5E7EB;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                ">
                    <svg style="width: 32px; height: 32px; color: #D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span style="color: #9CA3AF; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">Ajoutez votre photo</span>
                </div>
            @endif
        </div>

        {{-- CTA section --}}
        <div style="margin-top: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="
                    background: #D97706;
                    color: #FFFFFF;
                    font-size: 11px; font-weight: 800;
                    padding: 10px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.2);
                ">
                    {{ $cta }} →
                </div>

                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
                    <span style="color: #6B7280; font-size: 10px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase;">Postelio</span>
                </div>
            </div>
        </div>
    </div>
</div>
