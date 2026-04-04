@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'question' => '',
    'answer' => '',
    'logoUrl' => null,
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $question = $question ?: 'Quels sont les délais moyens pour une rénovation complète de toiture ?';
        $answer = $answer ?: 'Il faut compter entre 1 et 2 semaines selon la surface et la complexité de la charpente.';
        $phone = $phone ?? '01 23 45 67 89';
        $email = $email ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade ?? 'Artisan';
        $city = $city ?? 'Ma Ville';
    @endphp
@endif

@php
    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total = 2 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    <div class="flex-1 flex flex-col p-10 justify-center relative z-10 min-h-0">
        
        {{-- Slide 0: Question --}}
        @php
            $isSlide0 = $slideIndex === null || (int)$slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="absolute inset-x-10 top-1/2 -translate-y-1/2 pl-16">
            <div class="absolute left-0 top-0 w-12 h-12 bg-gray-900 flex items-center justify-center font-montserrat text-white text-xl font-black shadow-xl -rotate-3 border-2 border-white">Q</div>
            <div class="bg-white border-4 border-gray-900 p-8 shadow-[10px_10px_0px_0px_rgba(15,23,42,0.1)]">
                @php
                    $qLen = mb_strlen($question);
                    $qFontSize = 'text-xl';
                    if ($qLen > 100) $qFontSize = 'text-base';
                    elseif ($qLen > 50) $qFontSize = 'text-lg';
                @endphp
                <div class="max-h-[160px] overflow-hidden">
                    <p class="text-gray-900 {{ $qFontSize }} font-black font-montserrat leading-tight uppercase">
                        {{ $question }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Slide 1: Answer --}}
        @php
            $isSlide1 = (int)$slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}" class="absolute inset-x-10 top-1/2 -translate-y-1/2 pl-16">
            <div class="absolute left-0 top-0 w-12 h-12 flex items-center justify-center font-black text-white text-xl font-montserrat shadow-xl rotate-3 border-2 border-white" style="background-color: {{ $brandColor }}">R</div>
            <div class="bg-white border-4 border-gray-900 p-8 shadow-[10px_10px_0px_0px_rgba(15,23,42,0.1)] border-l-[12px] border-l-gray-900">
                @php
                    $aLen = mb_strlen($answer);
                    $aFontSize = 'text-base';
                    if ($aLen > 150) $aFontSize = 'text-xs';
                    elseif ($aLen > 80) $aFontSize = 'text-sm';
                @endphp
                <div class="max-h-[180px] overflow-hidden">
                    <p class="text-gray-900 {{ $aFontSize }} font-bold font-montserrat leading-snug uppercase">
                        {{ $answer }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Slide 2: Contact --}}
        @php
            $isSlide2 = (int)$slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = (int)$slideIndex !== 2 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-6" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
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

    </div>

    @php
        $showFooter = $hasContactInfo ? ((int)$slideIndex < 2) : true;
        $footerAlpine = $slideIndex === null ? ($hasContactInfo ? "x-show=\"current < 2\"" : "") : '';
    @endphp
    <div {!! $footerAlpine !!} class="py-2 px-10 border-t-4 border-gray-900 bg-[#CBB198] flex flex-col gap-1 relative z-20 min-h-[4rem]">
         <div class="flex items-center justify-between">
             <span class="text-gray-900 text-[11px] font-black uppercase tracking-[0.2em] font-montserrat truncate max-w-[200px]">{{ $companyLabel }}</span>
             
             @if($preview || $slideIndex !== null)
                <div class="text-gray-900/50 text-[10px] font-black font-montserrat tracking-widest" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                    @if(!$preview) {{ (int)$slideIndex + 1 }} / {{ $total }} @endif
                </div>
            @endif
         </div>
         <div class="flex items-center gap-1.5 overflow-hidden">
            @if($trade)
                <span class="px-1.5 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/60 text-[6px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $trade }}</span>
            @endif
            @if($city)
                <span class="px-1.5 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/60 text-[6px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $city }}</span>
            @endif
         </div>
    </div>

    {{-- Controls (Preview Only) --}}
    @if($preview)
        <div class="absolute inset-y-0 inset-x-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 border-4 border-gray-900 bg-white text-gray-900 flex items-center justify-center hover:bg-gray-900 hover:text-white pointer-events-auto transition-all shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 border-4 border-gray-900 bg-white text-gray-900 flex items-center justify-center hover:bg-gray-900 hover:text-white pointer-events-auto transition-all shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
