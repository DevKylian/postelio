@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
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
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'SUR LE TERRAIN') ? 'La passion du métier se lit dans chaque geste.' : $text;
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

<div {!! $alpineData !!} class="w-full aspect-square bg-gray-900 relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">

    {{-- Slide 0: Main --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        {{-- Photo Background --}}
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 flex items-center justify-center bg-gray-800">
                <svg class="w-16 h-16 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
        @endif

        {{-- Glass card — centered vertically in lower half --}}
        <div class="absolute inset-x-10 bottom-[40px] h-[90px] backdrop-blur-xl bg-slate-900/40 border border-white/20 rounded-2xl shadow-2xl">

            @php
                $displayText = $text ?: 'LIFESTYLE';
                $len = mb_strlen($displayText);
                $fontSize = 'text-sm';
                if ($len > 25) $fontSize = 'text-[10px]';
                elseif ($len > 18) $fontSize = 'text-xs';
            @endphp

            {{-- Text + company --}}
            <div class="absolute inset-x-4 top-[10px] bottom-[10px] flex flex-col items-center justify-center text-center overflow-hidden">
                <h3 class="text-white {{ $fontSize }} font-black uppercase tracking-widest font-montserrat leading-tight max-w-[220px] mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $displayText }}</h3>
                <div class="flex flex-col items-center gap-1">
                    <p class="text-white/60 text-[9px] font-bold uppercase tracking-[0.2em] truncate max-w-[180px]">{{ $companyLabel }}</p>
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
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-2xl backdrop-blur-xl bg-white/10 text-white flex items-center justify-center hover:bg-white/20 border border-white/10 shadow-2xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-2xl backdrop-blur-xl bg-white/10 text-white flex items-center justify-center hover:bg-white/20 border border-white/10 shadow-2xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
