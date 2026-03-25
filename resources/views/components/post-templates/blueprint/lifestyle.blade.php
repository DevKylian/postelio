@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
])

<div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">

    {{-- Background Grid --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none"
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 30px 30px;">
    </div>

    {{-- Photo area with text overlay --}}
    <div class="flex-1 min-h-0 relative flex items-center justify-center p-8 pb-4">
        <div class="relative w-full h-full border-2 border-white/50 p-2 bg-white overflow-hidden">
             @if($imageUrl)
                 <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
             @else
                 <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" class="w-full h-full object-cover">
             @endif

             {{-- Dark gradient for text readability --}}
             <div class="absolute inset-x-0 bottom-0 h-[60%] bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

             {{-- Text: centered in lower third of photo --}}
             @php
                $len = mb_strlen($text);
                $fontSize = 'text-sm';
                if ($len > 25) $fontSize = 'text-[10px]';
                elseif ($len > 18) $fontSize = 'text-xs';
             @endphp
             <div class="absolute inset-x-3 bottom-3 top-[55%] flex items-center justify-center overflow-hidden">
                 <span class="text-white {{ $fontSize }} font-black font-mono-technical uppercase tracking-[0.15em] leading-snug text-center" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $text }}</span>
             </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="h-12 shrink-0 bg-white border-t-2 border-[#0047AB] px-8 flex items-center justify-between z-20">
        <span class="text-[#0047AB] text-[10px] font-black font-mono-technical uppercase tracking-[0.2em] truncate max-w-[80%]">{{ $companyLabel }}</span>
        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $brandColor }}"></div>
    </div>
</div>
