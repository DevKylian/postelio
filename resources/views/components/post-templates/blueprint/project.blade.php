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

<div {!! $alpineData !!} class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    {{-- Background Grid --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
    </div>

    <div class="flex-1 relative z-10 p-6 min-h-0">
        
        {{-- Slide 0: Blueprint Split --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 grid grid-cols-2 gap-px bg-white/20" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
            <div class="relative overflow-hidden bg-blue-900 border border-white/10">
                @if($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-blue-900 border border-white/10 flex items-center justify-center">
                        <span class="text-white/20 text-[7px] font-black font-mono-technical uppercase">AVANT</span>
                    </div>
                @endif
                <div class="absolute top-0 left-0 px-3 py-1 bg-white text-[#0047AB] text-[9px] font-black font-mono-technical uppercase shadow-xl">AVANT</div>
            </div>
            <div class="relative overflow-hidden bg-blue-900 border border-white/10">
                @if($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-blue-900 border border-white/10 flex items-center justify-center">
                        <span class="text-white/20 text-[7px] font-black font-mono-technical uppercase tracking-widest">APRÈS</span>
                    </div>
                @endif
                <div class="absolute top-0 left-0 px-3 py-1 bg-white text-[#0047AB] text-[9px] font-black font-mono-technical uppercase shadow-xl">APRÈS</div>
            </div>
        </div>

        {{-- Slide 1: Full Avant --}}
        @php
            $isSlide1 = $slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = $slideIndex !== 1 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-blue-900 border-2 border-white/20" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover opacity-80">
                <div class="absolute top-0 left-0 px-5 py-2 bg-white text-[#0047AB] text-xs font-black font-mono-technical uppercase tracking-[0.2em] shadow-2xl">ETAT INITIAL / AVANT</div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = $slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = $slideIndex !== 2 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-blue-900 border-2 border-white/20" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-0 left-0 px-5 py-2 bg-white text-[#0047AB] text-xs font-black font-mono-technical uppercase tracking-[0.2em] shadow-2xl">PROJET FINALISÉ / APRÈS</div>
            @endif
        </div>

        {{-- Controls (Preview Only) --}}
        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-10 right-10 bg-white text-[#0047AB] text-[10px] font-black font-mono-technical px-3 py-1.5 shadow-xl z-30" x-text="(current + 1) + ' / ' + total"></div>
        @elseif($slideIndex !== null)
             <div class="absolute top-10 right-10 bg-white text-[#0047AB] text-[10px] font-black font-mono-technical px-3 py-1.5 shadow-xl z-30">
                {{ $slideIndex + 1 }} / {{ $total }}
            </div>
        @endif

    </div>

    {{-- Simplified Footer --}}
    @php
        $showBranding = $slideIndex === null || $slideIndex === 0;
        $brandingAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';
    @endphp
    @if($showBranding)
    <div {!! $brandingAlpine !!} class="h-20 bg-white px-10 flex items-center justify-between border-t-2 border-[#0047AB] relative z-20">
        @php
            $cLen = mb_strlen($companyLabel);
            $cFontSize = 'text-xs';
            if ($cLen > 30) $cFontSize = 'text-[9px]';
            elseif ($cLen > 20) $cFontSize = 'text-[10px]';

            $tLen = mb_strlen($text);
            $tFontSize = 'text-base';
            if ($tLen > 40) $tFontSize = 'text-xs';
            elseif ($tLen > 25) $tFontSize = 'text-sm';
        @endphp
        <div class="flex flex-col max-w-[300px]">
            <span class="text-[#0047AB] {{ $cFontSize }} font-black font-mono-technical tracking-widest uppercase mb-1 truncate">{{ $companyLabel }}</span>
            <div class="max-h-[40px] overflow-hidden">
                <span class="text-[#0047AB] {{ $tFontSize }} font-black font-mono-technical tracking-tighter uppercase leading-tight">{{ $text }}</span>
            </div>
        </div>
        <div class="flex items-center gap-6">
             {{-- Progress dots for blueprint --}}
             <div class="flex gap-1.5">
                 <div class="w-2 h-2 rounded-full transition-all" :class="current === 0 ? 'bg-[#0047AB]' : 'bg-blue-100'"></div>
                 <div class="w-2 h-2 rounded-full transition-all" :class="current === 1 ? 'bg-[#0047AB]' : 'bg-blue-100'"></div>
                 <div class="w-2 h-2 rounded-full transition-all" :class="current === 2 ? 'bg-[#0047AB]' : 'bg-blue-100'"></div>
             </div>
             <div class="w-8 h-8 rounded-full border-2 border-[#0047AB] flex items-center justify-center p-1">
                  <div class="w-full h-full rounded-full" style="background-color: {{ $brandColor }}"></div>
             </div>
        </div>
    </div>
    @endif
</div>
