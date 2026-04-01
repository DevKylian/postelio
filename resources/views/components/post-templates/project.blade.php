@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'text' => 'TRANSFORMATION',
    'logoUrl' => null,
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'slideIndex' => null,
    'preview' => false,
    'brandTextColor' => '#FFFFFF',
])

@if($preview)
    @php
        $imageBeforeUrl = $imageBeforeUrl ?? 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=800';
        $imageAfterUrl = $imageAfterUrl ?? 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?q=80&w=800';
        $text = ($text === 'TRANSFORMATION') ? 'Rénovation Complète d\'Intérieur' : $text;
        $phone = $phone ?? '01 23 45 67 89';
        $email = $email ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade ?? 'Artisan';
        $city = $city ?? 'Ma Ville';
    @endphp
@endif

@php
    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total = 3 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full aspect-square bg-white flex flex-col relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
    
    <div class="flex-1 relative min-h-0 bg-gray-100">
        
        {{-- Slide 0: Split View --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0 flex" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
            <div class="w-1/2 relative overflow-hidden border-r-2 border-white bg-gray-300 flex items-center justify-center">
                @if ($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute top-4 left-4 bg-black/50 backdrop-blur-sm px-2 py-1 rounded-sm border border-white/20 z-20">
                        <p class="text-white text-[8px] font-black uppercase tracking-widest leading-none">Avant</p>
                    </div>
                @else
                    <div class="text-center text-gray-500">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <p class="text-[10px] font-bold uppercase tracking-widest">Avant</p>
                    </div>
                @endif
            </div>
            <div class="w-1/2 relative overflow-hidden bg-gray-400 flex items-center justify-center">
                @if ($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-sm border border-black/10 z-20">
                        <p class="text-gray-900 text-[8px] font-black uppercase tracking-widest leading-none">Après</p>
                    </div>
                @else
                    <div class="text-center text-gray-100">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <p class="text-[10px] font-bold uppercase tracking-widest">Après</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Slide 1: Full Avant --}}
        @php
            $isSlide1 = $slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = $slideIndex !== 1 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0 bg-gray-300 flex items-center justify-center" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if ($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-4 left-4 bg-black/60 backdrop-blur-md px-4 py-2 rounded-lg border border-white/20 z-20 shadow-xl">
                    <p class="text-white text-xs font-black uppercase tracking-[0.2em] leading-none">Avant</p>
                </div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = (int)$slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = (int)$slideIndex !== 2 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0 bg-gray-400 flex items-center justify-center" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if ($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-lg border border-black/10 z-20 shadow-xl">
                    <p class="text-gray-900 text-xs font-black uppercase tracking-[0.2em] leading-none">Après</p>
                </div>
            @endif
        </div>

        {{-- Slide 3: Contact --}}
        @php
            $isSlide3 = (int)$slideIndex === 3;
            $slide3Alpine = $slideIndex === null ? "x-show=\"current === 3\" style=\"display: none;\"" : '';
            $slide3Style = (int)$slideIndex !== 3 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-0 bg-white flex items-center justify-center" {!! $slide3Alpine !!} style="{{ $slide3Style }}">
             <x-post-templates.contact-slide 
                :brand-color="$brandColor" 
                :company-label="$companyLabel"
                :phone="$phone"
                :email="$email"
                :website="$website"
                :trade="$trade"
                :city="$city"
                :brand-text-color="$brandTextColor"
            />
        </div>
        @endif

        {{-- Controls and Indicators (Preview Only) --}}
        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg z-30" x-text="(current + 1) + ' / ' + total"></div>
        @elseif($slideIndex !== null && (int)$slideIndex !== 3)
             <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg z-30">
                {{ $slideIndex + 1 }} / {{ $total }}
            </div>
        @endif

    </div>

    {{-- Branding Bar --}}
    @php
        $showBranding = $slideIndex === null || (int)$slideIndex === 0;
        $brandingAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';
    @endphp
    @if($showBranding)
    <div {!! $brandingAlpine !!} class="shrink-0 flex flex-col items-center justify-center px-6 py-2 gap-1 z-20" style="background-color: {{ $brandColor }}">
        @php
            $cLen = mb_strlen($companyLabel);
            $cFontSize = 'text-[8px]';
            if ($cLen > 35) $cFontSize = 'text-[6px]';
            elseif ($cLen > 20) $cFontSize = 'text-[7px]';

            $tLen = mb_strlen($text);
            $tFontSize = 'text-sm';
            if ($tLen > 40) $tFontSize = 'text-xs';
            elseif ($tLen > 25) $tFontSize = 'text-[13px]';
        @endphp
        <div class="flex flex-col items-center max-w-full">
            <p class="font-bold uppercase tracking-widest {{ $cFontSize }} font-montserrat leading-none mb-1 truncate w-full text-center opacity-60" style="color: {{ $brandTextColor }}">{{ $companyLabel }}</p>
            <div class="flex items-center gap-1.5 mb-1.5">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-md text-[7px] font-bold uppercase tracking-wider border border-current opacity-40 shrink-0" style="color: {{ $brandTextColor }}">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-md text-[7px] font-bold uppercase tracking-wider border border-current opacity-40 shrink-0" style="color: {{ $brandTextColor }}">{{ $city }}</span>
                @endif
            </div>
            <div class="max-h-[30px] overflow-hidden">
                <h3 class="font-black uppercase tracking-tighter {{ $tFontSize }} font-montserrat leading-none text-center" style="color: {{ $brandTextColor }}">{{ $text }}</h3>
            </div>
        </div>
    </div>
    @endif
</div>
