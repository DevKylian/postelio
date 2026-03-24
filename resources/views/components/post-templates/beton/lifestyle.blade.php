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

    @php
        $len = mb_strlen($text);
        $fontSize = 'text-4xl';
        if ($len > 80) $fontSize = 'text-xl';
        elseif ($len > 50) $fontSize = 'text-2xl';
        elseif ($len > 30) $fontSize = 'text-3xl';
    @endphp

    {{-- Content --}}
    <div class="mt-auto p-8 relative z-10 w-full">
        <div class="flex items-end justify-between">
            <div class="flex flex-col w-full">
                <div class="w-12 h-1 bg-white mb-4" style="background-color: {{ $brandColor }}"></div>
                <div class="max-h-[180px] overflow-hidden">
                    <span class="text-white {{ $fontSize }} font-black font-montserrat uppercase leading-tight tracking-tighter block">
                        {{ $text }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="absolute top-8 right-8">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-4 py-1.5 flex items-center gap-2">
            <span class="text-white text-[9px] font-black uppercase tracking-widest">{{ $companyLabel }}</span>
        </div>
    </div>
</div>
