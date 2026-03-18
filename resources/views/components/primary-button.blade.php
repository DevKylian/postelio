@props(['href' => null])

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-5 py-2.5 bg-copper border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wide hover:bg-amber-500 focus:bg-amber-500 active:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:ring-offset-1 transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-copper/20 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</a>
@else
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-copper border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wide hover:bg-amber-500 focus:bg-amber-500 active:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:ring-offset-1 transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-copper/20 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
@endif
