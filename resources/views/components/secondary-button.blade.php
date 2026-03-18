@props(['href' => null])

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-xl font-semibold text-sm text-gray-700 uppercase tracking-wide hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-copper/20 focus:ring-offset-1 disabled:opacity-40 transition-all duration-200 shadow-sm active:scale-[0.98]']) }}>
    {{ $slot }}
</a>
@else
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-xl font-semibold text-sm text-gray-700 uppercase tracking-wide hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-copper/20 focus:ring-offset-1 disabled:opacity-40 transition-all duration-200 shadow-sm active:scale-[0.98]']) }}>
    {{ $slot }}
</button>
@endif
