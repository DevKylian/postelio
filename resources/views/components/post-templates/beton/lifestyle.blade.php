@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'imageUrl' => null,
    'text' => '',
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === '') ? 'La passion du métier se lit dans chaque geste.' : $text;
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#D1D5DB] relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Full Image Background (Slide 0) --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        <div class="absolute inset-0">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-cover">
            @else
                <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-gray-900/20 to-transparent"></div>
        </div>

        {{-- Company badge — top right --}}
        <div class="absolute top-6 right-6 z-10 flex flex-col items-end gap-1.5">
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-3 py-1 flex items-center gap-2">
                <span class="text-white text-[9px] font-black uppercase tracking-widest">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
            </div>
            <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-2 py-0.5 rounded-full bg-white/5 backdrop-blur-sm text-white/50 text-[5px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-2 py-0.5 rounded-full bg-white/5 backdrop-blur-sm text-white/50 text-[5px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap">{{ $city }}</span>
                @endif
            </div>
        </div>

        {{-- Text — centered in lower third of image --}}
        @php
            $len = mb_strlen($text);
            $fontSize = 'sm:text-2xl text-xl';
            if ($len > 25) $fontSize = 'sm:text-lg text-base';
            elseif ($len > 18) $fontSize = 'sm:text-xl text-lg';
        @endphp
        <div class="absolute inset-x-8 bottom-20 top-[55%] flex items-end z-10 overflow-hidden text-center">
            <div class="flex flex-col w-full items-center">
                <div class="w-10 h-1 mb-3" style="background-color: {{ $brandColor }}"></div>
                <span class="text-white {{ $fontSize }} font-black font-montserrat uppercase leading-tight tracking-tighter block">
                    {{ $text }}
                </span>
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
