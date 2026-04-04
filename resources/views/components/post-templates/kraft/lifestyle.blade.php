@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
    'slideIndex' => null,
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>

    {{-- Slide 0: Content --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0 flex flex-col" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        {{-- Image area --}}
        <div class="flex-1 min-h-0 relative flex items-center justify-center p-10 pb-4">
            <div class="relative w-full h-full border-[8px] border-neutral-800 p-3 bg-white shadow-2xl rotate-1 overflow-hidden">
                 <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] overflow-hidden relative">
                     @if($imageUrl)
                         <img src="{{ $imageUrl }}" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-contain">
                     @else
                         <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-cover">
                     @endif
                     <div class="absolute inset-0 shadow-[inset_0_0_100px_rgba(0,0,0,0.2)]"></div>
                 </div>
            </div>
        </div>

        {{-- Footer with text + company --}}
        <div class="h-[72px] shrink-0 bg-[#CBB198] border-t-[6px] border-gray-900 px-8 flex items-center justify-between z-20">
            @php
                $displayText = $text ?: 'LIFESTYLE';
                $len = mb_strlen($displayText);
                $fontSize = 'text-[12px]';
                if ($len > 25) $fontSize = 'text-[9px]';
                elseif ($len > 18) $fontSize = 'text-[10px]';
            @endphp
            <div class="flex flex-col max-w-[220px] overflow-hidden">
                 <span class="text-gray-900 {{ $fontSize }} font-black uppercase tracking-[0.1em] font-montserrat leading-tight truncate">{{ $displayText }}</span>
                 <span class="text-gray-900/40 text-[8px] font-black uppercase font-mono-technical mt-0.5 mb-1 truncate">{{ $companyLabel }}</span>
                 <div class="flex items-center gap-1 overflow-hidden shrink-0">
                    @if($trade)
                        <span class="px-1 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/60 text-[5px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $trade }}</span>
                    @endif
                    @if($city)
                        <span class="px-1 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/60 text-[5px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $city }}</span>
                    @endif
                 </div>
            </div>
            <div class="flex items-center gap-4 shrink-0">
                 <div class="w-8 h-8 bg-gray-900 -rotate-6 border border-white flex items-center justify-center">
                      <div class="w-4 h-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
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
