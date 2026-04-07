@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    {{-- Background Grid --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
    </div>

    <div class="flex-1 relative z-10 p-6 min-h-0">
        
        {{-- Slide 0: Blueprint Split --}}
        @php
            $isSlide0 = $slideIndex === null || (int)$slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 grid grid-cols-2 gap-px bg-white/20" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
            <div class="relative overflow-hidden bg-blue-900 border border-white/10">
                @if($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-blue-900 border border-white/10 flex items-center justify-center">
                        <span class="text-white/20 text-[7px] font-black font-mono-technical uppercase">AVANT</span>
                    </div>
                @endif
                <div class="absolute top-0 left-0 px-3 py-1 bg-white text-[#0047AB] text-[9px] font-black font-mono-technical uppercase shadow-xl">AVANT</div>
            </div>
            <div class="relative overflow-hidden bg-blue-900 border border-white/10">
                @if($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-blue-900 border border-white/10 flex items-center justify-center">
                        <span class="text-white/20 text-[7px] font-black font-mono-technical uppercase tracking-widest">APRÈS</span>
                    </div>
                @endif
                <div class="absolute top-0 left-0 px-3 py-1 bg-white text-[#0047AB] text-[9px] font-black font-mono-technical uppercase shadow-xl">APRÈS</div>
            </div>
        </div>

        {{-- Slide 1: Full Avant --}}
        @php
            $isSlide1 = (int)$slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-950 border-2 border-white/20" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-0 left-0 px-5 py-2 bg-white text-[#0047AB] text-xs font-black font-mono-technical uppercase tracking-[0.2em] shadow-2xl">ETAT INITIAL / AVANT</div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = (int)$slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = (int)$slideIndex !== 2 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-900 border-2 border-white/20" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-0 left-0 px-5 py-2 bg-white text-[#0047AB] text-xs font-black font-mono-technical uppercase tracking-[0.2em] shadow-2xl">PROJET FINALISÉ / APRÈS</div>
            @endif
        </div>

        {{-- Slide 3: Contact --}}
        @php
            $isSlide3 = (int)$slideIndex === 3;
            $slide3Alpine = $slideIndex === null ? "x-show=\"current === 3\" style=\"display: none;\"" : '';
            $slide3Style = (int)$slideIndex !== 3 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-6" {!! $slide3Alpine !!} style="{{ $slide3Style }}">
            <x-post-templates.contact-slide 
                :brand-color="$brandColor" 
                :company-label="$companyLabel"
                :phone="$phone"
                :email="$email"
                :website="$website"
                :trade="$trade"
                :city="$city"
                visualStyle="blueprint"
            />
        </div>
        @endif

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
                {{ (int)$slideIndex + 1 }} / {{ $total }}
            </div>
        @endif

    </div>

    {{-- Simplified Footer --}}
    @php
        $showBranding = $hasContactInfo ? ((int)$slideIndex < 3) : true;
        $brandingAlpine = $slideIndex === null ? ($hasContactInfo ? 'x-show="current < 3"' : '') : '';
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
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[#0047AB] {{ $cFontSize }} font-black font-mono-technical tracking-widest uppercase truncate">{{ $companyLabel }}</span>
                <div class="flex items-center gap-1 overflow-hidden">
                    @if($trade)
                        <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/60 text-[6px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $trade }}</span>
                    @endif
                    @if($city)
                        <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/60 text-[6px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $city }}</span>
                    @endif
                </div>
            </div>
            <div class="max-h-[40px] overflow-hidden">
                <span class="text-[#0047AB] {{ $tFontSize }} font-black font-mono-technical tracking-tighter uppercase leading-tight">{{ $text }}</span>
            </div>
        </div>
        <div class="flex items-center gap-6">
             {{-- Progress dots for blueprint --}}
             <div class="flex gap-1.5">
                 @for($i=0; $i<$total; $i++)
                    <div class="w-2 h-2 rounded-full transition-all" 
                         @if($preview) :class="current === {{ $i }} ? 'bg-[#0047AB]' : 'bg-blue-100'"
                         @else class="{{ (int)($slideIndex ?? 0) === $i ? 'bg-[#0047AB]' : 'bg-blue-100' }}" @endif>
                    </div>
                 @endfor
             </div>
             <div class="w-8 h-8 rounded-full border-2 border-[#0047AB] flex items-center justify-center p-1">
                  <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] rounded-full" style="background-color: {{ $brandColor }}"></div>
             </div>
        </div>
    </div>
    @endif
</div>
