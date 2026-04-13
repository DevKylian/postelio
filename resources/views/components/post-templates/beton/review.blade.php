@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'customerName' => 'Client',
    'text' => '',
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $customerName = 'Jean-Pierre D.';
        $text = $text ?: 'Une expérience parfaite de A à Z. L\'équipe a su transformer notre vision en réalité avec un professionnalisme exemplaire. Les délais ont été respectés et les finitions sont impeccables. Nous recommandons vivement pour vos projets !';
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" !!}] bg-[#D1D5DB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' !!}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    {{-- Slide 0: Review --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style !!}">
        <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10 w-full">
            <div class="bg-white border-4 border-gray-900 p-8 rounded-2xl shadow-[20px_20px_0px_0px_rgba(15,23,42,0.1)] w-full max-w-[400px]">
                <div class="flex gap-1 mb-6">
                    @for($i=0; $i<5; $i++)
                        <svg class="w-6 h-6" style="color: {{ $brandColor !!}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                
                @php
                    $dlen = mb_strlen($text);
                    $fontSize = 'text-2xl';
                    if ($dlen > 200) $fontSize = 'text-base';
                    elseif ($dlen > 100) $fontSize = 'text-lg';
                    elseif ($dlen > 40) $fontSize = 'text-xl';
                @endphp
                <div class="max-h-[240px] overflow-hidden">
                    <p class="text-gray-900 {{ $fontSize !!} font-black font-montserrat leading-snug mb-8">
                        "{{ $text !!}"
                    </p>
                </div>

                <div class="flex items-center justify-between border-t-2 border-gray-100 pt-6">
                    <span class="text-gray-900 text-sm font-black uppercase font-montserrat">{{ $customerName !!}</span>
                    <span class="text-gray-900/40 text-[10px] font-bold uppercase tracking-widest">Client Vérifié</span>
                </div>
            </div>
        </div>

        <div class="h-20 px-10 bg-gray-900 flex flex-col justify-center gap-1 z-20">
             <div class="flex items-center justify-between">
                 <span class="text-white text-[10px] font-black font-montserrat uppercase tracking-[0.2em] truncate max-w-[250px]">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
                 <div class="w-4 h-4 rounded-full shrink-0" style="background-color: {{ $brandColor !!}"></div>
             </div>
             <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/50 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap">{{ $trade !!}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/50 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap">{{ $city !!}</span>
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
    <div class="absolute inset-0 p-8" {!! $slide1Alpine !!} style="{{ $slide1Style !!}">
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
