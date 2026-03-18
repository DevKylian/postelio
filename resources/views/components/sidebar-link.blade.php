@props(['active'])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 text-white bg-white/10 group relative'
    : 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 text-slate-400 hover:text-slate-100 hover:bg-white/[0.06] group relative';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($active ?? false)
        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 bg-copper rounded-r-full"></span>
    @endif
    {{ $slot }}
</a>
