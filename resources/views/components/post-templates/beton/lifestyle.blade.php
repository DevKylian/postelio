@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => '',
    'preview' => false,
])

<div class="w-full aspect-square bg-[#CBD5E1] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Full Image with Subtle Overlay --}}
    <div class="absolute inset-0">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
        @else
            <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-80"></div>
    </div>

    {{-- Company badge — top right --}}
    <div class="absolute top-8 right-8 z-10">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-4 py-1.5 flex items-center gap-2">
            <span class="text-white text-[9px] font-black uppercase tracking-widest">{{ $companyLabel }}</span>
        </div>
    </div>

    {{-- Text zone: fixed 100px band at bottom, inside the image --}}
    <div class="absolute inset-x-0 bottom-0 h-[100px] z-10 flex items-end px-8 pb-6">
        <div class="flex flex-col w-full">
            <div class="w-12 h-1 mb-3" style="background-color: {{ $brandColor }}"></div>
            @php
                $len = mb_strlen($text);
                $fontSize = 'text-2xl';
                if ($len > 35) $fontSize = 'text-lg';
                elseif ($len > 25) $fontSize = 'text-xl';
            @endphp
            <span class="text-white {{ $fontSize }} font-black font-montserrat uppercase leading-tight tracking-tighter block" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $text }}
            </span>
        </div>
    </div>
</div>
