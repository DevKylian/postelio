@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'text' => '',
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#D1D5DB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    {{-- Slide 0: Conseil --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10 w-full">
            <div class="bg-white border-2 border-gray-900 p-10 rounded-2xl shadow-[15px_15px_0px_0px_{{ $brandColor }}33] w-full max-w-[420px] relative">
                <div class="absolute -top-6 left-10 bg-gray-900 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-xl">DOSSIER TECHNIQUE</div>
                
                @php
                    $dlen = mb_strlen($text);
                    $fontSize = 'text-sm';
                    if ($dlen > 250) $fontSize = 'text-xs';
                @endphp
                
                <div class="max-h-[160px] overflow-hidden">
                    <p class="text-gray-900/60 {{ $fontSize }} font-bold font-montserrat leading-relaxed uppercase bg-gray-50 p-6 rounded-xl border border-gray-200">
                        {{ $text }}
                    </p>
                </div>
            </div>
        </div>

        <div class="h-20 px-10 bg-gray-900 flex flex-col justify-center gap-1 z-20">
             <div class="flex items-center justify-between">
                  <span class="text-white text-[10px] font-black font-montserrat uppercase tracking-widest truncate max-w-[250px]">{{ $companyLabel }}</span>
                  <div class="w-12 h-1 rounded-full opacity-30 shrink-0" style="background-color: {{ $brandColor }}"></div>
             </div>
             <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/50 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap font-mono-technical">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/50 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap font-mono-technical">{{ $city }}</span>
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
            visualStyle="beton"
        />
    </div>
    @endif

    @if($preview)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 bg-white/20 backdrop-blur-md text-white rounded-full flex items-center justify-center hover:bg-white/40 pointer-events-auto border border-white/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 bg-white/20 backdrop-blur-md text-white rounded-full flex items-center justify-center hover:bg-white/40 pointer-events-auto border border-white/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
