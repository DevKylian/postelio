@props([
    'brandColor'     => '#F59E0B',
    'companyLabel'   => 'Votre Société',
    'title'          => 'Vos questions, nos réponses',
    'question'       => '',
    'answer'         => '',
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
        $question = 'Quels sont les délais moyens pour une rénovation complète de toiture ?';
        $answer   = 'Il faut compter entre 1 et 2 semaines selon la surface et la complexité de la charpente. On vous accompagne de A à Z pour réaliser votre projet dans les meilleurs délais.';
        $phone    = $phone   ?? '01 23 45 67 89';
        $email    = $email   ?? 'contact@votre-artisan.fr';
        $website  = $website ?? 'www.votre-artisan.fr';
        $trade    = $trade   ?? 'Artisan';
        $city     = $city    ?? 'Ma Ville';
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

    // Tag style (in dark footer pill)
    if ($isDarkBrand) {
        $tagBg     = 'rgba(255,255,255,0.12)';
        $tagBorder = 'rgba(255,255,255,0.2)';
        $tagText   = 'rgba(255,255,255,0.9)';
    } else {
        $tagBg     = $brandColor . '25';
        $tagBorder = $brandColor . '40';
        $tagText   = $brandColor;
    }
    $tagStyle = "background-color: $tagBg; border-color: $tagBorder; color: $tagText;";

    // Chip style for elements on LIGHT backgrounds
    // Light brand → outlined (border + tint bg + dark text) to preserve hue without darkening
    if ($yiq > 195) {
        $chipStyle    = "background-color: {$brandColor}12; border: 1.5px solid {$brandColor}70; color: #374151;";
        $chipIconColor = $brandColor; // icon keeps brand color (visible on tinted bg)
    } else {
        $chipStyle    = "background-color: {$brandColor}; color: {$badgeTextCol}; border: none;";
        $chipIconColor = $badgeTextCol;
    }

    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total      = 2 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';

    // Font sizes
    $qLen      = mb_strlen($question ?? '');
    $qFont     = $qLen > 120 ? 'text-lg' : ($qLen > 70 ? 'text-xl' : 'text-2xl');
    $aLen      = mb_strlen($answer ?? '');
    $aFont     = $aLen > 180 ? 'text-sm' : ($aLen > 100 ? 'text-base' : 'text-lg');
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? '4:5') === '4:5' ? '4/5' : '1/1' }}] relative overflow-hidden flex flex-col {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="background-color: #F8FAFC;">



    {{-- ── MAIN CONTENT AREA ── --}}
    <div class="flex-1 flex flex-col justify-center px-7 pb-20 relative overflow-hidden">

        {{-- Decorative big "?" watermark --}}
        <div class="absolute -bottom-6 -right-4 select-none pointer-events-none font-black" style="font-size: 200px; line-height: 1; color: {{ $brandColor }}; opacity: 0.06; font-family: 'Montserrat', sans-serif;">?</div>

        {{-- ── SLIDE 0 : QUESTION ── --}}
        @php
            $isSlide0    = (int)$slideIndex === 0 || $slideIndex === null;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="relative z-10">

            {{-- FAQ Chip --}}
            <div class="inline-flex items-center gap-2 mb-5 px-3 h-7 rounded-full" style="{{ $chipStyle }}">
                <span class="text-lg font-black leading-none" style="color: {{ $chipIconColor }}">?</span>
                <span class="text-[9px] font-black uppercase tracking-[0.25em] leading-none">Question fréquente</span>
            </div>

            {{-- Separator --}}
            <div class="w-10 h-1 rounded-full mb-5" style="background-color: {{ $brandColor }}"></div>

            {{-- Question Text --}}
            <p class="font-black {{ $qFont }} leading-tight tracking-tight text-slate-900 font-montserrat">
                {!! nl2br(e($question)) !!}
            </p>

            {{-- Subtle hint --}}
            <p class="mt-5 text-[9px] font-bold uppercase tracking-[0.2em] text-slate-400">
                → Glissez pour voir notre réponse
            </p>
        </div>

        {{-- ── SLIDE 1 : RÉPONSE ── --}}
        @php
            $isSlide1     = (int)$slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style  = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}" class="relative z-10">

            {{-- Answer Chip --}}
            <div class="inline-flex items-center gap-2 mb-5 px-3 h-7 rounded-full" style="{{ $chipStyle }}">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: {{ $chipIconColor }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                <span class="text-[9px] font-black uppercase tracking-[0.25em] leading-none">Notre réponse</span>
            </div>

            {{-- Separator --}}
            <div class="w-10 h-1 rounded-full mb-5" style="background-color: {{ $brandColor }}"></div>

            {{-- Answer Card --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden">
                {{-- Left accent bar --}}
                <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-2xl" style="background-color: {{ $brandColor }}"></div>
                <p class="font-bold {{ $aFont }} leading-relaxed text-slate-700 font-montserrat pl-3">
                    {!! nl2br(e($answer)) !!}
                </p>
            </div>
        </div>
    </div>

    {{-- ── SLIDE CONTACT — couvre tout le template ── --}}
    @php
        $isSlide2     = (int)$slideIndex === 2;
        $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
        $slide2Style  = (int)$slideIndex !== 2 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    @if($hasContactInfo)
    <div class="absolute inset-0 z-40" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
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
        $footerAlpine = $slideIndex === null ? "x-show=\"current < 2\"" : '';
        $showFooter   = $hasContactInfo ? ($slideIndex === null || (int)$slideIndex < 2) : true;
    @endphp
    @if($showFooter && !$exporting)
    <div class="absolute bottom-5 inset-x-0 mx-auto w-[85%] z-20" {!! $footerAlpine !!}>
        <div class="backdrop-blur-xl rounded-2xl px-4 py-2.5 shadow-xl border border-white/10 flex items-center justify-between gap-3"
             style="background-color: {{ $accentColor }}cc;">
            <div class="flex items-center gap-2 min-w-0">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-3 w-3 object-contain rounded opacity-80 shrink-0 {{ $accentTextColor === '#FFFFFF' ? 'brightness-0 invert' : '' }}">
                @endif
                @php
                    $wrappedLabel = mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel;
                @endphp
                <p class="font-black tracking-[0.18em] text-[7px] uppercase whitespace-normal leading-tight" style="color: {{ $accentTextColor }}bf">{!! $wrappedLabel !!}</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                @if($trade)
                    <span class="px-2 h-4 inline-flex items-center justify-center rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap leading-none" style="{{ $tagStyle }}">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-2 h-4 inline-flex items-center justify-center rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap leading-none" style="{{ $tagStyle }}">{{ $city }}</span>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ── NAVIGATION (Preview) ── --}}
    @if($preview && !$exporting)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-50 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-slate-900/30 backdrop-blur-md text-white flex items-center justify-center hover:bg-slate-900/50 border border-white/20 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-slate-900/30 backdrop-blur-md text-white flex items-center justify-center hover:bg-slate-900/50 border border-white/20 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
        <div class="absolute top-[72px] right-5 z-30 px-2.5 py-1 rounded-full text-[9px] font-black" style="{{ $chipStyle }}" x-text="(current + 1) + ' / ' + total"></div>
    @endif
</div>
