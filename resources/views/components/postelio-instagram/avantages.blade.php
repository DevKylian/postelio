@props([
    'tag'      => 'POINT FORT',
    'headline' => 'Pourquoi choisir notre expertise ?',
    'items'    => [],
    'body'     => null, // Fallback if items are missing
])

@php
    $cleanHeadline = html_entity_decode($headline, ENT_QUOTES, 'UTF-8');
    $headlineLen = mb_strlen($cleanHeadline);
    $headlineSize = $headlineLen > 50 ? '19px' : ($headlineLen > 30 ? '21px' : '23px');

    $safeItems = array_filter((array)($items ?? []));
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

    {{-- Subtle background texture --}}
    <div style="
        position: absolute; right: -40px; bottom: -40px;
        width: 180px; height: 180px; border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.08) 0%, transparent 70%);
        pointer-events: none;
    "></div>

    {{-- Grid pattern --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(rgba(0,0,0,0.03) 1px, transparent 1px);
        background-size: 20px 20px;
        pointer-events: none;
    "></div>

    {{-- Content Container --}}
    <div style="
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        display: flex; flex-direction: column;
        padding: 36px 32px 42px 32px;
    ">
        {{-- Tag --}}
        <div style="margin-bottom: 16px;">
            <span style="
                background: rgba(245,158,11,0.1);
                border: 1px solid rgba(245,158,11,0.3);
                color: #D97706;
                font-size: 8px; font-weight: 800;
                letter-spacing: 0.2em; text-transform: uppercase;
                padding: 4px 12px; border-radius: 100px;
            ">{{ $tag }}</span>
        </div>

        {{-- Headline --}}
        <h1 style="
            color: #111827;
            font-size: {{ $headlineSize }}; font-weight: 900;
            line-height: 1.25; letter-spacing: -0.02em;
            margin: 0 0 24px 0;
        ">{!! $cleanHeadline !!}</h1>

        {{-- Main content (Items or Body) --}}
        <div style="flex-grow: 1;">
            @if(count($safeItems) > 0)
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach(array_slice($safeItems, 0, 4) as $item)
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div style="
                                width: 22px; height: 22px; border-radius: 6px;
                                background: linear-gradient(135deg, #F59E0B, #D97706);
                                display: flex; align-items: center; justify-content: center;
                                flex-shrink: 0; margin-top: 1px;
                                box-shadow: 0 2px 4px rgba(245,158,11,0.2);
                            ">
                                <svg style="width: 12px; height: 12px;" fill="none" stroke="#FFFFFF" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span style="
                                color: #374151;
                                font-size: 13.5px; font-weight: 600;
                                line-height: 1.5;
                            ">{!! html_entity_decode($item, ENT_QUOTES, 'UTF-8') !!}</span>
                        </div>
                    @endforeach
                </div>
            @elseif($body)
                <p style="
                    color: #4B5563;
                    font-size: 14.5px; font-weight: 500;
                    line-height: 1.6; margin: 0;
                    white-space: pre-line;
                ">{!! html_entity_decode($body, ENT_QUOTES, 'UTF-8') !!}</p>
            @endif
        </div>

        {{-- Branding Footer --}}
        <div style="
            display: flex; align-items: center; justify-content: space-between;
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 16px;
            margin-top: 20px;
        ">
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 5px; height: 5px; border-radius: 50%; background: #F59E0B;"></div>
                <span style="color: #6B7280; font-size: 10px; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase;">Postelio</span>
            </div>
            <span style="color: #9CA3AF; font-size: 10px; letter-spacing: 0.05em;">postelio.fr</span>
        </div>
    </div>
</div>
