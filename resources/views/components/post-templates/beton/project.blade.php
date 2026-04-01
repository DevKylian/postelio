@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'text' => 'TRANSFORMATION',
    'preview' => false,
    'slideIndex' => null, // null = interactive alpine preview, int = render specific slide
])

@if($preview)
    @php
        $imageBeforeUrl = $imageBeforeUrl ?? 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=800';
        $imageAfterUrl = $imageAfterUrl ?? 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?q=80&w=800';
        $text = ($text === 'TRANSFORMATION') ? 'Rénovation Complète d\'Intérieur' : $text;
        $phone = $phone ?? '01 23 45 67 89';
        $email = $email ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade ?? 'Artisan';
        $city = $city ?? 'Ma Ville';
    @endphp
@endif

@php
    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total = 3 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full aspect-square bg-[#D1D5DB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    {{-- Main Content --}}
    <div class="flex-1 p-6 relative z-10 min-h-0">
        
        {{-- Slide 0: Beton Split --}}
        @php
            $isSlide0 = $slideIndex === null || (int)$slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 grid grid-cols-2 gap-4" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
            <div class="relative rounded-lg overflow-hidden border-2 border-gray-900 shadow-xl bg-gray-400">
                @if($imageBeforeUrl)
                    <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @endif
                <div class="absolute top-3 left-3 bg-gray-900 px-3 py-1 rounded text-[10px] font-black text-white uppercase font-montserrat tracking-widest">Avant</div>
            </div>
            <div class="relative rounded-lg overflow-hidden border-2 border-gray-900 shadow-xl bg-gray-500">
                @if($imageAfterUrl)
                    <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @endif
                <div class="absolute top-3 left-3 bg-gray-900 px-3 py-1 rounded text-[10px] font-black text-white uppercase font-montserrat tracking-widest" style="background-color: {{ $brandColor }}">Après</div>
            </div>
        </div>

        {{-- Slide 1: Full Avant --}}
        @php
            $isSlide1 = (int)$slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-950 rounded-xl border-4 border-gray-900 overflow-hidden shadow-2xl" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            @if($imageBeforeUrl)
                <img src="{{ $imageBeforeUrl }}" class="absolute inset-0 w-full h-full object-contain p-4">
                <div class="absolute top-6 left-6 bg-gray-900 px-5 py-2 rounded-lg text-xs font-black text-white uppercase font-montserrat tracking-[0.2em] shadow-2xl z-20">VUE INITIALE</div>
            @endif
        </div>

        {{-- Slide 2: Full Après --}}
        @php
            $isSlide2 = (int)$slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = (int)$slideIndex !== 2 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-6 bg-slate-900 rounded-xl border-4 border-gray-900 overflow-hidden shadow-2xl" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
            @if($imageAfterUrl)
                <img src="{{ $imageAfterUrl }}" class="absolute inset-0 w-full h-full object-contain p-4">
                <div class="absolute top-6 right-6 px-5 py-2 rounded-lg text-xs font-black text-white uppercase font-montserrat tracking-[0.2em] shadow-2xl z-20" style="background-color: {{ $brandColor }}">RÉSULTAT FINAL</div>
            @endif
        </div>

        {{-- Slide 3: Contact --}}
        @php
            $isSlide3 = (int)$slideIndex === 3;
            $slide3Alpine = $slideIndex === null ? "x-show=\"current === 3\" style=\"display: none;\"" : '';
            $slide3Style = (int)$slideIndex !== 3 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-0 p-8" {!! $slide3Alpine !!} style="{{ $slide3Style }}">
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

        {{-- Controls (Preview Only) --}}
        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-40 pointer-events-none">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 shadow-2xl border-2 border-white/20 pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 shadow-2xl border-2 border-white/20 pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-10 right-10 bg-gray-900 text-white text-[10px] font-black font-montserrat px-3 py-1.5 rounded-lg shadow-2xl z-40" x-text="(current + 1) + ' / ' + total"></div>
        @elseif($slideIndex !== null)
             <div class="absolute top-10 right-10 bg-gray-900 text-white text-[10px] font-black font-montserrat px-3 py-1.5 rounded-lg shadow-2xl z-40">
                {{ (int)$slideIndex + 1 }} / {{ $total }}
            </div>
        @endif

    </div>

    @php
        $showBranding = $hasContactInfo ? ((int)$slideIndex < 3) : true;
        $brandingAlpine = $slideIndex === null ? ($hasContactInfo ? 'x-show="current < 3"' : '') : '';
    @endphp
    @if($showBranding)
    <div {!! $brandingAlpine !!} class="shrink-0 h-20 flex items-center justify-between border-t border-gray-900/10 p-6 relative z-20">
         @php
             $cLen = mb_strlen($companyLabel);
             $cFontSize = 'text-lg';
             if ($cLen > 40) $cFontSize = 'text-xs';
             elseif ($cLen > 25) $cFontSize = 'text-sm';
             elseif ($cLen > 15) $cFontSize = 'text-base';
         @endphp
         <div class="flex flex-col max-w-[200px]">
             <span class="text-gray-900/40 text-[9px] font-black uppercase tracking-widest leading-none mb-1">PROJET RÉALISÉ PAR</span>
             <span class="text-gray-900 {{ $cFontSize }} font-black uppercase tracking-tighter leading-tight font-montserrat truncate mb-1">{{ $companyLabel }}</span>
             <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/50 text-[6px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/50 text-[6px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $city }}</span>
                @endif
             </div>
         </div>
         <div class="flex items-center gap-4">
             {{-- Progress dots --}}
             <div class="flex gap-2">
                 @for($i=0; $i<$total; $i++)
                    <div class="w-2 h-2 rounded-full transition-all" 
                         @if($preview) :class="current === {{ $i }} ? 'bg-gray-900' : 'bg-gray-300'"
                         @else style="background-color: {{ (int)($slideIndex ?? 0) === $i ? 'rgb(17, 24, 39)' : 'rgb(209, 213, 219)' }}" @endif>
                    </div>
                 @endfor
             </div>
             <div class="w-12 h-12 rounded-lg border-2 border-gray-900 flex items-center justify-center bg-white shadow-lg">
                  <div class="w-8 h-8 rounded-full" style="background-color: {{ $brandColor }}"></div>
             </div>
         </div>
    </div>
    @endif
</div>
