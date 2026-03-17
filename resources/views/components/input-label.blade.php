@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-midnight']) }}>
    {{ $value ?? $slot }}
</label>
