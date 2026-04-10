@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'customerName' => 'Client',
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
])

@if($preview)
    @php
        $customerName = ($customerName === 'Client' || empty($customerName)) ? 'Jean-Pierre D.' : $customerName;
        $text = empty($text) ? '"Une expérience parfaite de A à Z. L\'équipe a su transformer notre vision en réalité avec un professionnalisme exemplaire. Les délais ont été respectés et les finitions sont impeccables. Nous recommandons vivement pour vos projets !"' : $text;
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? '4:5') === '4:5' ? '4/5' : '1/1' }}] relative overflow-hidden flex flex-col justify-end {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">

    {{-- Slide 0 --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center" {!! $slide0Alpine !!} style="{{ $slide0Style }}; background-color: {{ $brandColor }}">

        @php
            $hex = str_replace('#', '', $brandColor);
            if (strlen($hex) == 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $r = hexdec(substr($hex, 0, 2) ?: '00');
            $g = hexdec(substr($hex, 2, 2) ?: '00');
            $b = hexdec(substr($hex, 4, 2) ?: '00');
            $isWarm = ($r > 140 && $b < 140);
            if ($isWarm) {
                $starColor = '#FFFFFF';
            } else {
                $starColor = ($brandTextColor === '#1E293B') ? '#D97706' : '#FDE047';
            }

            // Initials logic
            $words = explode(' ', $customerName);
            $initials = '';
            foreach($words as $w) {
                $initials .= mb_substr($w, 0, 1);
                if(mb_strlen($initials) >= 2) break;
            }
            $initials = mb_strtoupper($initials);
        @endphp

        {{-- Dot Pattern Overlay --}}
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none" 
             style="background-image: radial-gradient({{ $brandTextColor }} 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>

        {{-- Ambient glows --}}
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full blur-3xl pointer-events-none" style="background: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.12)' : 'rgba(255,255,255,0.2)' }}"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 rounded-full blur-3xl pointer-events-none" style="background: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.08)' : 'rgba(255,255,255,0.15)' }}"></div>

        {{-- Verified Badge --}}
        <div class="mb-4 relative z-10 h-6 inline-flex items-center gap-1.5 px-3 rounded-full border shadow-sm backdrop-blur-sm transition-all" 
             style="background: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.1)' }}; border-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}">
            <svg class="w-2.5 h-2.5" fill="none" stroke="{{ $brandTextColor }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            <span class="text-[8px] font-black uppercase tracking-[0.2em] leading-none mb-[1px]" style="color: {{ $brandTextColor }}">Témoignage Vérifié</span>
        </div>

        {{-- Stars --}}
        <div class="flex space-x-1.5 mb-8 relative z-10">
            @for ($i = 0; $i < 5; $i++)
                <svg class="w-6 h-6 drop-shadow-sm" fill="{{ $starColor }}" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            @endfor
        </div>

        @php
            $len = mb_strlen($text);
            $fontSize = 'text-2xl';
            if ($len > 200) $fontSize = 'text-base';
            elseif ($len > 120) $fontSize = 'text-lg';
            elseif ($len > 60) $fontSize = 'text-xl';
        @endphp
        <div class="flex-1 flex items-center justify-center relative z-10 px-10 w-full min-h-0">
            {{-- Wrapper tightly bound to text width for better horizontal anchoring --}}
            <div class="relative inline-block max-w-full">
                {{-- Ambient Quotes --}}
                <div class="absolute top-0 left-0 -translate-x-6 -translate-y-8 opacity-[0.08] select-none pointer-events-none z-0" 
                     style="font-size: 110px; font-family: serif; color: {{ $brandTextColor }}; font-weight: 900; line-height: 1;">“</div>
                
                <p class="font-black tracking-tight {{ $fontSize }} leading-tight font-montserrat relative z-10" style="color: {{ $brandTextColor }}">
                    {!! nl2br(e(str_replace(['"', '«', '»'], '', $text))) !!}
                </p>

                <div class="absolute bottom-0 right-0 translate-x-3 translate-y-16 opacity-[0.08] select-none pointer-events-none z-0" 
                     style="font-size: 110px; font-family: serif; color: {{ $brandTextColor }}; font-weight: 900; line-height: 1;">”</div>
            </div>
        </div>

        {{-- Author & footer --}}
        <div class="mt-8 pt-6 w-full relative z-10 flex flex-col items-center">
                @if($customerName && $customerName !== 'Client')
                <div class="flex items-center gap-4 px-6 py-3 rounded-2xl border shadow-lg backdrop-blur-md" 
                     style="background: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.02)' : 'rgba(255,255,255,0.05)' }}; border-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.1)' }}">
                    
                    {{-- Initials Avatar --}}
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 shadow-inner" 
                         style="background: {{ $brandTextColor }}; color: {{ $brandColor }}">
                        <span class="text-xs font-black tracking-tighter leading-none mb-[1px]">{{ $initials }}</span>
                    </div>

                    <div class="flex flex-col items-start min-w-0">
                        <div class="flex items-center gap-1.5 mb-0.5 max-w-full">
                            <span class="font-black text-xs uppercase tracking-wider truncate" style="color: {{ $brandTextColor }}">{{ $customerName }}</span>
                            <div class="flex-shrink-0 mt-[1px]">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="{{ $brandTextColor }}"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Logo" class="h-3 w-3 object-contain rounded opacity-90 brightness-0 {{ $brandTextColor === '#FFFFFF' ? 'invert' : '' }}">
                            @endif
                            <p class="font-black tracking-[0.2em] text-[7px] uppercase truncate leading-none" style="color: {{ $brandTextColor }}">{{ $companyLabel }}</p>
                        </div>
                    </div>
                </div>
                @endif

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
                        $tagBg = 'rgba(0,0,0,0.05)';
                        $tagBorder = 'rgba(0,0,0,0.1)';
                        // Use a dark color for contrast on light backgrounds
                        $tagText = 'rgba(30, 41, 59, 0.8)';
                    }
                    $tagStyle = "background-color: $tagBg; border-color: $tagBorder; color: $tagText;";
                @endphp

                <div class="flex items-center justify-center gap-2 mt-4">
                    @if($trade)
                        <span class="px-2 h-4 inline-flex items-center justify-center rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap" style="{{ $tagStyle }}">{{ $trade }}</span>
                    @endif
                    @if($city)
                        <span class="px-2 h-4 inline-flex items-center justify-center rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap" style="{{ $tagStyle }}">{{ $city }}</span>
                    @endif
                </div>
        </div>
    </div>

    {{-- Slide 1: Contact --}}
    @php
        $isSlide1 = (int)$slideIndex === 1;
        $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
        $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    @if($hasContactInfo)
    <div class="absolute inset-0 bg-white" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
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

    @if($preview && !$exporting)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-20">
            <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
