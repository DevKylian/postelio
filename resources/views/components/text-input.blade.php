@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-200 focus:border-copper focus:ring-2 focus:ring-copper/20 rounded-xl shadow-sm transition-all duration-200 text-midnight placeholder:text-gray-300 text-sm bg-white disabled:bg-gray-50 disabled:text-gray-400']) }}>
