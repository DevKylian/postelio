@props([
    'image_url' => null, {{-- Background image --}}
    'post_url'  => null, {{-- The generated post image --}}
    'caption'   => null,
])

<div style="
    width: 360px; height: 450px;
    background: #FFFFFF;
    position: relative; overflow: hidden;
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
">
    {{-- Background Image --}}
    @if($image_url)
        <img src="{{ $image_url }}" style="
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover; filter: brightness(0.8) blur(4px);
            transform: scale(1.1);
        ">
        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.2); pointer-events: none;"></div>
    @else
        <div style="
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
        "></div>
    @endif

    {{-- Instagram Mockup Card (Dezoomed Fixed Height) --}}
    <div style="
        position: absolute; top: 48%; left: 50%; transform: translate(-50%, -50%);
        width: 215px; height: 420px;
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        overflow: hidden;
        display: block;
        text-align: left;
    ">
        {{-- Card Header --}}
        <div style="height: 42px; padding: 0 12px; display: flex; align-items: center; gap: 8px; border-bottom: 0.5px solid #F3F4F6; width: 100%; box-sizing: border-box; text-align: left;">
            <div style="width: 20px; height: 20px; border-radius: 50%; background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-size: 9px; font-weight: 900;">P</div>
            <div style="flex: 1; text-align: left;">
                <div style="color: #111827; font-size: 9.5px; font-weight: 700; line-height: 1.2;">PostRenov</div>
                <div style="color: #9CA3AF; font-size: 7.5px; font-weight: 500; line-height: 1.1;">Votre post Insta en 10s...</div>
            </div>
        </div>

        {{-- Main Post Image (Fixed 1:1 Square Ratio) --}}
        <div style="width: 100%; height: 215px; background: #EEEEEE; overflow: hidden; display: block;">
            @if($post_url)
                <img src="{{ $post_url }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #E5E7EB;">
                    <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            @endif
        </div>

        {{-- Footer Section --}}
        <div style="padding: 10px 13px; width: 100%; box-sizing: border-box; text-align: left; display: block; background: #FFFFFF;">
            {{-- Interaction Icons & Stats --}}
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 6px; width: 100%; text-align: left;">
                @php $l = rand(110, 160); $c = rand(30, 50); @endphp
                <div style="display: flex; align-items: center; gap: 4px; text-align: left;">
                    <svg style="width: 16px; height: 16px; color: #EF4444;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <span style="color: #111827; font-size: 10px; font-weight: 800;">{{ $l }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 4px; text-align: left;">
                    <svg style="width: 16px; height: 16px; color: #111827;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                    <span style="color: #111827; font-size: 10px; font-weight: 800;">{{ $c }}</span>
                </div>
                <div style="margin-left: auto; text-align: left;">
                    <svg style="width: 16px; height: 16px; color: #111827;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"/></svg>
                </div>
            </div>

            {{-- Caption area --}}
            <div style="color: #111827; font-size: 10px; line-height: 1.4; white-space: pre-wrap; text-align: left; width: 100%; display: block;"><span style="font-weight: 800; margin-right: 4px;">PostRenov</span>{{ preg_replace('/^\s+/', '', $caption ?? '...') }}</div>
        </div>
    </div>
</div>
