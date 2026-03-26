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
        $text = ($text === '') ? 'Le choix des meilleurs matériaux pour une durabilité garantie.' : $text;
    @endphp
@endif

<div class="w-full aspect-square bg-[#CBD5E1] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    <div class="flex-1 flex flex-col p-8 relative z-10">
        <div class="flex-1 rounded-2xl border-4 border-slate-900 bg-white overflow-hidden relative shadow-2xl">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" class="w-full h-full object-contain">
            @else
                <img src="https://images.unsplash.com/photo-1504148455328-c376907d081c?q=80&w=600" class="w-full h-full object-cover grayscale-[0.2]">
            @endif
            

            @php
                $fLen = mb_strlen($text ?: 'FOCUS QUALITÉ');
                $fFontSize = 'text-[10px]';
                if ($fLen > 40) $fFontSize = 'text-[7px]';
                elseif ($fLen > 20) $fFontSize = 'text-[8px]';
            @endphp
            <div class="absolute bottom-6 left-6 bg-slate-900 text-white px-4 py-2 font-black uppercase tracking-widest rounded-lg max-w-[80%] overflow-hidden">
                <span class="{{ $fFontSize }}">{{ $text ?: 'FOCUS QUALITÉ' }}</span>
            </div>
        </div>
    </div>

    <div class="h-16 px-10 flex items-center justify-between bg-slate-900">
         <span class="text-white text-[10px] font-black font-montserrat uppercase tracking-[0.2em]">{{ $companyLabel }}</span>
         <div class="w-8 h-8 rounded border-2 border-white/20 flex items-center justify-center">
              <div class="w-4 h-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
         </div>
    </div>
</div>
