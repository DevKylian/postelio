@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-white bg-white/10 shadow-sm shadow-black/20 group'
            : 'flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-gray-400 hover:text-white hover:bg-white/5 group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
