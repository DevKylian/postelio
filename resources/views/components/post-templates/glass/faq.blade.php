@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'question' => '',
    'answer' => '',
    'logoUrl' => null,
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full blur-[80px] opacity-20" style="background-color: {{ $brandColor }}"></div>

    <div class="flex-1 flex flex-col p-8 justify-center gap-6 relative z-10">
        
        {{-- Slide 0: Question --}}
        @php
            $isSlide0 = $slideIndex === null || (int)$slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="space-y-4">
            <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] ml-1">Question Fréquente</span>
            <div class="backdrop-blur-md bg-white/5 border border-white/10 rounded-2xl p-6 shadow-xl">
                @php
                    $qLen = mb_strlen($question);
                    $qFontSize = 'text-2xl';
                    if ($qLen > 100) $qFontSize = 'text-lg';
                    elseif ($qLen > 50) $qFontSize = 'text-xl';
                @endphp
                <div class="max-h-[220px] overflow-hidden">
                    <p class="text-white {{ $qFontSize }} font-bold leading-tight">
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
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}" class="space-y-4">
            <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] ml-1">Réponse d'Expert</span>
            <div class="backdrop-blur-xl bg-white/10 border-l-4 rounded-2xl p-6 shadow-2xl" style="border-color: {{ $brandColor }}">
                @php
                    $aLen = mb_strlen($answer);
                    $aFontSize = 'text-lg';
                    if ($aLen > 150) $aFontSize = 'text-sm';
                    elseif ($aLen > 80) $aFontSize = 'text-base';
                @endphp
                <div class="max-h-[240px] overflow-hidden">
                    <p class="text-white/90 {{ $aFontSize }} leading-relaxed font-medium">
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
                visualStyle="glass"
            />
        </div>
        @endif

    </div>

    <div class="px-8 py-4 backdrop-blur-md bg-white/5 border-t border-white/10 flex flex-col gap-2 relative z-20">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                 <div class="w-2 h-2 rounded-full" style="background-color: {{ $brandColor }}"></div>
                 <span class="text-white/50 text-[10px] font-bold uppercase tracking-widest">{{ $companyLabel }}</span>
            </div>
            
            @if($preview || ($slideIndex !== null && (int)$slideIndex !== (int)($total - 1)))
                @php
                    $showTotal = $hasContactInfo ? ((int)$slideIndex !== 2) : true;
                @endphp
                @if($showTotal)
                <div class="text-white/20 text-[10px] font-bold tracking-widest" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                    @if(!$preview) {{ $slideIndex + 1 }} / {{ $total }} @endif
                </div>
                @endif
            @endif
        </div>
        <div class="flex items-center gap-1.5 overflow-hidden">
            @if($trade)
                <span class="px-1.5 py-0.5 rounded-md bg-white/5 text-white/30 text-[6px] font-black uppercase tracking-wider border border-white/5">{{ $trade }}</span>
            @endif
            @if($city)
                <span class="px-1.5 py-0.5 rounded-md bg-white/5 text-white/30 text-[6px] font-black uppercase tracking-wider border border-white/5">{{ $city }}</span>
            @endif
        </div>
    </div>

    {{-- Controls (Preview Only) --}}
    @if($preview)
        <div class="absolute inset-y-0 inset-x-4 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 text-white flex items-center justify-center hover:bg-white/10 backdrop-blur-md pointer-events-auto transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 text-white flex items-center justify-center hover:bg-white/10 backdrop-blur-md pointer-events-auto transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
