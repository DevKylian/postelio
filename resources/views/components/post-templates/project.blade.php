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
    'accentColor' => '#0F172A',
    'accentTextColor' => '#FFFFFF',
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

    // Contrast logic for brand color badges
    $hex = str_replace('#', '', $brandColor);
    if (strlen($hex) == 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    $badgeTextCol = ($yiq >= 186) ? '#1E293B' : '#FFFFFF';
    // Badge background uses brand color with opacity
    // For very dark brands, slightly boost contrast; for very light, slightly darken
    $badgeBg = $brandColor;
    $badgeTextStyle = "color: $badgeTextCol;";
    $badgeStyle = "background-color: $badgeBg; border-color: rgba(255,255,255,0.3); color: $badgeTextCol;";
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? '4:5') === '4:5' ? '4/5' : '1/1' }}] bg-white flex flex-col relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
    
    <div class="flex-1 relative min-h-0 bg-gray-100">
        
        {{-- Slide 0: Split View --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0 flex" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
            <div class="w-1/2 relative overflow-hidden border-r border-white/20 bg-gray-300 flex items-center justify-center">
                @if ($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute top-6 left-6 px-4 h-7 grid-center rounded-full shadow-2xl z-20 border" style="{{ $badgeStyle }}">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] leading-none v-align-fix">Avant</p>
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-center text-gray-500">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <p class="text-[9px] font-bold uppercase tracking-widest leading-none v-align-fix" style="color: {{ $brandColor }}">Avant</p>
                    </div>
                @endif
            </div>
            <div class="w-1/2 relative overflow-hidden bg-gray-400 flex items-center justify-center">
                @if ($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute top-6 right-6 px-4 h-7 grid-center rounded-full shadow-2xl z-20 border" style="{{ $badgeStyle }}">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] leading-none v-align-fix">Après</p>
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-center text-gray-100">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <p class="text-[9px] font-bold uppercase tracking-widest leading-none v-align-fix" style="color: {{ $brandColor }}">Après</p>
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
        <div class="absolute inset-0 bg-slate-900 flex items-center justify-center" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if ($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-6 left-6 px-4 h-7 grid-center rounded-full shadow-2xl z-20 border" style="{{ $badgeStyle }}">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] leading-none v-align-fix">Avant</p>
                </div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = (int)$slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = (int)$slideIndex !== 2 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0 bg-slate-900 flex items-center justify-center" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if ($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute top-6 right-6 px-4 h-7 grid-center rounded-full shadow-2xl z-20 border" style="{{ $badgeStyle }}">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] leading-none v-align-fix">Après</p>
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
                :accent-color="$accentColor"
                :accent-text-color="$accentTextColor"
            />
        </div>
        @endif

        {{-- Branding Overlay — bottom compact strip --}}
        @php
            $showBranding   = $slideIndex === null || (int)$slideIndex === 0;
            $brandingAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';

            // Contrast for tags
            $hex = str_replace('#', '', $brandColor);
            if(strlen($hex) == 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            $r   = hexdec(substr($hex, 0, 2));
            $g   = hexdec(substr($hex, 2, 2));
            $b   = hexdec(substr($hex, 4, 2));
            $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
            $badgeTextCol = ($yiq >= 186) ? '#1E293B' : '#FFFFFF';
            $isDarkBrand  = $yiq < 120;
            if ($isDarkBrand) {
                $tagBg = 'rgba(255,255,255,0.1)'; $tagBorder = 'rgba(255,255,255,0.2)'; $tagText = 'rgba(255,255,255,0.9)';
            } else {
                $tagBg = $brandColor . '25'; $tagBorder = $brandColor . '40'; $tagText = $brandColor;
            }
            $tagStyle = "background-color: $tagBg; border-color: $tagBorder; color: $tagText;";

            $tLen      = mb_strlen($text);
            $tFontSize = $tLen > 40 ? 'text-xs' : ($tLen > 25 ? 'text-sm' : 'text-base');
        @endphp
        @if($showBranding)
        <div {!! $brandingAlpine !!} class="absolute bottom-5 inset-x-0 mx-auto w-[85%] z-20">
            <div class="backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden" 
                 style="background-color: {{ $accentColor }}cc; border: 1px solid rgba(255,255,255,0.1); border-top: 2px solid {{ $brandColor }}">
                <div class="px-4 py-3 flex items-center gap-3">
                    {{-- Title --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="font-black {{ $tFontSize }} leading-tight tracking-tight line-clamp-2" style="color: {{ $accentTextColor }}">{{ $text }}</h3>
                    </div>
                    {{-- Divider --}}
                    <div class="w-px self-stretch bg-white/10 shrink-0"></div>
                    {{-- Branding column --}}
                    <div class="shrink-0 flex flex-col items-center gap-1.5 max-w-[72px]">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded opacity-80 brightness-0 invert">
                        @else
                            <div class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $brandColor }}"></div>
                        @endif
                        <p class="font-black tracking-[0.15em] text-[6px] uppercase text-center leading-none line-clamp-2" style="color: {{ $accentTextColor }}bf">{{ $companyLabel }}</p>
                        @if($trade)
                            <span class="px-2 h-4 inline-flex items-center justify-center rounded text-[5px] font-black uppercase tracking-wide border whitespace-nowrap leading-none" style="{{ $tagStyle }}">{{ $trade }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Controls and Indicators (Preview Only) --}}
        @if($preview && !$exporting)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-5 right-5 text-[10px] font-bold px-3 py-1.5 rounded-full z-30 border shadow-xl" style="background-color: {{ $brandColor }}; color: {{ $badgeTextCol }}; border-color: rgba(255,255,255,0.2);" x-text="(current + 1) + ' / ' + total"></div>
        @elseif($slideIndex !== null && (int)$slideIndex !== 3 && !$exporting)
            <div class="absolute top-5 right-5 text-[10px] font-bold px-3 py-1.5 rounded-full z-30 border shadow-xl" style="background-color: {{ $brandColor }}; color: {{ $badgeTextCol }}; border-color: rgba(255,255,255,0.2);">
                {{ $slideIndex + 1 }} / {{ $total }}
            </div>
        @endif

    </div>
</div>
