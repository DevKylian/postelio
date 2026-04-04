@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => 'La passion du métier se lit dans chaque geste.',
    'imageUrl' => null,
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
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = $text ?? 'La passion du métier se lit dans chaque geste.';
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? '4:5') === '4:5' ? '4/5' : '1/1' }}] bg-gray-700 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
    
    {{-- Slide 0: Main Content --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        {{-- Full-bleed photo --}}
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center text-gray-400 select-none pointer-events-none">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Votre photo</p>
                </div>
            </div>
        @endif

        {{-- Dark gradient on lower half --}}
        <div class="absolute inset-x-0 bottom-0 h-[50%] bg-gradient-to-t from-black/80 via-black/40 to-transparent z-[1]"></div>

        {{-- Company label — bottom bar --}}
        <div class="absolute inset-x-0 bottom-0 py-2 px-5 bg-black/40 backdrop-blur-sm flex flex-col items-center z-10 border-t-2" style="border-top-color: {{ $brandColor }}">
            <div class="w-full flex items-center justify-center gap-2 mb-1.5">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded">
                @endif
                <p class="text-white/90 font-bold text-[9px] tracking-widest font-montserrat truncate">{{ $companyLabel }}</p>
            </div>
            <div class="flex items-center gap-2 mt-1">
                @php
                    $isWhiteTag = true; // Modern white tag style
                    $tagTextCol = $brandColor;
                    // Safety check for contrast on white background
                    $hexTag = str_replace('#', '', $brandColor);
                    if (strlen($hexTag) == 3) $hexTag = $hexTag[0].$hexTag[0].$hexTag[1].$hexTag[1].$hexTag[2].$hexTag[2];
                    $rTag = hexdec(substr($hexTag, 0, 2));
                    $gTag = hexdec(substr($hexTag, 2, 2));
                    $bTag = hexdec(substr($hexTag, 4, 2));
                    $yiqTag = (($rTag * 299) + ($gTag * 587) + ($bTag * 114)) / 1000;
                    if ($yiqTag > 235) $tagTextCol = '#1E293B'; // Fallback to dark if brand color is too light
                @endphp

                @if($trade)
                    <span class="px-2 py-0.5 rounded-full bg-white text-[7px] font-black uppercase tracking-[0.15em] shadow-sm ring-1 ring-black/5" style="color: {{ $tagTextCol }}">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-2 py-0.5 rounded-full bg-white text-[7px] font-black uppercase tracking-[0.15em] shadow-sm ring-1 ring-black/5" style="color: {{ $tagTextCol }}">{{ $city }}</span>
                @endif
            </div>
        </div>

        {{-- Text overlay — centered in lower third, above company label --}}
        @if($text)
            @php
                $len = mb_strlen($text);
                $fontSize = 'text-sm';
                if ($len > 25) $fontSize = 'text-[10px]';
                elseif ($len > 18) $fontSize = 'text-xs';
            @endphp
            <div class="absolute inset-x-6 bottom-[42px] top-[60%] flex items-center justify-center z-10 overflow-hidden">
                <div class="bg-black/50 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/10">
                    <h3 class="text-white font-black {{ $fontSize }} leading-snug font-montserrat text-center" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $text }}</h3>
                </div>
            </div>
        @endif
    </div>

    {{-- Slide 1: Contact --}}
    @php
        $isSlide1 = (int)$slideIndex === 1;
        $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
        $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    @if($hasContactInfo)
    <div class="absolute inset-0 bg-gray-900" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
         <x-post-templates.contact-slide 
            :brand-color="$brandColor" 
            :company-label="$companyLabel"
            :phone="$phone"
            :email="$email"
            :website="$website"
            :trade="$trade"
            :city="$city"
            :brand-text-color="$brandTextColor"
            visualStyle="glass"
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
