@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'preview' => false,
])

<div class="w-full aspect-square bg-[#CBD5E1] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    {{-- Content --}}
    <div class="flex-1 flex flex-col p-6 gap-4 relative z-10">
        <div class="flex-1 grid grid-cols-2 gap-4">
            <div class="relative rounded-lg overflow-hidden border-2 border-slate-900 shadow-xl">
                <img src="{{ $imageBeforeUrl ?? 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?q=80&w=400' }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-3 left-3 bg-slate-900 px-3 py-1 rounded text-[10px] font-black text-white uppercase font-montserrat tracking-widest">Avant</div>
            </div>
            <div class="relative rounded-lg overflow-hidden border-2 border-slate-900 shadow-xl">
                <img src="{{ $imageAfterUrl ?? 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=400' }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-3 left-3 bg-slate-900 px-3 py-1 rounded text-[10px] font-black text-white uppercase font-montserrat tracking-widest" style="background-color: {{ $brandColor }}">Après</div>
            </div>
        </div>

        <div class="h-16 flex items-center justify-between border-t border-slate-900/10 pt-4">
             @php
                 $cLen = mb_strlen($companyLabel);
                 $cFontSize = 'text-lg';
                 if ($cLen > 40) $cFontSize = 'text-xs';
                 elseif ($cLen > 25) $cFontSize = 'text-sm';
                 elseif ($cLen > 15) $cFontSize = 'text-base';
             @endphp
             <div class="flex flex-col max-w-[200px]">
                 <span class="text-slate-900/40 text-[9px] font-black uppercase tracking-widest leading-none mb-1">Société</span>
                 <span class="text-slate-900 {{ $cFontSize }} font-black uppercase tracking-tighter leading-tight font-montserrat truncate">{{ $companyLabel }}</span>
             </div>
             <div class="w-12 h-12 rounded-lg border-2 border-slate-900 flex items-center justify-center bg-white shadow-lg">
                  <div class="w-8 h-8 rounded-full" style="background-color: {{ $brandColor }}"></div>
             </div>
        </div>
    </div>
</div>
