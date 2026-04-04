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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    {{-- Slide 0: Erreur --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        {{-- Safety Stripes Border Top --}}
        <div class="h-6 w-full flex relative z-20">
            @for($i=0; $i<20; $i++)
                <div class="flex-1 h-full skew-x-[35deg] {{ $i % 2 == 0 ? 'bg-orange-600' : 'bg-gray-900' }}"></div>
            @endfor
        </div>

        <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10">
            <div class="bg-white border-[6px] border-gray-900 p-10 shadow-[25px_25px_0px_0px_rgba(15,23,42,0.1)] w-full max-w-[420px] relative">
                <div class="flex items-center gap-6 mb-8">
                     <div class="w-16 h-16 bg-orange-600 flex items-center justify-center shadow-xl border-4 border-gray-900 rotate-3 shrink-0" style="background-color: {{ $brandColor }}">
                          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                     </div>
                     <span class="text-gray-900 text-xl font-black uppercase font-montserrat">ALERTE ERREUR</span>
                </div>

                @php
                    $len = mb_strlen($text);
                    $fontSize = 'text-2xl';
                    if ($len > 80) $fontSize = 'text-base';
                    elseif ($len > 40) $fontSize = 'text-lg';
                @endphp
                <div class="max-h-[160px] overflow-hidden">
                    <h3 class="text-gray-900 {{ $fontSize }} font-black font-montserrat leading-[1.1] mb-8 uppercase italic underline decoration-[6px] underline-offset-8 text-center" style="text-decoration-color: {{ $brandColor }}">
                        {{ $text }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="h-20 px-10 bg-gray-900 flex flex-col justify-center gap-1 border-t-4 border-white/10 z-20">
            <span class="text-white text-xs font-black uppercase font-mono-technical tracking-[0.3em] truncate w-full">{{ $companyLabel }}</span>
            <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/40 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap font-mono-technical">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/40 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap font-mono-technical">{{ $city }}</span>
                @endif
            </div>
        </div>

        {{-- Safety Stripes Border Bottom --}}
        <div class="h-6 w-full flex relative z-20">
            @for($i=0; $i<20; $i++)
                <div class="flex-1 h-full -skew-x-[35deg] {{ $i % 2 == 0 ? 'bg-orange-600' : 'bg-gray-900' }}"></div>
            @endfor
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
            visualStyle="kraft"
        />
    </div>
    @endif

    @if($preview)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 shadow-xl border-2 border-white/20 pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 shadow-xl border-2 border-white/20 pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
