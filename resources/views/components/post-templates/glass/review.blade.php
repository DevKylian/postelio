@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'customerName' => 'Client',
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    {{-- Slide 0: Review Content --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        {{-- Background pattern --}}
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: radial-gradient({{ $brandColor }} 1px, transparent 1px); background-size: 30px 30px;"></div>
        </div>

        {{-- Backdrop glass --}}
        <div class="relative flex-1 flex flex-col items-center justify-center p-8">
            
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl w-full max-w-[340px] relative z-20">
                <div class="flex items-center gap-1 mb-4">
                    @for($i=0; $i<5; $i++)
                    <svg class="w-4 h-4" style="color: {{ $brandColor }}" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    @endfor
                </div>

                @php
                    $len = mb_strlen($text);
                    $fontSize = 'text-lg';
                    if ($len > 150) $fontSize = 'text-xs';
                    elseif ($len > 80) $fontSize = 'text-sm';
                    elseif ($len > 40) $fontSize = 'text-base';
                @endphp
                <div class="max-h-[220px] overflow-hidden">
                    <p class="text-white {{ $fontSize }} font-medium leading-relaxed italic mb-6 text-center">
                        "{{ $text }}"
                    </p>
                </div>

                <div class="flex items-center justify-between border-t border-white/10 pt-4 gap-2">
                    <span class="text-white/80 font-bold text-sm truncate flex-1 shrink-0">{{ $customerName }}</span>
                    <div class="flex flex-col items-end min-w-0">
                        <span class="text-white/40 text-[9px] font-bold uppercase tracking-widest truncate mb-1 text-right w-full">{{ $companyLabel }}</span>
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

        {{-- Brand glow --}}
        <div class="absolute -bottom-20 -right-20 w-64 h-64 rounded-full blur-[100px] opacity-30" style="background-color: {{ $brandColor }}"></div>
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
