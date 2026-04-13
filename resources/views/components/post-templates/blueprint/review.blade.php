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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" !!}] bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' !!}">
    
    {{-- Grid background --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 40px 40px;">
    </div>

    {{-- Slide 0: Review --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10" {!! $slide0Alpine !!} style="{{ $slide0Style !!}">
        
        <div class="bg-white border-2 border-[#0047AB] p-10 shadow-2xl relative w-full max-w-[360px] z-20">
            {{-- Simplified Rating --}}
            <div class="flex items-center gap-1 mb-8">
                @for($i=0; $i<5; $i++)
                <div class="w-3.5 h-3.5" style="background-color: {{ $brandColor !!}"></div>
                @endfor
                <span class="ml-4 text-[10px] font-black font-mono-technical text-[#0047AB] tracking-widest">5.0/5.0</span>
            </div>

            @php
                $len = mb_strlen($text);
                $fontSize = 'text-lg';
                if ($len > 150) $fontSize = 'text-xs';
                elseif ($len > 80) $fontSize = 'text-sm';
                elseif ($len > 40) $fontSize = 'text-base';
            @endphp

            <div class="max-h-[220px] overflow-hidden">
                <p class="text-[#0047AB] {{ $fontSize !!} font-black leading-tight font-mono-technical mb-10 text-center">
                    "{{ $text !!}"
                </p>
            </div>

            <div class="pt-6 border-t border-dashed border-[#0047AB]/20 flex items-center justify-between">
                <div class="flex-1 min-w-0 pr-4">
                    <span class="text-[#0047AB] text-xs font-black font-mono-technical uppercase truncate block link-blue-900">{{ $customerName !!}</span>
                </div>
                <div class="text-right flex flex-col items-end min-w-0 flex-1">
                    <span class="text-[#0047AB] text-[9px] font-black font-mono-technical uppercase truncate block w-full mb-1">{{ $companyLabel !!}</span>
                    <div class="flex items-center gap-1 overflow-hidden">
                        @if($trade)
                            <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/60 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $trade !!}</span>
                        @endif
                        @if($city)
                            <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/60 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $city !!}</span>
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
    <div class="absolute inset-0 p-8" {!! $slide1Alpine !!} style="{{ $slide1Style !!}">
        <x-post-templates.contact-slide 
            :brand-color="$brandColor" 
            :company-label="$companyLabel"
            :phone="$phone"
            :email="$email"
            :website="$website"
            :trade="$trade"
            :city="$city"
            visualStyle="blueprint"
        />
    </div>
    @endif

    @if($preview)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20 pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20 pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
