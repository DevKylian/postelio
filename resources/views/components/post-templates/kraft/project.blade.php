@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'preview' => false,
])

@if($preview)
    @php
        $imageBeforeUrl = $imageBeforeUrl ?? 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=800';
        $imageAfterUrl = $imageAfterUrl ?? 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?q=80&w=800';
    @endphp
@endif

<div class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    {{-- Main Content --}}
    <div class="flex-1 p-6 flex flex-col gap-4 relative z-10">
        {{-- Images Container --}}
        <div class="flex-1 grid grid-cols-2 gap-4">
             <div class="relative border-[4px] border-slate-900 shadow-[8px_8px_0px_0px_rgba(15,23,42,0.1)] overflow-hidden bg-slate-800">
                 <img src="{{ $imageBeforeUrl ?? 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?q=80&w=400' }}" class="absolute inset-0 w-full h-full object-cover grayscale-[0.2]">
                 <div class="absolute -top-1 -left-1 bg-slate-900 text-white px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] font-mono-technical shadow-xl z-20">AVANT</div>
             </div>
             <div class="relative border-[4px] border-slate-900 shadow-[8px_8px_0px_0px_rgba(15,23,42,0.1)] overflow-hidden bg-slate-800">
                 <img src="{{ $imageAfterUrl ?? 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=400' }}" class="absolute inset-0 w-full h-full object-cover">
                 <div class="absolute -top-1 -right-1 bg-orange-600 text-white px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] font-mono-technical shadow-xl z-20">APRÈS</div>
             </div>
        </div>
        
        {{-- Footer/Branding --}}
        <div class="h-14 flex items-center justify-between border-t-[4px] border-slate-900 pt-4">
            @php
                $cLen = mb_strlen($companyLabel);
                $cFontSize = 'text-lg';
                if ($cLen > 40) $cFontSize = 'text-xs';
                elseif ($cLen > 25) $cFontSize = 'text-sm';
                elseif ($cLen > 15) $cFontSize = 'text-base';
            @endphp
            <div class="flex flex-col max-w-[240px]">
                <span class="text-slate-900/60 text-[8px] font-black uppercase tracking-[0.3em] font-mono-technical leading-none mb-1">PROJET RÉALISÉ PAR</span>
                <span class="text-slate-900 {{ $cFontSize }} font-black uppercase tracking-tighter leading-none font-montserrat truncate">{{ $companyLabel }}</span>
            </div>
            <div class="w-10 h-10 bg-slate-900 flex items-center justify-center -rotate-3 border-2 border-white shadow-xl shrink-0">
                 <div class="w-6 h-6 rounded-full" style="background-color: {{ $brandColor }}"></div>
            </div>
        </div>
    </div>

    {{-- Industrial Tape Effect --}}
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-32 h-8 bg-orange-600/20 backdrop-blur-sm -rotate-2 border-x-4 border-dashed border-orange-600/40 pointer-events-none opacity-50"></div>
</div>
