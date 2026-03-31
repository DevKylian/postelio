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
])

@if($preview)
    @php
        $text = 'L\'erreur fatale : négliger l\'étanchéité sous le carrelage de votre douche.';
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

<div {!! $alpineData !!} class="w-full aspect-square relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="background-color: #111827; border-top: 8px solid {{ $brandColor }};">
    
    {{-- Slide 0 --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        <div class="absolute inset-0 overflow-hidden pointer-events-none" style="opacity:0.045">
            <div class="absolute bg-white w-full h-3 top-1/2 left-0" style="transform: rotate(45deg) scaleX(1.6) translateY(-50%)"></div>
            <div class="absolute bg-white w-full h-3 top-1/2 left-0" style="transform: rotate(-45deg) scaleX(1.6) translateY(-50%)"></div>
        </div>
        <div class="absolute inset-0 p-10 flex flex-col justify-center">
            <span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span>
            <h3 class="font-black uppercase tracking-widest text-sm mb-5 font-montserrat" style="color: {{ $brandColor }}">Erreur à Éviter</h3>
            @php
                $len = mb_strlen($text);
                $fontSize = 'text-2xl';
                if ($len > 150) $fontSize = 'text-base';
                elseif ($len > 80) $fontSize = 'text-lg';
                elseif ($len > 40) $fontSize = 'text-xl';
            @endphp
            <div class="max-h-[160px] overflow-hidden">
                <p class="text-white font-black uppercase tracking-tight {{ $fontSize }} leading-snug font-montserrat">{!! nl2br(e($text)) !!}</p>
            </div>
        </div>
        <div class="absolute bottom-0 inset-x-0 border-t px-10 py-3 flex flex-col gap-1" style="border-color: rgba(255,255,255,0.1)">
            <div class="flex items-center gap-2">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded">
                @endif
                <p class="font-bold text-xs uppercase tracking-widest font-montserrat" style="color: {{ $brandColor }}">{{ $companyLabel }}</p>
            </div>
            <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded bg-white/5 text-white/30 text-[7px] font-bold uppercase tracking-wider border border-white/5">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded bg-white/5 text-white/30 text-[7px] font-bold uppercase tracking-wider border border-white/5">{{ $city }}</span>
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
