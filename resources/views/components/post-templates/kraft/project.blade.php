@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'text' => 'TRANSFORMATION',
    'preview' => false,
    'slideIndex' => null, // null = interactive alpine preview, int = render specific slide
])

@if($preview)
    @php
        $imageBeforeUrl = $imageBeforeUrl ?? 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=800';
        $imageAfterUrl = $imageAfterUrl ?? 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?q=80&w=800';
        $text = ($text === 'TRANSFORMATION') ? 'Rénovation Complète d\'Intérieur' : $text;
    @endphp
@endif

@php
    $total = 3;
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    {{-- Main Content --}}
    <div class="flex-1 p-6 relative z-10 min-h-0">
        
        {{-- Slide 0: Kraft Split --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 grid grid-cols-2 gap-4" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
             <div class="relative border-[4px] border-slate-900 shadow-[8px_8px_0px_0px_rgba(15,23,42,0.1)] overflow-hidden bg-slate-800">
                 @if($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover grayscale-[0.2]">
                 @endif
                 <div class="absolute -top-1 -left-1 bg-slate-900 text-white px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] font-mono-technical shadow-xl z-20">AVANT</div>
             </div>
             <div class="relative border-[4px] border-slate-900 shadow-[8px_8px_0px_0px_rgba(15,23,42,0.1)] overflow-hidden bg-slate-800">
                 @if($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                 @endif
                 <div class="absolute -top-1 -right-1 bg-orange-600 text-white px-3 py-1 text-[9px] font-black uppercase tracking-[0.2em] font-mono-technical shadow-xl z-20">APRÈS</div>
             </div>
        </div>

        {{-- Slide 1: Full Avant --}}
        @php
            $isSlide1 = $slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = $slideIndex !== 1 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-800 border-[6px] border-slate-900 shadow-[12px_12px_0px_0px_rgba(15,23,42,0.15)] overflow-hidden" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-contain">
                <div class="absolute top-4 left-4 bg-slate-900 text-white px-5 py-2 text-xs font-black uppercase tracking-[0.3em] font-mono-technical shadow-2xl z-20">ETAT INITIAL</div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = $slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = $slideIndex !== 2 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-800 border-[6px] border-slate-900 shadow-[12px_12px_0px_0px_rgba(15,23,42,0.15)] overflow-hidden" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-contain">
                <div class="absolute top-4 right-4 bg-orange-600 text-white px-5 py-2 text-xs font-black uppercase tracking-[0.3em] font-mono-technical shadow-2xl z-20">RÉALISATION FINALE</div>
            @endif
        </div>

        {{-- Controls (Preview Only) --}}
        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 shadow-xl border-2 border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 shadow-xl border-2 border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute bottom-10 right-10 bg-slate-900 text-white text-[10px] font-black font-mono-technical px-3 py-1.5 shadow-xl z-30" x-text="(current + 1) + ' / ' + total"></div>
        @elseif($slideIndex !== null)
             <div class="absolute bottom-10 right-10 bg-slate-900 text-white text-[10px] font-black font-mono-technical px-3 py-1.5 shadow-xl z-30">
                {{ $slideIndex + 1 }} / {{ $total }}
            </div>
        @endif
    </div>

    {{-- Footer/Branding --}}
    @php
        $showBranding = $slideIndex === null || $slideIndex === 0;
        $brandingAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';
    @endphp
    @if($showBranding)
    <div {!! $brandingAlpine !!} class="shrink-0 h-16 flex items-center justify-between border-t-[4px] border-slate-900 pt-1 pb-4 px-6 relative z-20">
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
        <div class="flex items-center gap-4">
            {{-- Industrial Progress dots --}}
            <div class="flex gap-1">
                <div class="w-3 h-3 border-2 border-slate-900 transition-all" :class="current === 0 ? 'bg-slate-900' : 'bg-transparent'"></div>
                <div class="w-3 h-3 border-2 border-slate-900 transition-all" :class="current === 1 ? 'bg-slate-900' : 'bg-transparent'"></div>
                <div class="w-3 h-3 border-2 border-slate-900 transition-all" :class="current === 2 ? 'bg-slate-900' : 'bg-transparent'"></div>
            </div>
            <div class="w-10 h-10 bg-slate-900 flex items-center justify-center -rotate-3 border-2 border-white shadow-xl shrink-0">
                 <div class="w-6 h-6 rounded-full" style="background-color: {{ $brandColor }}"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Industrial Tape Effect --}}
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-32 h-8 bg-orange-600/20 backdrop-blur-sm -rotate-2 border-x-4 border-dashed border-orange-600/40 pointer-events-none opacity-50"></div>
</div>
