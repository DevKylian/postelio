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
])

@if($preview)
    @php
        $customerName = 'Jean-Pierre D.';
        $text = '"Une expérience parfaite de A à Z. L\'équipe a su transformer notre vision en réalité avec un professionnalisme exemplaire. Les délais ont été respectés et les finitions sont impeccables. Nous recommandons vivement pour vos projets !"';
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
    <div class="absolute inset-0 flex flex-col items-center justify-center p-10 text-center" {!! $slide0Alpine !!} style="{{ $slide0Style }}; background-color: {{ $brandColor }}">

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
        @endphp

        {{-- Decorative quote mark --}}
        <div class="absolute top-6 left-8 pointer-events-none select-none" style="font-size: 80px; line-height: 1; font-family: Georgia, serif; color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.08)' : 'rgba(255,255,255,0.12)' }}; font-weight: 900">"</div>

        {{-- Ambient glows --}}
        <div class="absolute -top-12 -right-12 w-36 h-36 rounded-full blur-3xl pointer-events-none" style="background: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.08)' : 'rgba(255,255,255,0.15)' }}"></div>
        <div class="absolute -bottom-12 -left-12 w-36 h-36 rounded-full blur-3xl pointer-events-none" style="background: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.06)' : 'rgba(0,0,0,0.12)' }}"></div>

        {{-- Stars --}}
        <div class="flex space-x-1.5 mb-5 relative z-10">
            @for ($i = 0; $i < 5; $i++)
                <svg class="w-6 h-6" fill="{{ $starColor }}" viewBox="0 0 20 20">
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
        <div class="flex-1 flex items-center justify-center max-h-[280px] overflow-hidden relative z-10">
            <p class="font-black tracking-tight {{ $fontSize }} leading-tight font-montserrat" style="color: {{ $brandTextColor }}">
                {!! nl2br(e($text)) !!}
            </p>
        </div>

        {{-- Author & footer --}}
        <div class="mt-5 pt-4 w-full relative z-10" style="border-top: 1px solid {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.12)' : 'rgba(255,255,255,0.2)' }}">
            <div class="flex items-center justify-center gap-2 mb-2">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-5 w-5 object-contain rounded">
                @endif
                <p class="font-bold text-[9px] tracking-widest font-montserrat" style="color: {{ $brandTextColor }}; opacity: 0.7">{{ $companyLabel }} · {{ $customerName }}</p>
            </div>
            <div class="flex items-center justify-center gap-2">
                @php
                    $tagTextCol = $brandColor;
                    $hexTag = str_replace('#', '', $brandColor);
                    if (strlen($hexTag) == 3) $hexTag = $hexTag[0].$hexTag[0].$hexTag[1].$hexTag[1].$hexTag[2].$hexTag[2];
                    $rTag = hexdec(substr($hexTag, 0, 2));
                    $gTag = hexdec(substr($hexTag, 2, 2));
                    $bTag = hexdec(substr($hexTag, 4, 2));
                    $yiqTag = (($rTag * 299) + ($gTag * 587) + ($bTag * 114)) / 1000;
                    if ($yiqTag > 235) $tagTextCol = '#1E293B';
                @endphp

                @if($trade)
                    <span class="px-2 py-0.5 rounded-full bg-white text-[7px] font-black uppercase tracking-[0.15em] shadow-sm ring-1 ring-black/5" style="color: {{ $tagTextCol }}">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-2 py-0.5 rounded-full bg-white text-[7px] font-black uppercase tracking-[0.15em] shadow-sm ring-1 ring-black/5" style="color: {{ $tagTextCol }}">{{ $city }}</span>
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
            :brand-text-color="$brandTextColor"
        />
    </div>
    @endif

    @if($preview)
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
