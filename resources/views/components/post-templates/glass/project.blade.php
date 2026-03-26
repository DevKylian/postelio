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

<div {!! $alpineData !!} class="w-full aspect-square bg-slate-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/20 group-hover:-translate-y-2' : '' }}">
    
    <div class="flex-1 flex min-h-0 relative">
        {{-- Slide 0: Glass Split --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0 flex" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
            <div class="w-1/2 relative overflow-hidden bg-slate-800">
                @if ($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @endif
            </div>
            <div class="w-1/2 relative overflow-hidden bg-slate-700">
                @if ($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @endif
            </div>
            {{-- Glass Divider --}}
            <div class="absolute inset-y-0 left-1/2 -ml-px w-0.5 bg-white/30 backdrop-blur-md z-10"></div>
        </div>

        {{-- Slide 1: Full Avant --}}
        @php
            $isSlide1 = $slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = $slideIndex !== 1 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0 bg-slate-800" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if ($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-contain">
                <div class="absolute top-6 left-6 backdrop-blur-xl bg-white/10 border border-white/20 px-4 py-2 rounded-xl z-20 shadow-2xl">
                    <p class="text-white text-[10px] font-black uppercase tracking-[0.3em] leading-none">Avant</p>
                </div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = $slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = $slideIndex !== 2 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0 bg-slate-700" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if ($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-contain">
                <div class="absolute top-6 right-6 backdrop-blur-xl bg-white/20 border border-white/30 px-4 py-2 rounded-xl z-20 shadow-2xl">
                    <p class="text-white text-[10px] font-black uppercase tracking-[0.3em] leading-none">Après</p>
                </div>
            @endif
        </div>

        {{-- Controls (Preview Only) --}}
        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-2xl backdrop-blur-xl bg-white/10 text-white flex items-center justify-center hover:bg-white/20 border border-white/10 shadow-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-2xl backdrop-blur-xl bg-white/10 text-white flex items-center justify-center hover:bg-white/20 border border-white/10 shadow-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-6 right-6 backdrop-blur-xl bg-black/40 text-white text-[10px] font-bold px-3 py-1.5 rounded-xl z-30 border border-white/10" x-text="(current + 1) + ' / ' + total"></div>
        @elseif($slideIndex !== null)
            <div class="absolute top-6 right-6 backdrop-blur-xl bg-black/40 text-white text-[10px] font-bold px-3 py-1.5 rounded-xl z-30 border border-white/10">
                {{ $slideIndex + 1 }} / {{ $total }}
            </div>
        @endif
    </div>


    {{-- Glass Floor --}}
    @php
        $showBranding = $slideIndex === null || $slideIndex === 0;
        $brandingAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';
    @endphp
    @if($showBranding)
    <div {!! $brandingAlpine !!} class="shrink-0 backdrop-blur-xl bg-white/10 border-t border-white/20 px-5 py-2.5 flex items-center justify-between shadow-2xl relative z-20">
        @php
            $cLen = mb_strlen($companyLabel);
            $cFontSize = 'text-[9px]';
            if ($cLen > 30) $cFontSize = 'text-[7px]';
            elseif ($cLen > 20) $cFontSize = 'text-[8px]';

            $tLen = mb_strlen($text ?: 'TRANSFORMATION');
            $tFontSize = 'text-sm';
            if ($tLen > 40) $tFontSize = 'text-[10px]';
            elseif ($tLen > 25) $tFontSize = 'text-xs';
        @endphp
        <div class="flex flex-col min-w-0 flex-1">
            <span class="text-white/50 {{ $cFontSize }} font-bold uppercase tracking-[0.2em] font-montserrat truncate">{{ $companyLabel }}</span>
            <span class="text-white {{ $tFontSize }} font-black uppercase tracking-tighter font-montserrat truncate">{{ $text ?: 'TRANSFORMATION' }}</span>
        </div>
    </div>
    @endif
    
    {{-- Decorative glow --}}
    <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full blur-[80px] opacity-20 pointer-events-none" style="background-color: {{ $brandColor }}"></div>
    <div class="absolute -bottom-24 -left-24 w-48 h-48 rounded-full blur-[80px] opacity-10 pointer-events-none" style="background-color: {{ $brandColor }}"></div>
</div>
