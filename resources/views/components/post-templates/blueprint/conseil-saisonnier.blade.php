@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'text' => '',
    'logoUrl' => null,
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
    
    $showFooter = $hasContactInfo ? ((int)$slideIndex === 0) : true;
    $footerAlpine = $slideIndex === null ? ($hasContactInfo ? 'x-show="current === 0"' : '') : '';
@endphp

<div {!! $alpineData !!} class="w-full {{ ($aspectRatio ?? '4:5') === '4:5' ? 'aspect-[4/5]' : 'aspect-square' }} bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 30px 30px;">
    </div>

    {{-- Slide 0: Conseil --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10">
            
            <div class="w-20 h-20 border-2 border-[#0047AB] bg-white flex items-center justify-center mb-10 shadow-xl">
                 <svg class="w-10 h-10 text-[#0047AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                 </svg>
            </div>

            <div class="bg-white p-10 shadow-2xl w-full max-w-[360px] relative border-l-4 border-l-[#003380]">
                <h3 class="text-[#0047AB] text-lg font-black font-mono-technical leading-tight mb-2 uppercase tracking-tighter">CONSEIL D'EXPERT</h3>
                @php
                    $len = mb_strlen($text);
                    $fontSize = 'sm:text-sm text-xs';
                    if ($len > 250) $fontSize = 'sm:text-[10px] text-[8px]';
                    elseif ($len > 120) $fontSize = 'sm:text-xs text-[10px]';
                @endphp
                <div class="max-h-[160px] overflow-hidden">
                    <p class="text-[#0047AB] {{ $fontSize }} font-black font-mono-technical leading-relaxed uppercase">
                        {{ $text }}
                    </p>
                </div>
            </div>
        </div>

        <div {!! $footerAlpine !!} class="py-2.5 px-10 bg-white border-t-2 border-[#0047AB] flex flex-col gap-1 relative z-20 min-h-14">
            <div class="flex items-center justify-between text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest">
                <span class="truncate max-w-[200px]">{{ $companyLabel }}</span>
                <div class="flex gap-2">
                     <div class="w-2 h-2 rounded-full" style="background-color: {{ $brandColor }}"></div>
                     <div class="w-2 h-2 rounded-full bg-blue-100"></div>
                </div>
            </div>
            <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/50 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/50 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $city }}</span>
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
    <div class="absolute inset-0 p-8" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
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

    @if($preview)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20 pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20 pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>

