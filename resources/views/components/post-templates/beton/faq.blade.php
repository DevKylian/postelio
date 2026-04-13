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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#D1D5DB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    <div class="flex-1 flex flex-col p-10 justify-center relative z-10 min-h-0">
        
        {{-- Slide 0: Question --}}
        @php
            $isSlide0 = $slideIndex === null || (int)$slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="absolute inset-x-10 top-1/2 -translate-y-1/2 bg-gray-900 p-8 rounded-2xl shadow-2xl border-b-4 border-gray-700">
            <div class="flex items-center gap-4 mb-6">
                 <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-white text-lg shadow-lg" style="background-color: {{ $brandColor }}">Q</div>
                 <span class="text-white/40 text-[10px] font-black uppercase tracking-[0.2em]">Question Client</span>
            </div>
            @php
                $qLen = mb_strlen($question);
                $qFontSize = 'text-2xl';
                if ($qLen > 100) $qFontSize = 'text-lg';
                elseif ($qLen > 50) $qFontSize = 'text-xl';
            @endphp
            <div class="max-h-[160px] overflow-hidden">
                <p class="text-white {{ $qFontSize }} font-black font-montserrat leading-tight uppercase tracking-tight">
                    {{ $question }}
                </p>
            </div>
        </div>

        {{-- Slide 1: Answer --}}
        @php
            $isSlide1 = (int)$slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}" class="absolute inset-x-10 top-1/2 -translate-y-1/2 bg-white border-4 border-gray-900 p-8 rounded-2xl shadow-2xl ml-6">
            <div class="flex items-center gap-4 mb-6">
                 <div class="w-10 h-10 rounded-full bg-gray-900 flex items-center justify-center font-black text-white text-lg shadow-lg">R</div>
                 <span class="text-gray-900/40 text-[10px] font-black uppercase tracking-[0.2em]">Réponse Expert</span>
            </div>
            @php
                $aLen = mb_strlen($answer);
                $aFontSize = 'text-lg';
                if ($aLen > 150) $aFontSize = 'text-sm';
                elseif ($aLen > 80) $aFontSize = 'text-base';
            @endphp
            <div class="max-h-[180px] overflow-hidden">
                <p class="text-gray-900 {{ $aFontSize }} font-black font-montserrat leading-snug uppercase tracking-tight">
                    {{ $answer }}
                </p>
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
                visualStyle="beton"
            />
        </div>
        @endif

    </div>

    @php
        $showFooter = $hasContactInfo ? ((int)$slideIndex < 2) : true;
        $footerAlpine = $slideIndex === null ? ($hasContactInfo ? 'x-show="current < 2"' : '') : '';
    @endphp
    <div {!! $footerAlpine !!} class="py-2.5 px-10 border-t border-gray-900/10 flex flex-col justify-center gap-1 z-20 min-h-16">
         <div class="flex items-center justify-between">
             <span class="text-gray-900 text-xs font-black font-montserrat uppercase tracking-[0.2em] truncate max-w-[200px]">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
             
             @if(($preview || $slideIndex !== null) && !$exporting)
                <div class="flex items-center gap-3">
                    <div class="text-gray-900/40 text-[10px] font-black" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                        @if(!$preview) {{ (int)($slideIndex ?? 0) + 1 }} / {{ $total }} @endif
                    </div>
                </div>
            @endif
         </div>
         <div class="flex items-center gap-1.5 overflow-hidden">
            @if($trade)
                <span class="px-1.5 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/50 text-[6px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $trade }}</span>
            @endif
            @if($city)
                <span class="px-1.5 py-0.5 rounded-sm bg-gray-900/5 text-gray-900/50 text-[6px] font-black uppercase tracking-wider border border-gray-900/10 whitespace-nowrap">{{ $city }}</span>
            @endif
         </div>
    </div>

    @if($preview && !$exporting)
        <div class="absolute inset-y-0 inset-x-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-40 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 pointer-events-auto transition-all shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-xl bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 pointer-events-auto transition-all shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
