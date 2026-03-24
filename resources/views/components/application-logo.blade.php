@props(['variant' => 'light'])

{{--
    Postelio Icon Logo — Voice Wave
    Now uses the same voice wave icon as <x-logo>.
    variant="light" → amber gradient circle + white wave (for light backgrounds)
    variant="dark"  → amber gradient circle + white wave (for dark backgrounds)
--}}

<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" {{ $attributes->except('variant') }}>
    {{-- Gradient circle background --}}
    <defs>
        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#FBBF24"/>
            <stop offset="100%" stop-color="#D97706"/>
        </linearGradient>
    </defs>
    <circle cx="50" cy="50" r="46" fill="url(#logoGradient)"/>

    {{-- Voice wave bars --}}
    <line x1="22" y1="38" x2="22" y2="62" stroke="white" stroke-width="5" stroke-linecap="round"/>
    <line x1="36" y1="28" x2="36" y2="72" stroke="white" stroke-width="5" stroke-linecap="round"/>
    <line x1="50" y1="18" x2="50" y2="82" stroke="white" stroke-width="5" stroke-linecap="round"/>
    <line x1="64" y1="30" x2="64" y2="70" stroke="white" stroke-width="5" stroke-linecap="round"/>
    <line x1="78" y1="40" x2="78" y2="60" stroke="white" stroke-width="5" stroke-linecap="round"/>
</svg>
