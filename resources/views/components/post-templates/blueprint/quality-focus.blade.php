@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'imageUrl' => null,
    'text' => 'FOCUS QUALITÉ',
    'logoUrl' => null,
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'FOCUS QUALITÉ') ? 'Le choix des meilleurs matériaux pour une durabilité garantie.' : $text;
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

<div {!! $alpineData !!} class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 20px 20px;">
    </div>

    {{-- Slide 0: Content --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        <div class="flex-1 relative flex items-center justify-center p-16 overflow-hidden text-[#0047AB]">
            <div class="relative w-full h-full border border-white/30 p-2 bg-white shadow-2xl">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" class="w-full h-full object-contain">
                @else
                    <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=600" class="w-full h-full object-cover">
                @endif
            </div>
        </div>

        <div class="h-16 px-10 bg-white border-t-2 border-[#0047AB] flex items-center justify-between z-20">
              @php
                    $len = mb_strlen($text);
                    $fontSize = 'text-sm';
                    if ($len > 80) $fontSize = 'text-[9px]';
                    elseif ($len > 40) $fontSize = 'text-xs';
              @endphp
              <div class="flex-1 max-w-[70%] max-h-[50px] overflow-hidden flex items-center shrink-0">
                   <span class="text-[#0047AB] {{ $fontSize }} font-black font-mono-technical tracking-tighter uppercase leading-tight truncate-multiline">{{ $text }}</span>
              </div>
             <div class="flex flex-col items-end gap-1 min-w-0">
                  <div class="flex items-center gap-2">
                       <span class="text-[#0047AB]/60 text-[8px] font-black font-mono-technical uppercase tracking-widest truncate max-w-[120px]">{{ $companyLabel }}</span>
                       <div class="w-4 h-1.5" style="background-color: {{ $brandColor }}"></div>
                  </div>
                  <div class="flex items-center gap-1 overflow-hidden">
                    @if($trade)
                        <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/50 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $trade }}</span>
                    @endif
                    @if($city)
                        <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/50 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $city }}</span>
                    @endif
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
    <div class="absolute inset-0 p-8" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
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
                <svg class="w-5 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
