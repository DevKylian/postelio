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

    <div class="flex-1 relative flex items-center justify-center p-10">
        {{-- Image with Simple Technical Frame --}}
        <div class="relative w-full h-full border border-white/40 p-2 bg-white flex flex-col items-center justify-center">
             @if($imageUrl)
                 <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
             @else
                 <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" class="w-full h-full object-cover">
             @endif
             
             @php
                $len = mb_strlen($text);
                $fontSize = 'text-[11px]';
                if ($len > 80) $fontSize = 'text-[7px]';
                elseif ($len > 40) $fontSize = 'text-[9px]';
             @endphp
             
             <div class="absolute bottom-6 bg-[#0047AB] px-4 py-2 border-2 border-white shadow-2xl max-w-[85%] flex items-center justify-center">
                  <span class="text-white {{ $fontSize }} font-black font-mono-technical uppercase tracking-[0.2em] leading-tight text-center" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $text }}</span>
             </div>
        </div>
    </div>

    <div class="h-16 bg-white border-t-2 border-[#0047AB] px-10 flex items-center justify-between z-20">
        <span class="text-[#0047AB] text-[11px] font-black font-mono-technical uppercase tracking-[0.2em]">{{ $companyLabel }}</span>
        <div class="flex items-center gap-4">
             <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $brandColor }}"></div>
        </div>
    </div>
</div>
