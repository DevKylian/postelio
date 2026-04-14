@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
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
    'aspectRatio' => '4:5',
])

@if($preview)
    @php
        $text = '3 gestes essentiels pour protéger votre toiture avant l\'hiver.';
        $phone = $phone ?? '01 23 45 67 89';
        $email = $email ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade ?? 'Artisan';
        $city = $city ?? 'Ma Ville';
    @endphp
@endif

@php
    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total = 1 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="background-color: {{ $accentColor }};">

    {{-- Ambient glows (Sophisticated) --}}
    <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full blur-[100px] pointer-events-none opacity-30" style="background: radial-gradient(circle, {{ $brandColor }}, transparent 70%)"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full blur-[100px] pointer-events-none opacity-20" style="background: radial-gradient(circle, {{ $brandColor }}, transparent 70%)"></div>

    {{-- Slide 0 --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 p-8 flex flex-col justify-center relative z-10" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        
        {{-- Glass Card for Content --}}
        <div class="backdrop-blur-[12px] bg-white/10 border border-white/20 rounded-[2.5rem] p-10 shadow-3xl overflow-hidden relative">
            {{-- Seasonal Indicator Badge --}}
            <div class="inline-flex items-center gap-2 mb-6">
                <div class="w-2 h-2 rounded-full animate-pulse" style="background-color: {{ $brandColor }}"></div>
                <span class="font-black uppercase tracking-[0.3em] text-[10px] font-montserrat text-white/40">Conseil de saison</span>
            </div>

            @php
                $len = mb_strlen($text);
                $fontSize = 'sm:text-2xl text-xl';
                if ($len > 150) $fontSize = 'sm:text-lg text-base';
                elseif ($len > 80) $fontSize = 'sm:text-xl text-lg';
            @endphp
            <div class="max-h-[180px] overflow-hidden">
                <p class="text-white font-black {{ $fontSize }} leading-tight font-montserrat tracking-tight">{!! nl2br(e($text)) !!}</p>
            </div>

            {{-- Subtle decoration --}}
            <div class="absolute -bottom-4 -right-4 w-24 h-24 opacity-[0.03]" style="color: {{ $brandColor }}">
                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
            </div>
        </div>

        {{-- Footer Branding (Unified Standard) --}}
        @php
            $showBranding = $slideIndex === null || (int)$slideIndex === 0;
            $brandingAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';
        @endphp
        @if($showBranding)
        <div {!! $brandingAlpine !!} class="absolute bottom-6 inset-x-0 mx-auto w-[90%] z-20">
            <div class="backdrop-blur-xl rounded-2xl px-4 py-3.5 shadow-2xl border border-white/10 flex flex-col items-center"
                 style="background-color: {{ $accentColor }}d9;">
                <div class="flex items-center gap-2 mb-2.5">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-3 w-3 object-contain rounded opacity-80 shrink-0 {{ $accentTextColor === '#FFFFFF' ? 'brightness-0 invert' : '' }}">
                    @endif
                    @php
                        $wrappedLabel = mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel;
                    @endphp
                    <p class="font-black tracking-[0.18em] text-[7px] uppercase whitespace-normal" style="color: {{ $accentTextColor }}bf">{!! $wrappedLabel !!}</p>
                </div>
                
                <div class="flex items-center gap-1.5 overflow-hidden">
                    @php
                        $hex = str_replace('#', '', $brandColor);
                        if(strlen($hex) == 3) {
                            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
                        } else {
                            $r = hexdec(substr($hex,0,2));
                            $g = hexdec(substr($hex,2,2));
                            $b = hexdec(substr($hex,4,2));
                        }
                        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
                        $isDarkBrand = $yiq < 120;

                        if ($isDarkBrand) {
                            $tagBg = 'rgba(255,255,255,0.1)';
                            $tagBorder = 'rgba(255,255,255,0.2)';
                            $tagText = 'rgba(255,255,255,0.9)';
                        } else {
                            $tagBg = $brandColor . '25';
                            $tagBorder = $brandColor . '40';
                            $tagText = $brandColor;
                        }
                        $tagStyle = "background-color: $tagBg; border-color: $tagBorder; color: $tagText;";
                    @endphp
                    @if($trade)
                        <span class="px-2 py-0.5 rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap" style="{{ $tagStyle }}">{{ $trade }}</span>
                    @endif
                    @if($city)
                        <span class="px-2 py-0.5 rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap" style="{{ $tagStyle }}">{{ $city }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Slide 1: Contact --}}
    @php
        $isSlide1 = (int)$slideIndex === 1;
        $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
        $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    @if($hasContactInfo)
    <div class="absolute inset-0" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
          <x-post-templates.contact-slide
            :brand-color="$brandColor"
            :company-label="$companyLabel"
            :phone="$phone"
            :email="$email"
            :website="$website"
            :trade="$trade"
            :city="$city"
            :visual-style="'transparent'"
            :brand-text-color="$brandTextColor"
            :accent-color="$accentColor"
            :accent-text-color="$accentTextColor"
        />
    </div>
    @endif

    {{-- Navigation (Preview Only) --}}
    @if($preview && !$exporting)
        <div class="absolute inset-y-0 inset-x-4 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/20 border border-white/10 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/20 border border-white/10 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
