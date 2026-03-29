@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'FOCUS QUALITÉ',
    'logoUrl' => null,
    'preview' => false,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'FOCUS QUALITÉ') ? 'Le choix des meilleurs matériaux pour une durabilité garantie.' : $text;
    @endphp
@endif

<div {!! $alpineData !!} class="w-full aspect-square bg-neutral-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    {{-- Background image with focus --}}
    <div class="absolute inset-0 bg-neutral-800">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover blur-[2px]">
        @else
            <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="absolute inset-0 w-full h-full object-cover opacity-60 grayscale blur-[2px]">
        @endif
    </div>

    {{-- Main Image Background --}}
    <div class="relative flex-1 flex items-center justify-center p-12">
        <div class="relative w-full h-full rounded-2xl border border-white/10 shadow-2xl overflow-hidden group/zoom">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-contain transition-transform duration-700 group-hover/zoom:scale-110">
            @else
                <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover/zoom:scale-110">
            @endif
            <div class="absolute inset-0 bg-gradient-to-tr from-midnight/40 to-transparent"></div>
        </div>
    </div>

    {{-- Info panel --}}
    <div class="h-24 px-8 backdrop-blur-2xl bg-white/5 border-t border-white/10 flex items-center justify-between">
        <div class="flex flex-col gap-1 max-w-[70%]">
            @php
                $cLen = mb_strlen($companyLabel);
                $cFontSize = 'text-[9px]';
                if ($cLen > 30) $cFontSize = 'text-[7px]';
                elseif ($cLen > 20) $cFontSize = 'text-[8px]';

                $tLen = mb_strlen($text);
                $tFontSize = 'text-base';
                if ($tLen > 40) $tFontSize = 'text-xs';
                elseif ($tLen > 25) $tFontSize = 'text-sm';
            @endphp
            <span class="{{ $cFontSize }} font-black text-white/40 uppercase tracking-[0.4em] font-montserrat truncate">{{ $companyLabel }}</span>
            <div class="max-h-[40px] overflow-hidden">
                <span class="text-white {{ $tFontSize }} font-bold tracking-tight leading-tight uppercase">{{ $text }}</span>
            </div>
        </div>

        <div class="flex items-center gap-4">
        </div>
    </div>
</div>
