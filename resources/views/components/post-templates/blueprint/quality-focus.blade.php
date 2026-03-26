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

<div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 20px 20px;">
    </div>

    <div class="flex-1 relative flex items-center justify-center p-16 overflow-hidden text-[#0047AB]">
        <div class="relative w-full h-full border border-white/30 p-2 bg-white shadow-2xl">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" class="w-full h-full object-contain">
            @else
                <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="w-full h-full object-cover">
            @endif
            
            
        </div>
    </div>

    <div class="h-16 px-10 bg-white border-t-2 border-[#0047AB] flex items-center justify-between z-20">
          @php
                $len = mb_strlen($text);
                $fontSize = 'text-sm';
                if ($len > 80) $fontSize = 'text-[9px]';
                elseif ($len > 40) $fontSize = 'text-xs';
          @endphp
          <div class="flex-1 max-w-[70%] max-h-[50px] overflow-hidden flex items-center shrink-0">
               <span class="text-[#0047AB] {{ $fontSize }} font-black font-mono-technical tracking-tighter uppercase leading-tight truncate-multiline">{{ $text }}</span>
          </div>
         <div class="flex items-center gap-4">
              <span class="text-[#0047AB]/40 text-[7px] font-mono-technical uppercase tracking-widest">{{ $companyLabel }}</span>
              <div class="w-5 h-2" style="background-color: {{ $brandColor }}"></div>
         </div>
    </div>
</div>
