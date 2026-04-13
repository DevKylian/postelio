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
    'exporting' => false,
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    {{-- Grid Overlay --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
    </div>

    <div class="flex-1 flex flex-col p-12 justify-center relative z-10">
        
        {{-- Slide 0: Question --}}
        @php
            $isSlide0 = $slideIndex === null || (int)$slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="relative pl-14">
            <div class="absolute left-0 top-0 w-12 h-12 border-2 border-[#0047AB] bg-white flex items-center justify-center font-mono-technical text-[#0047AB] font-black z-20 text-xl shadow-lg">Q</div>
            <div class="bg-white p-8 shadow-2xl ring-1 ring-black/5">
                @php
                    $qLen = mb_strlen($question);
                    $qFontSize = 'text-base';
                    if ($qLen > 100) $qFontSize = 'text-xs';
                    elseif ($qLen > 50) $qFontSize = 'text-sm';
                @endphp
                <div class="max-h-[160px] overflow-hidden">
                    <p class="text-[#0047AB] {{ $qFontSize }} font-black font-mono-technical leading-tight uppercase tracking-tight">
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
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}" class="relative pl-14">
            <div class="absolute left-0 top-0 w-12 h-12 border-2 border-white flex items-center justify-center font-mono-technical text-white font-black z-20 text-xl shadow-lg" style="background-color: {{ $brandColor }}">R</div>
            <div class="bg-white p-8 shadow-2xl ring-1 ring-black/5 border-l-8" style="border-left-color: {{ $brandColor }}">
                @php
                    $aLen = mb_strlen($answer);
                    $aFontSize = 'text-sm';
                    if ($aLen > 150) $aFontSize = 'text-[11px]';
                    elseif ($aLen > 80) $aFontSize = 'text-xs';
                @endphp
                <div class="max-h-[180px] overflow-hidden">
                    <p class="text-[#0047AB] {{ $aFontSize }} font-black font-mono-technical leading-snug uppercase tracking-tight">
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
        <div {!! $slide2Alpine !!} style="{{ $slide2Style }}">
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

    </div>

    @php
        $showFooter = $hasContactInfo ? ((int)$slideIndex < 2) : true;
        $footerAlpine = $slideIndex === null ? ($hasContactInfo ? "x-show=\"current < 2\"" : '') : '';
    @endphp
    <div {!! $footerAlpine !!} class="py-2.5 px-10 bg-white border-t-2 border-[#0047AB] flex flex-col gap-1 relative z-20">
        <div class="flex items-center justify-between text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest">
            <span>{{ $companyLabel }}</span>
            
            @if($preview || ($slideIndex !== null && (int)$slideIndex < (int)($total - 1)))
                @php
                    $showPagination = $hasContactInfo ? ((int)$slideIndex < 2) : true;
                @endphp
                @if($showPagination && !$exporting)
                <span class="opacity-50" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                    @if(!$preview) {{ (int)$slideIndex + 1 }} / {{ $total }} @endif
                </span>
                @endif
            @endif
        </div>
        <div class="flex items-center gap-1.5 overflow-hidden">
            @if($trade)
                <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/60 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $trade }}</span>
            @endif
            @if($city)
                <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/60 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $city }}</span>
            @endif
        </div>
    </div>

    @if($preview && !$exporting)
        <div class="absolute inset-y-0 inset-x-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-8 h-8 bg-white border border-[#0047AB] text-[#0047AB] flex items-center justify-center hover:bg-[#0047AB] hover:text-white pointer-events-auto transition-colors shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-8 h-8 bg-white border border-[#0047AB] text-[#0047AB] flex items-center justify-center hover:bg-[#0047AB] hover:text-white pointer-events-auto transition-colors shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
