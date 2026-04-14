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
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $text = $text ?: '3 gestes essentiels pour protéger votre toiture avant l\'hiver.';
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

<div {!! $alpineData !!} class="w-full {{ ($aspectRatio ?? '4:5') === '4:5' ? 'aspect-[4/5]' : 'aspect-square' }} bg-gray-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    {{-- Slide 0: Conseil --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        {{-- Background Season Glow --}}
        <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full blur-[120px] opacity-20" style="background-color: {{ $brandColor }}"></div>
        <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full blur-[100px] opacity-10 bg-gray-500"></div>

        <div class="flex-1 flex flex-col items-center justify-center p-8 relative z-10">
            
            {{-- Season Icon Glass --}}
            <div class="w-14 h-14 rounded-2xl backdrop-blur-2xl bg-white/10 border border-white/20 flex items-center justify-center shadow-2xl mb-4 transition-transform duration-500">
                 <svg class="w-7 h-7" style="color: {{ $brandColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                 </svg>
            </div>

            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl w-full max-w-[320px] text-center">
                <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] block mb-2 font-montserrat">Conseil de saison</span>
                @php
                    $len = mb_strlen($text);
                    $fontSize = 'sm:text-base text-sm';
                    if ($len > 150) $fontSize = 'sm:text-[10px] text-[8px]';
                    elseif ($len > 80) $fontSize = 'sm:text-xs text-[10px]';
                    elseif ($len > 40) $fontSize = 'sm:text-sm text-xs';
                @endphp
                <div class="max-h-[160px] overflow-hidden">
                    <p class="text-white {{ $fontSize }} font-bold leading-relaxed">
                        {{ $text }}
                    </p>
                </div>
            </div>
        </div>

        <div class="absolute bottom-6 left-1/2 -ml-28 w-56 py-2 px-3 border border-white/10 rounded-2xl bg-white/5 backdrop-blur-md flex flex-col items-center justify-center gap-1">
            <span class="text-white/50 text-[8px] font-black uppercase tracking-[0.3em] font-montserrat truncate w-full text-center">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
            <div class="flex items-center gap-1 overflow-hidden">
                @if($trade)
                    <span class="px-1 py-0.5 rounded-md bg-white/5 text-white/30 text-[5px] font-bold uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1 py-0.5 rounded-md bg-white/5 text-white/30 text-[5px] font-bold uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $city }}</span>
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

