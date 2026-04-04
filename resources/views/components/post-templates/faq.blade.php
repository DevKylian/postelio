@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Vos questions, nos réponses',
    'question' => '',
    'answer' => '',
    'logoUrl' => null,
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'slideIndex' => null,
    'preview' => false,
    'brandTextColor' => '#FFFFFF',
])

@if($preview)
    @php
        $question = 'Quels sont les délais moyens pour une rénovation complète de toiture ?';
        $answer = 'Il faut compter entre 1 et 2 semaines selon la surface et la complexité de la charpente.';
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

<div {!! $alpineData !!} class="w-full aspect-square bg-midnight flex flex-col p-10 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="border-top: 8px solid {{ $brandColor }}">
    
    <div class="flex-1 flex flex-col justify-center">
        <span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span>
        
        {{-- Slide 0: Question --}}
        @php
            $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}">
            <h3 class="font-black uppercase tracking-widest text-sm mb-4" style="color: white; opacity: 0.6;">FAQ Artisan</h3>
            @php
                $qLen = mb_strlen($question);
                $qFontSize = 'text-2xl';
                if ($qLen > 120) $qFontSize = 'text-lg';
                elseif ($qLen > 60) $qFontSize = 'text-xl';
            @endphp
            <div class="max-h-[200px] overflow-hidden">
                <p class="text-white font-black tracking-tight {{ $qFontSize }} leading-snug font-montserrat">{!! nl2br(e($question)) !!}</p>
            </div>
        </div>

        {{-- Slide 1: Answer --}}
        @php
            $isSlide1 = (int)$slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}">
            <h3 class="font-black uppercase tracking-widest text-sm mb-4" style="color: {{ $brandColor }}">La Réponse</h3>
            @php
                $aLen = mb_strlen($answer);
                $aFontSize = 'text-lg';
                if ($aLen > 150) $aFontSize = 'text-sm';
                elseif ($aLen > 80) $aFontSize = 'text-base';
            @endphp
            <div class="max-h-[220px] overflow-hidden">
                <p class="text-white font-bold {{ $aFontSize }} leading-relaxed font-montserrat">{!! nl2br(e($answer)) !!}</p>
            </div>
        </div>

        {{-- Slide 2: Contact --}}
        @php
            $isSlide2 = (int)$slideIndex === 2;
            $slide2Alpine = $slideIndex === null ? "x-show=\"current === 2\" style=\"display: none;\"" : '';
            $slide2Style = (int)$slideIndex !== 2 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-0 flex items-center justify-center bg-midnight" {!! $slide2Alpine !!} style="{{ $slide2Style }}">
             <x-post-templates.contact-slide 
                :brand-color="$brandColor" 
                :company-label="$companyLabel"
                :phone="$phone"
                :email="$email"
                :website="$website"
                :trade="$trade"
                :city="$city"
                :brand-text-color="$brandTextColor"
                visualStyle="glass"
            />
        </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="pt-4 border-t border-white/10 flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-5 w-5 object-contain rounded">
                @endif
                <p class="font-bold text-xs tracking-widest font-montserrat" style="color: {{ $brandColor }}">{{ $companyLabel }}</p>
            </div>
            
            @if($preview || $slideIndex !== null)
                @php
                    $showTotal = $hasContactInfo ? ((int)$slideIndex !== 2) : true;
                @endphp
                @if($showTotal)
                <div class="text-white/40 text-[10px] font-bold" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                    @if(!$preview) {{ (int)$slideIndex + 1 }} / {{ $total }} @endif
                </div>
                @endif
            @endif
        </div>
        <div class="flex items-center gap-2 mt-0.5">
            @php
                $tagTextCol = $brandColor;
                $hexTag = str_replace('#', '', $brandColor);
                if (strlen($hexTag) == 3) $hexTag = $hexTag[0].$hexTag[0].$hexTag[1].$hexTag[1].$hexTag[2].$hexTag[2];
                $rTag = hexdec(substr($hexTag, 0, 2));
                $gTag = hexdec(substr($hexTag, 2, 2));
                $bTag = hexdec(substr($hexTag, 4, 2));
                $yiqTag = (($rTag * 299) + ($gTag * 587) + ($bTag * 114)) / 1000;
                if ($yiqTag > 235) $tagTextCol = '#1E293B'; 
            @endphp
            
            @if($trade)
                <span class="px-2 py-0.5 rounded-full bg-white text-[7px] font-black uppercase tracking-[0.15em] shadow-sm ring-1 ring-black/5" style="color: {{ $tagTextCol }}">{{ $trade }}</span>
            @endif
            @if($city)
                <span class="px-2 py-0.5 rounded-full bg-white text-[7px] font-black uppercase tracking-[0.15em] shadow-sm ring-1 ring-black/5" style="color: {{ $tagTextCol }}">{{ $city }}</span>
            @endif
        </div>
    </div>

    {{-- Controls (Preview Only) --}}
    @if($preview)
        <div class="absolute inset-y-0 inset-x-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 pointer-events-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 pointer-events-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif

    <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-white/5 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
    </svg>
</div>
