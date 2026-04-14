@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'FOCUS QUALITÉ',
    'logoUrl' => null,
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'FOCUS QUALITÉ') ? 'Le choix des meilleurs matériaux pour une durabilité garantie.' : $text;
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

<div {!! $alpineData !!} class="w-full {{ ($aspectRatio ?? '4:5') === '4:5' ? 'aspect-[4/5]' : 'aspect-square' }} bg-neutral-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    {{-- Slide 0: Content --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        {{-- Background image with focus --}}
        <div class="absolute inset-0 bg-neutral-800">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover blur-[2px]">
            @else
                <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="absolute inset-0 w-full h-full object-cover opacity-60 grayscale blur-[2px]">
            @endif
        </div>

        {{-- Main Image Background --}}
        <div class="relative flex-1 flex items-center justify-center p-12">
            <div class="relative w-full h-full rounded-2xl border border-white/10 shadow-2xl overflow-hidden group/zoom">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-contain transition-transform duration-700 group-hover/zoom:scale-110">
                @else
                    <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover/zoom:scale-110">
                @endif
                <div class="absolute inset-0 bg-gradient-to-tr from-midnight/40 to-transparent"></div>
            </div>
        </div>

        {{-- Info panel --}}
        <div class="h-24 px-8 backdrop-blur-2xl bg-white/5 border-t border-white/10 flex items-center justify-between">
            <div class="flex flex-col gap-1 max-w-[70%]">
                @php
                    $cLen = mb_strlen($companyLabel);
                    $cFontSize = 'text-[9px]';
                    if ($cLen > 30) $cFontSize = 'text-[7px]';
                    elseif ($cLen > 20) $cFontSize = 'text-[8px]';

                    $tLen = mb_strlen($text);
                    $tFontSize = 'text-base';
                    if ($tLen > 40) $tFontSize = 'text-xs';
                    elseif ($tLen > 25) $tFontSize = 'text-sm';
                @endphp
                <div class="flex items-center gap-2 mb-1">
                    <span class="{{ $cFontSize }} font-black text-white/50 uppercase tracking-[0.4em] font-montserrat truncate">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
                    <div class="flex items-center gap-1 overflow-hidden">
                        @if($trade)
                            <span class="px-1 py-0.5 rounded-md bg-white/5 text-white/30 text-[5px] font-bold uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $trade }}</span>
                        @endif
                        @if($city)
                            <span class="px-1 py-0.5 rounded-md bg-white/5 text-white/30 text-[5px] font-bold uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $city }}</span>
                        @endif
                    </div>
                </div>
                <div class="max-h-[40px] overflow-hidden">
                    <span class="text-white {{ $tFontSize }} font-bold tracking-tight leading-tight uppercase">{{ $text }}</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
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
    <div class="absolute inset-0" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
        <x-post-templates.contact-slide 
            :brand-color="$brandColor" 
            :company-label="$companyLabel"
            :phone="$phone"
            :email="$email"
            :website="$website"
            :trade="$trade"
            :city="$city"
            visualStyle="glass"
        />
    </div>
    @endif

    @if($preview)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-2xl backdrop-blur-xl bg-white/10 text-white flex items-center justify-center hover:bg-white/20 border border-white/10 shadow-2xl pointer-events-auto transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-2xl backdrop-blur-xl bg-white/10 text-white flex items-center justify-center hover:bg-white/20 border border-white/10 shadow-2xl pointer-events-auto transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>

