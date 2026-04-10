@props([
    'brandColor'     => '#F59E0B',
    'companyLabel'   => 'Votre Société',
    'text'           => '',
    'logoUrl'        => null,
    'phone'          => null,
    'email'          => null,
    'website'        => null,
    'trade'          => null,
    'city'           => null,
    'slideIndex'     => null,
    'preview'        => false,
    'brandTextColor' => '#FFFFFF',
    'accentColor'    => '#0F172A',
    'accentTextColor' => '#FFFFFF',
    'exporting'      => false,
])

@if($preview)
    @php
        $text  = 'L\'erreur fatale : négliger l\'étanchéité sous le carrelage de votre douche.';
        $phone = $phone   ?? '01 23 45 67 89';
        $email = $email   ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade   ?? 'Artisan';
        $city  = $city    ?? 'Ma Ville';
    @endphp
@endif

@php
    // Contrast calc
    $hex = str_replace('#', '', $brandColor);
    if (strlen($hex) == 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    $r   = hexdec(substr($hex, 0, 2));
    $g   = hexdec(substr($hex, 2, 2));
    $b   = hexdec(substr($hex, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    $badgeTextCol = ($yiq >= 186) ? '#1E293B' : '#FFFFFF';
    $isDarkBrand  = $yiq < 120;

    // Tag style
    if ($isDarkBrand) {
        $tagBg = 'rgba(255,255,255,0.12)'; $tagBorder = 'rgba(255,255,255,0.2)'; $tagText = 'rgba(255,255,255,0.9)';
    } else {
        $tagBg = $brandColor . '25'; $tagBorder = $brandColor . '40'; $tagText = $brandColor;
    }
    $tagStyle = "background-color: $tagBg; border-color: $tagBorder; color: $tagText;";

    // Chip style for elements on LIGHT backgrounds
    if ($yiq > 195) {
        $chipStyle     = "background-color: {$brandColor}12; border: 1.5px solid {$brandColor}70; color: #374151;";
        $chipIconColor = $brandColor;
    } else {
        $chipStyle     = "background-color: {$brandColor}; color: {$badgeTextCol}; border: none;";
        $chipIconColor = $badgeTextCol;
    }

    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total      = 1 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';

    $len      = mb_strlen($text ?? '');
    $fontSize = $len > 150 ? 'text-lg' : ($len > 80 ? 'text-xl' : 'text-2xl');
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? '4:5') === '4:5' ? '4/5' : '1/1' }}] relative overflow-hidden flex flex-col {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="background-color: #F8FAFC;">



    {{-- ── MAIN CONTENT AREA ── --}}
    <div class="flex-1 flex flex-col justify-center px-7 relative overflow-hidden">

        {{-- Decorative warning watermark --}}
        <svg class="absolute -bottom-10 -right-10 select-none pointer-events-none" style="width: 240px; height: 240px; color: {{ $brandColor }}; opacity: 0.05;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>


        {{-- ── SLIDE 0 : WARNING CONTENT ── --}}
        @php
            $isSlide0     = (int)$slideIndex === 0 || $slideIndex === null;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style  = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="relative z-10 flex flex-col items-start translate-y-[-15px]">

            {{-- Warning chip --}}
            <div class="inline-flex items-center gap-2 mb-5 px-3 py-1.5 rounded-full" style="{{ $chipStyle }}">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" style="color: {{ $chipIconColor }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="text-[9px] font-black uppercase tracking-[0.25em]">Erreur à éviter</span>
            </div>

            {{-- Separator bar --}}
            <div class="w-10 h-1 rounded-full mb-5" style="background-color: {{ $brandColor }}"></div>

            {{-- Main warning card --}}
            <div class="w-full bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                {{-- Left accent bar --}}
                <div class="flex">
                    <div class="w-1.5 shrink-0" style="background-color: {{ $brandColor }}"></div>
                    <div class="p-6">
                        <div class="max-h-[260px] overflow-hidden">
                            <p class="font-black {{ $fontSize }} leading-tight tracking-tight text-slate-900 font-montserrat">
                                {!! nl2br(e($text)) !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── SLIDE CONTACT — couvre tout le template (hors flex) ── --}}
    @php
        $isSlide1     = (int)$slideIndex === 1;
        $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
        $slide1Style  = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    @if($hasContactInfo)
    <div class="absolute inset-0 z-40" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
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

    {{-- ── BOTTOM BRANDING PILL ── --}}
    @php
        $footerAlpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $showFooter   = $hasContactInfo ? ($slideIndex === null || (int)$slideIndex === 0) : true;
    @endphp
    @if($showFooter && !$exporting)
    <div class="absolute bottom-5 inset-x-0 mx-auto w-[85%] z-20" {!! $footerAlpine !!}>
        <div class="backdrop-blur-xl rounded-2xl px-4 py-2.5 shadow-xl border border-white/10 flex items-center justify-between gap-3"
             style="background-color: {{ $accentColor }}cc;">
            <div class="flex items-center gap-2 min-w-0">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-3 w-3 object-contain rounded opacity-80 shrink-0 {{ $accentTextColor === '#FFFFFF' ? 'brightness-0 invert' : '' }}">
                @endif
                <p class="font-black tracking-[0.18em] text-[7px] uppercase truncate leading-none" style="color: {{ $accentTextColor }}bf">{{ $companyLabel }}</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
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

    {{-- ── NAVIGATION (Preview) ── --}}
    @if($preview && !$exporting)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-50 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-slate-900/20 backdrop-blur-md text-slate-700 flex items-center justify-center hover:bg-slate-900/30 border border-slate-200 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-slate-900/20 backdrop-blur-md text-slate-700 flex items-center justify-center hover:bg-slate-900/30 border border-slate-200 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
        <div class="absolute top-[72px] right-5 z-30 px-2.5 py-1 rounded-full text-[9px] font-black border" style="background-color: {{ $brandColor }}20; border-color: {{ $brandColor }}40; color: {{ $brandColor }}" x-text="(current + 1) + ' / ' + total"></div>
    @endif
</div>
