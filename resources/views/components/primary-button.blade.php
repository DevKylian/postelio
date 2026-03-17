@props(['href' => null])

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-6 py-3 bg-copper border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-copper focus:ring-offset-2 transition-all duration-200 ease-in-out hover:scale-[1.02] hover:shadow-lg active:scale-95 disabled:opacity-50']) }}>
    {{ $slot }}
</a>
@else
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-copper border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-copper focus:ring-offset-2 transition-all duration-200 ease-in-out hover:scale-[1.02] hover:shadow-lg active:scale-95 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
@endif
