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

<div {!! $alpineData !!} class="w-full aspect-square bg-[#CBD5E1] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    {{-- Main Content --}}
    <div class="flex-1 p-6 relative z-10 min-h-0">
        
        {{-- Slide 0: Beton Split --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 grid grid-cols-2 gap-4" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
            <div class="relative rounded-lg overflow-hidden border-2 border-slate-900 shadow-xl bg-slate-400">
                @if($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @endif
                <div class="absolute top-3 left-3 bg-slate-900 px-3 py-1 rounded text-[10px] font-black text-white uppercase font-montserrat tracking-widest">Avant</div>
            </div>
            <div class="relative rounded-lg overflow-hidden border-2 border-slate-900 shadow-xl bg-slate-500">
                @if($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @endif
                <div class="absolute top-3 left-3 bg-slate-900 px-3 py-1 rounded text-[10px] font-black text-white uppercase font-montserrat tracking-widest" style="background-color: {{ $brandColor }}">Après</div>
            </div>
        </div>

        {{-- Slide 1: Full Avant --}}
        @php
            $isSlide1 = $slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = $slideIndex !== 1 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-400 rounded-xl border-4 border-slate-900 overflow-hidden shadow-2xl" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-6 left-6 bg-slate-900 px-5 py-2 rounded-lg text-xs font-black text-white uppercase font-montserrat tracking-[0.2em] shadow-2xl z-20">VUE INITIALE</div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = $slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = $slideIndex !== 2 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-500 rounded-xl border-4 border-slate-900 overflow-hidden shadow-2xl" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-6 right-6 px-5 py-2 rounded-lg text-xs font-black text-white uppercase font-montserrat tracking-[0.2em] shadow-2xl z-20" style="background-color: {{ $brandColor }}">RÉSULTAT FINAL</div>
            @endif
        </div>

        {{-- Controls (Preview Only) --}}
        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 shadow-2xl border-2 border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 shadow-2xl border-2 border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-10 right-10 bg-slate-900 text-white text-[10px] font-black font-montserrat px-3 py-1.5 rounded-lg shadow-2xl z-30" x-text="(current + 1) + ' / ' + total"></div>
        @elseif($slideIndex !== null)
             <div class="absolute top-10 right-10 bg-slate-900 text-white text-[10px] font-black font-montserrat px-3 py-1.5 rounded-lg shadow-2xl z-30">
                {{ $slideIndex + 1 }} / {{ $total }}
            </div>
        @endif

    </div>

    @php
        $showBranding = $slideIndex === null || $slideIndex === 0;
        $brandingAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';
    @endphp
    @if($showBranding)
    <div {!! $brandingAlpine !!} class="shrink-0 h-20 flex items-center justify-between border-t border-slate-900/10 p-6 relative z-20">
         @php
             $cLen = mb_strlen($companyLabel);
             $cFontSize = 'text-lg';
             if ($cLen > 40) $cFontSize = 'text-xs';
             elseif ($cLen > 25) $cFontSize = 'text-sm';
             elseif ($cLen > 15) $cFontSize = 'text-base';
         @endphp
         <div class="flex flex-col max-w-[200px]">
             <span class="text-slate-900/40 text-[9px] font-black uppercase tracking-widest leading-none mb-1">PROJET RÉALISÉ PAR</span>
             <span class="text-slate-900 {{ $cFontSize }} font-black uppercase tracking-tighter leading-tight font-montserrat truncate">{{ $companyLabel }}</span>
         </div>
         <div class="flex items-center gap-4">
             {{-- Progress dots --}}
             <div class="flex gap-2">
                 <div class="w-2 h-2 rounded-full transition-all" :class="current === 0 ? 'bg-slate-900' : 'bg-slate-300'"></div>
                 <div class="w-2 h-2 rounded-full transition-all" :class="current === 1 ? 'bg-slate-900' : 'bg-slate-300'"></div>
                 <div class="w-2 h-2 rounded-full transition-all" :class="current === 2 ? 'bg-slate-900' : 'bg-slate-300'"></div>
             </div>
             <div class="w-12 h-12 rounded-lg border-2 border-slate-900 flex items-center justify-center bg-white shadow-lg">
                  <div class="w-8 h-8 rounded-full" style="background-color: {{ $brandColor }}"></div>
             </div>
         </div>
    </div>
    @endif
</div>
