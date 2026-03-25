@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => '',
    'preview' => false,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === '') ? 'La passion du métier se lit dans chaque geste.' : $text;
    @endphp
@endif

<div class="w-full aspect-square bg-[#CBD5E1] relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Full Image --}}
    <div class="absolute inset-0">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
        @else
            <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
    </div>

    {{-- Company badge — top right --}}
    <div class="absolute top-6 right-6 z-10">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-3 py-1 flex items-center gap-2">
            <span class="text-white text-[9px] font-black uppercase tracking-widest">{{ $companyLabel }}</span>
        </div>
    </div>

    {{-- Text — centered in lower third of image --}}
    @php
        $len = mb_strlen($text);
        $fontSize = 'text-2xl';
        if ($len > 25) $fontSize = 'text-lg';
        elseif ($len > 18) $fontSize = 'text-xl';
    @endphp
    <div class="absolute inset-x-8 bottom-20 top-[55%] flex items-end z-10 overflow-hidden">
        <div class="flex flex-col w-full">
            <div class="w-10 h-1 mb-3" style="background-color: {{ $brandColor }}"></div>
            <span class="text-white {{ $fontSize }} font-black font-montserrat uppercase leading-tight tracking-tighter block" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $text }}
            </span>
        </div>
    </div>
</div>
