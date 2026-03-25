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

<div class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    <div class="flex-1 relative flex items-center justify-center p-16">
        <div class="relative w-full h-full border-[6px] border-slate-900 p-4 bg-white shadow-2xl -rotate-1">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" class="w-full h-full object-cover grayscale-[0.1]">
            @else
                <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="w-full h-full object-cover grayscale-[0.1]">
            @endif
            
            @php
                $len = mb_strlen($text ?: 'DÉTAIL TECHNIQUE');
                $fontSize = 'text-[8px]';
                if ($len > 30) $fontSize = 'text-[6px]';
                elseif ($len > 20) $fontSize = 'text-[7px]';
            @endphp
            <div class="absolute bottom-6 left-6 bg-slate-900 text-white px-3 py-1 {{ $fontSize }} font-black uppercase tracking-widest font-mono-technical max-w-[80%] truncate">
                {{ $text ?: 'DÉTAIL TECHNIQUE' }}
            </div>
        </div>
    </div>

    <div class="h-16 px-10 bg-slate-900 flex items-center justify-between z-20">
         <span class="text-white text-[11px] font-black uppercase tracking-[0.2em] font-montserrat">{{ $companyLabel }}</span>
         <div class="flex items-center gap-4">
              <div class="w-12 h-1.5" style="background-color: {{ $brandColor }}"></div>
         </div>
    </div>
</div>
