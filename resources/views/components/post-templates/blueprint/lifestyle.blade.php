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

    {{-- Photo area --}}
    <div class="flex-1 min-h-0 relative flex items-center justify-center pt-8 px-10">
        <div class="relative w-full h-full border border-white/40 p-2 bg-white overflow-hidden">
             @if($imageUrl)
                 <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
             @else
                 <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" class="w-full h-full object-cover">
             @endif
        </div>
    </div>

    {{-- Text zone: between photo and footer, fixed 40px height --}}
    <div class="h-[40px] shrink-0 flex items-center justify-center px-10 overflow-hidden">
        @php
            $len = mb_strlen($text);
            $fontSize = 'text-[11px]';
            if ($len > 30) $fontSize = 'text-[8px]';
            elseif ($len > 20) $fontSize = 'text-[9px]';
        @endphp
        <div class="bg-white/15 border border-white/30 px-4 py-1.5 max-w-full overflow-hidden">
            <span class="text-white {{ $fontSize }} font-black font-mono-technical uppercase tracking-[0.15em] leading-tight text-center block truncate">{{ $text }}</span>
        </div>
    </div>

    {{-- Footer — fixed 48px --}}
    <div class="h-12 shrink-0 bg-white border-t-2 border-[#0047AB] px-10 flex items-center justify-between z-20">
        <span class="text-[#0047AB] text-[10px] font-black font-mono-technical uppercase tracking-[0.2em] truncate max-w-[80%]">{{ $companyLabel }}</span>
        <div class="flex items-center gap-4">
             <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $brandColor }}"></div>
        </div>
    </div>
</div>
