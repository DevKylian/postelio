@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
])

<div class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>

    {{-- Image area — flex-1 with min-h-0 to allow proper shrink --}}
    <div class="flex-1 min-h-0 relative flex items-center justify-center p-12">
        <div class="relative w-full h-full border-[8px] border-slate-900 p-3 bg-white shadow-2xl rotate-1 overflow-hidden">
             <div class="w-full h-full overflow-hidden relative">
                 @if($imageUrl)
                     <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
                 @else
                     <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" class="w-full h-full object-cover">
                 @endif
                 <div class="absolute inset-0 shadow-[inset_0_0_100px_rgba(0,0,0,0.2)]"></div>
             </div>
        </div>
    </div>

    {{-- Footer — fixed 80px, shrink-0 to guarantee visibility --}}
    <div class="h-20 shrink-0 bg-[#CBB198] border-t-[6px] border-slate-900 px-10 flex items-center justify-between z-20">
        @php
            $displayText = mb_substr($text ?: 'LIFESTYLE', 0, 40);
            $len = mb_strlen($displayText);
            $fontSize = 'text-[13px]';
            if ($len > 30) $fontSize = 'text-[9px]';
            elseif ($len > 20) $fontSize = 'text-[11px]';
        @endphp
        <div class="flex flex-col max-w-[220px] overflow-hidden">
             <span class="text-slate-900 {{ $fontSize }} font-black uppercase tracking-[0.15em] font-montserrat leading-tight" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $displayText }}</span>
             <span class="text-slate-900/40 text-[8px] font-black uppercase font-mono-technical mt-1 truncate">{{ $companyLabel }}</span>
        </div>
        <div class="w-10 h-10 shrink-0 rounded-full border-4 border-slate-900 flex items-center justify-center">
             <div class="w-4 h-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
        </div>
    </div>
</div>
