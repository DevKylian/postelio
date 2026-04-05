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
    'brandTextColor' => '#FFFFFF',
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="background-color: #111827; border-top: 3px solid {{ $brandColor }};">

    {{-- Slide 0 --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0" {!! $slide0Alpine !!} style="{{ $slide0Style }}">

        {{-- Diagonal X pattern overlay --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none" style="opacity:0.035">
            <div class="absolute w-full h-3 top-1/2 left-0" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}; transform: rotate(45deg) scaleX(1.6) translateY(-50%)"></div>
            <div class="absolute w-full h-3 top-1/2 left-0" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}; transform: rotate(-45deg) scaleX(1.6) translateY(-50%)"></div>
        </div>

        {{-- Ambient glows --}}
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full blur-3xl pointer-events-none" style="background: radial-gradient(circle, {{ $brandColor }}2E, transparent 65%)"></div>
        <div class="absolute -bottom-12 -left-12 w-40 h-40 rounded-full blur-3xl pointer-events-none" style="background: radial-gradient(circle, {{ $brandColor }}1A, transparent 70%)"></div>

        <div class="absolute inset-0 p-10 pb-24 flex flex-col justify-center">
            {{-- Warning label badge --}}
            <div class="inline-flex items-center gap-2 mb-5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="{{ $brandColor }}" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <span class="font-black uppercase tracking-[0.2em] text-[10px] font-montserrat" style="color: {{ $brandColor }}">Erreur à Éviter</span>
            </div>

            {{-- Accent line --}}
            <span class="inline-block w-8 h-px mb-5" style="background-color: {{ $brandColor }}; opacity: 0.5"></span>

            @php
                $len = mb_strlen($text);
                $fontSize = 'text-2xl';
                if ($len > 150) $fontSize = 'text-base';
                elseif ($len > 80) $fontSize = 'text-lg';
                elseif ($len > 40) $fontSize = 'text-xl';
            @endphp
            <div class="max-h-[160px] overflow-hidden">
                <p class="text-white font-black {{ $fontSize }} leading-snug font-montserrat">{!! nl2br(e($text)) !!}</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="absolute bottom-0 inset-x-0 px-8 py-3 flex flex-col gap-1.5" style="background: linear-gradient(to top, rgba(0,0,0,0.5), transparent); border-top: 1px solid rgba(255,255,255,0.07)">
            <div class="flex items-center gap-2">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-3.5 w-3.5 object-contain rounded">
                @endif
                <p class="font-bold text-[9px] tracking-widest font-montserrat truncate" style="color: {{ $brandColor }}">{{ $companyLabel }}</p>
            </div>
            <div class="flex items-center gap-2">
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
    <div class="absolute inset-0 bg-[#111827]" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
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
