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
    'logoUrl' => null,
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $customerName = 'Jean-Pierre D.';
        $text = $text ?: 'Une expérience parfaite. Équipe professionnelle, vision transformée en réalité avec un souci du détail exemplaire. Nous recommandons !';
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

<div {!! $alpineData !!} class="w-full {{ ($aspectRatio ?? '4:5') === '4:5' ? 'aspect-[4/5]' : 'aspect-square' }} bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    {{-- Slide 0: Review --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        <div class="flex-1 p-10 flex flex-col items-center justify-center relative z-10">
            
            <div class="bg-white border-4 border-gray-900 p-10 shadow-[15px_15px_0px_0px_rgba(15,23,42,0.1)] relative w-full max-w-[380px] -rotate-1">
                
                @php
                    $len = mb_strlen($text);
                    $fontSize = 'sm:text-lg text-base';
                @endphp

                <div class="max-h-[220px] overflow-hidden">
                    <p class="text-gray-900 {{ $fontSize }} font-black leading-[1.1] font-montserrat text-center">
                        "{{ $text }}"
                    </p>
                </div>

                <div class="pt-8 border-t-2 border-dashed border-gray-800 flex items-center justify-between">
                    <div>
                        <span class="text-gray-900 text-sm font-black uppercase font-montserrat">{{ $customerName }}</span>
                    </div>
                    <div class="w-10 h-10 border-2 border-gray-900 flex items-center justify-center">
                        <div class="w-6 h-6 rounded-full" style="background-color: {{ $brandColor }}"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Branding --}}
        <div class="h-20 px-10 bg-gray-900 flex flex-col justify-center gap-1 z-20">
             <div class="flex items-center justify-between">
                 <span class="text-white text-xs font-black font-mono-technical uppercase tracking-[0.3em] truncate max-w-[250px]">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
                 <div class="flex gap-1.5 shrink-0">
                      <div class="w-2.5 h-2.5" style="background-color: {{ $brandColor }}"></div>
                      <div class="w-2.5 h-2.5 bg-white/20"></div>
                      <div class="w-2.5 h-2.5 bg-white/10"></div>
                 </div>
             </div>
             <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/40 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/40 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap">{{ $city }}</span>
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

