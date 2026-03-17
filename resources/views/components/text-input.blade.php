@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-200 focus:border-copper focus:ring-copper rounded-xl shadow-sm transition-all duration-200 text-midnight placeholder:text-gray-400']) }}>
