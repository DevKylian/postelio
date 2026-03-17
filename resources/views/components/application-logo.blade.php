@props(['variant' => 'light'])

{{--
    Postelio Icon Logo
    variant="light"  → dark hexagon (#1E293B) + white circle + orange wave  (for light backgrounds)
    variant="dark"   → white hexagon outline + orange wave                   (for dark backgrounds like the sidebar)
--}}

<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" {{ $attributes->except('variant') }}>
    @if ($variant === 'dark')
        {{-- White hexagon for dark backgrounds --}}
        <polygon points="27,3 73,3 97,50 73,97 27,97 3,50" fill="white" fill-opacity="0.15"/>
        <polygon points="27,3 73,3 97,50 73,97 27,97 3,50" fill="none" stroke="white" stroke-width="3.5" stroke-linejoin="round"/>
        <circle cx="50" cy="50" r="29" fill="white" fill-opacity="0.07"/>
    @else
        {{-- Dark hexagon for light backgrounds --}}
        <polygon points="27,3 73,3 97,50 73,97 27,97 3,50" fill="#1E293B"/>
        <circle cx="50" cy="50" r="29" fill="white"/>
    @endif

    {{-- ECG / heartbeat wave — always copper orange --}}
    <polyline
        points="3,50 20,50 26,38 32,60 38,23 44,74 50,38 55,50 97,50"
        fill="none"
        stroke="#F59E0B"
        stroke-width="3.5"
        stroke-linecap="round"
        stroke-linejoin="round"
    />
</svg>
