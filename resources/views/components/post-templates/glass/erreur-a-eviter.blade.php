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
        $text = $text ?: 'L\'erreur fatale : négliger l\'étanchéité sous le carrelage de votre douche.';
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-red-500/20 group-hover:-translate-y-2' : '' }}">
    
    {{-- Slide 0: Erreur --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        {{-- Danger glow --}}
        <div class="absolute -top-24 -left-24 w-64 h-64 rounded-full blur-[100px] opacity-20 bg-red-600"></div>

        <div class="flex-1 flex flex-col items-center justify-center p-8 relative z-10">
            
            {{-- Warning Icon Glass --}}
            <div class="w-14 h-14 rounded-2xl backdrop-blur-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center shadow-2xl mb-4 animate-pulse">
                 <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                 </svg>
            </div>

            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl w-full max-w-[320px] text-center border-t-4 border-t-red-500">
                <span class="text-[10px] font-black text-red-500/60 uppercase tracking-[0.3em] block mb-2 font-montserrat">Attention : Erreur courante</span>
                @php
                    $len = mb_strlen($text);
                    $fontSize = 'text-base';
                    if ($len > 150) $fontSize = 'text-[10px]';
                    elseif ($len > 80) $fontSize = 'text-xs';
                    elseif ($len > 40) $fontSize = 'text-sm';
                @endphp
                <div class="max-h-[140px] overflow-hidden">
                    <h3 class="text-white {{ $fontSize }} font-bold leading-tight">
                        {{ $text }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="px-8 py-3 backdrop-blur-md bg-white/5 border-t border-white/10 flex flex-col gap-1 items-start">
            <span class="text-white/50 text-[8px] font-black uppercase tracking-[0.3em] font-montserrat truncate pr-4">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
            <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-md bg-white/5 text-white/30 text-[5px] font-bold uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-md bg-white/5 text-white/30 text-[5px] font-bold uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $city }}</span>
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
