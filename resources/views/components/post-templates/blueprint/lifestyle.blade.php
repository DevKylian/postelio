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

    {{-- Image area — explicit height: 360-64=296px, with min-h-0 to allow flex shrink --}}
    <div class="flex-1 min-h-0 relative flex items-center justify-center p-10">
        <div class="relative w-full h-full border border-white/40 p-2 bg-white overflow-hidden">
             @if($imageUrl)
                 <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
             @else
                 <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" class="w-full h-full object-cover">
             @endif

             {{-- Text zone: anchored inside photo frame, max 2 lines --}}
             @php
                $displayText = mb_substr($text, 0, 45);
                $len = mb_strlen($displayText);
                $fontSize = 'text-[11px]';
                if ($len > 35) $fontSize = 'text-[8px]';
                elseif ($len > 25) $fontSize = 'text-[9px]';
             @endphp

             <div class="absolute bottom-2 left-2 right-2 h-[36px] bg-[#0047AB] border-2 border-white shadow-2xl flex items-center justify-center px-3 overflow-hidden">
                  <span class="text-white {{ $fontSize }} font-black font-mono-technical uppercase tracking-[0.15em] leading-tight text-center" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $displayText }}</span>
             </div>
        </div>
    </div>

    {{-- Footer — fixed 64px --}}
    <div class="h-16 shrink-0 bg-white border-t-2 border-[#0047AB] px-10 flex items-center justify-between z-20">
        <span class="text-[#0047AB] text-[11px] font-black font-mono-technical uppercase tracking-[0.2em] truncate max-w-[80%]">{{ $companyLabel }}</span>
        <div class="flex items-center gap-4">
             <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $brandColor }}"></div>
        </div>
    </div>
</div>
