@props(['showText' => true, 'size' => 'md', 'textClass' => ''])

@php
    $sizes = [
        'xs' => 'w-7 h-7',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
    ];
    $iconSizes = [
        'xs' => 'w-3.5 h-3.5',
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
        'xl' => 'w-8 h-8',
    ];
    $textSizes = [
        'xs' => 'text-[1.1rem]',
        'sm' => 'text-[1.25rem]',
        'md' => 'text-[1.4rem]',
        'lg' => 'text-[1.7rem]',
        'xl' => 'text-[2rem]',
    ];
    $circleClass = $sizes[$size] ?? $sizes['md'];
    $svgClass = $iconSizes[$size] ?? $iconSizes['md'];
    $defaultTextClass = $textSizes[$size] ?? $textSizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    {{-- Gradient circle with voice wave --}}
    <div class="{{ $circleClass }} bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center shadow-[0_2px_10px_rgba(245,158,11,0.2)] flex-shrink-0">
        <svg class="{{ $svgClass }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            {{-- Voice / sound wave icon --}}
            <line x1="4" y1="8" x2="4" y2="16" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
            <line x1="8" y1="5" x2="8" y2="19" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
            <line x1="12" y1="2" x2="12" y2="22" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
            <line x1="16" y1="6" x2="16" y2="18" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
            <line x1="20" y1="9" x2="20" y2="15" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
        </svg>
    </div>

    @if($showText)
        <span class="font-logo font-bold tracking-tight text-midnight {{ $textClass ?: $defaultTextClass }}">Postelio</span>
    @endif
</div>
