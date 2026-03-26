@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'SUR LE TERRAIN') ? 'La passion du métier se lit dans chaque geste.' : $text;
    @endphp
@endif

<div class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>

    {{-- Image area --}}
    <div class="flex-1 min-h-0 relative flex items-center justify-center p-10 pb-4">
        <div class="relative w-full h-full border-[8px] border-slate-900 p-3 bg-white shadow-2xl rotate-1 overflow-hidden">
             <div class="w-full h-full overflow-hidden relative">
                 @if($imageUrl)
                     <img src="{{ $imageUrl }}" class="w-full h-full object-contain">
                 @else
                     <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" class="w-full h-full object-cover">
                 @endif
                 <div class="absolute inset-0 shadow-[inset_0_0_100px_rgba(0,0,0,0.2)]"></div>
             </div>
        </div>
    </div>

    {{-- Footer with text + company --}}
    <div class="h-[72px] shrink-0 bg-[#CBB198] border-t-[6px] border-slate-900 px-8 flex items-center justify-between z-20">
        @php
            $displayText = $text ?: 'LIFESTYLE';
            $len = mb_strlen($displayText);
            $fontSize = 'text-[12px]';
            if ($len > 25) $fontSize = 'text-[9px]';
            elseif ($len > 18) $fontSize = 'text-[10px]';
        @endphp
        <div class="flex flex-col max-w-[200px] overflow-hidden">
             <span class="text-slate-900 {{ $fontSize }} font-black uppercase tracking-[0.1em] font-montserrat leading-tight truncate">{{ $displayText }}</span>
             <span class="text-slate-900/40 text-[8px] font-black uppercase font-mono-technical mt-0.5 truncate">{{ $companyLabel }}</span>
        </div>
        <div class="flex items-center gap-4">
        </div>
    </div>
</div>
