@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0 mt-2',
    'top' => 'origin-bottom bottom-full mb-2',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0 mt-2',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
            class="absolute z-50 {{ $width }} rounded-xl shadow-xl {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-xl ring-1 ring-black/[0.06] shadow-2xl {{ $contentClasses }} overflow-hidden">
            {{ $content }}
        </div>
    </div>
</div>
