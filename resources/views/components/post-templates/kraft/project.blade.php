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
    'slideIndex' => null,
    'exporting' => false,
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

    // Contrast logic for tags
    $hex = str_replace('#', '', $brandColor);
    if (strlen($hex) == 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    $tagTextCol = ($yiq > 180) ? '#1E293B' : '#FFFFFF';
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    {{-- Main Content --}}
    <div class="flex-1 p-6 relative z-10 min-h-0">
        
        {{-- Slide 0: Kraft Split --}}
        @php
            $isSlide0 = $slideIndex === null || (int)$slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 grid grid-cols-2 gap-4" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
             <div class="relative border-[4px] border-gray-900 shadow-[8px_8px_0px_0px_rgba(15,23,42,0.1)] overflow-hidden bg-gray-800">
                 @if($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                 @endif
                 <div class="absolute top-2 left-2 px-3 py-1 rounded-full shadow-xl z-20" style="background-color: {{ $brandColor }}">
                     <p class="text-[9px] font-black uppercase tracking-[0.2em] font-mono-technical leading-none" style="color: {{ $tagTextCol }}">Avant</p>
                 </div>
             </div>
             <div class="relative border-[4px] border-gray-900 shadow-[8px_8px_0px_0px_rgba(15,23,42,0.1)] overflow-hidden bg-gray-800">
                 @if($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                 @endif
                 <div class="absolute top-2 right-2 px-3 py-1 rounded-full shadow-xl z-20" style="background-color: {{ $brandColor }}">
                     <p class="text-[9px] font-black uppercase tracking-[0.2em] font-mono-technical leading-none" style="color: {{ $tagTextCol }}">Après</p>
                 </div>
             </div>
        </div>

        {{-- Slide 1: Full Avant --}}
        @php
            $isSlide1 = (int)$slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-950 border-[6px] border-gray-900 shadow-[12px_12px_0px_0px_rgba(15,23,42,0.15)] overflow-hidden" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-2 left-2 px-5 py-2 rounded-full shadow-2xl z-20" style="background-color: {{ $brandColor }}">
                    <p class="text-xs font-black uppercase tracking-[0.3em] font-mono-technical leading-none" style="color: {{ $tagTextCol }}">Avant</p>
                </div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = (int)$slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = (int)$slideIndex !== 2 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-900 border-[6px] border-gray-900 shadow-[12px_12px_0px_0px_rgba(15,23,42,0.15)] overflow-hidden" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-2 right-2 px-5 py-2 rounded-full shadow-2xl z-20" style="background-color: {{ $brandColor }}">
                    <p class="text-xs font-black uppercase tracking-[0.3em] font-mono-technical leading-none" style="color: {{ $tagTextCol }}">Après</p>
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
        <div class="absolute inset-6" {!! $slide3Alpine !!} style="{{ $slide3Style }}">
            <x-post-templates.contact-slide 
                :brand-color="$brandColor" 
                :company-label="$companyLabel"
                :phone="$phone"
                :email="$email"
                :website="$website"
                :trade="$trade"
                :city="$city"
                visualStyle="kraft"
            />
        </div>
        @endif

        @if($preview && !$exporting)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 shadow-xl border-2 border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 shadow-xl border-2 border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute bottom-10 right-10 bg-gray-900 text-white text-[10px] font-black font-mono-technical px-3 py-1.5 shadow-xl z-30" x-text="(current + 1) + ' / ' + total"></div>
        @elseif($slideIndex !== null && !$exporting)
             <div class="absolute bottom-10 right-10 bg-gray-900 text-white text-[10px] font-black font-mono-technical px-3 py-1.5 shadow-xl z-30">
                {{ (int)$slideIndex + 1 }} / {{ $total }}
            </div>
        @endif
    </div>

    {{-- Footer/Branding --}}
    @php
        $showBranding = $hasContactInfo ? ((int)$slideIndex < 3) : true;
        $brandingAlpine = $slideIndex === null ? ($hasContactInfo ? 'x-show="current < 3"' : '') : '';
    @endphp
    @if($showBranding)
    <div {!! $brandingAlpine !!} class="shrink-0 h-16 flex items-center justify-between border-t-[4px] border-gray-900 pt-1 pb-4 px-6 relative z-20">
        @php
            $cLen = mb_strlen($companyLabel);
            $cFontSize = 'text-lg';
            if ($cLen > 40) $cFontSize = 'text-xs';
            elseif ($cLen > 25) $cFontSize = 'text-sm';
            elseif ($cLen > 15) $cFontSize = 'text-base';
        @endphp
        <div class="flex flex-col max-w-[240px]">
            <span class="text-gray-900/60 text-[8px] font-black uppercase tracking-[0.3em] font-mono-technical leading-none mb-1">PROJET RÉALISÉ PAR</span>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-gray-900 {{ $cFontSize }} font-black uppercase tracking-tighter leading-none font-montserrat truncate">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
            </div>
            <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/60 text-[6px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/60 text-[6px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $city }}</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-4">
            {{-- Industrial Progress dots --}}
            <div class="flex gap-1">
                @for($i=0; $i<$total; $i++)
                    <div class="w-3 h-3 border-2 border-gray-900 transition-all" 
                         @if($preview) :class="current === {{ $i }} ? 'bg-gray-900' : 'bg-transparent'"
                         @else class="{{ (int)($slideIndex ?? 0) === $i ? 'bg-gray-900' : 'bg-transparent' }}" @endif>
                    </div>
                @endfor
            </div>
            <div class="w-10 h-10 bg-gray-900 flex items-center justify-center -rotate-3 border-2 border-white shadow-xl shrink-0">
                 <div class="w-6 h-6 rounded-full" style="background-color: {{ $brandColor }}"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Industrial Tape Effect --}}
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-32 h-8 bg-orange-600/20 backdrop-blur-sm -rotate-2 border-x-4 border-dashed border-orange-600/40 pointer-events-none opacity-50"></div>
</div>
