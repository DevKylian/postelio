@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'slides' => [null, null, null, null], // array of image URLs
    'slideContents' => [], // array of steps (text)
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $slides = [
            null,
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?q=80&w=800',
            'https://images.unsplash.com/photo-1517646287270-a5a067f920f0?q=80&w=800',
            'https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?q=80&w=800'
        ];
        $slideContents = [
            'Analyse de la structure et préparation.',
            'Mise en œuvre de l\'isolation thermique.',
            'Application de l\'enduit de finition.',
            'Contrôle final et nettoyage du chantier.'
        ];
        $phone = $phone ?? '01 23 45 67 89';
        $email = $email ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade ?? 'Artisan';
        $city = $city ?? 'Ma Ville';
    @endphp
@endif

@php
    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total = count($slides) + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-900 flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
        
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-gradient-to-br from-midnight via-gray-800 to-midnight" style="background-image: linear-gradient(30deg, #111827 12%, transparent 12.5%, transparent 87%, #111827 87.5%, #111827), linear-gradient(150deg, #111827 12%, transparent 12.5%, transparent 87%, #111827 87.5%, #111827), linear-gradient(30deg, #111827 12%, transparent 12.5%, transparent 87%, #111827 87.5%, #111827), linear-gradient(150deg, #111827 12%, transparent 12.5%, transparent 87%, #111827 87.5%, #111827), linear-gradient(60deg, #1f2937 25%, transparent 25.5%, transparent 75%, #1f2937 75%, #1f2937), linear-gradient(60deg, #1f2937 25%, transparent 25.5%, transparent 75%, #1f2937 75%, #1f2937); background-size: 80px 140px; background-position: 0 0, 0 0, 40px 70px, 40px 70px, 0 0, 40px 70px;"></div>
        </div>

        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || (int)$slideIndex === $index;
                $slideAlpine = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : '';
                $slideStyle = (int)$slideIndex !== $index && $slideIndex !== null ? 'display: none;' : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                {{-- Show image if available --}}
                @if($imageUrl)
                    <div class="absolute inset-0">
                        <img src="{{ $imageUrl }}" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-cover">
                        <div class="absolute inset-0 bg-slate-900/60"></div>
                    </div>
                @endif

                <div class="absolute inset-0 flex flex-col p-6 pb-20 justify-center gap-2 z-10">
                    @foreach($slideContents as $stepIndex => $stepText)
                        @php
                            $isCurrentStep = $index === ($stepIndex + 1);
                            $isPastStep = $index > ($stepIndex + 1);
                        @endphp
                        <div class="flex items-center gap-3 group/step transition-all duration-300 {{ $isCurrentStep ? 'scale-105 translate-x-2' : ($isPastStep ? 'opacity-40' : 'opacity-20') }}">
                            <div class="w-8 h-8 rounded-full backdrop-blur-md border flex items-center justify-center font-black text-white text-[10px] shadow-lg shrink-0"
                                 style="background-color: {{ $isCurrentStep ? $brandColor : 'rgba(255,255,255,0.05)' }}; border-color: {{ $isCurrentStep ? 'white' : 'rgba(255,255,255,0.1)' }}">
                                {{ $stepIndex + 1 }}
                            </div>
                            <div class="flex-1 backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-3 shadow-2xl max-h-[70px] overflow-hidden">
                                @php
                                    $sLen = mb_strlen($stepText);
                                    $sFontSize = 'text-xs';
                                    if ($sLen > 80) $sFontSize = 'text-[8px]';
                                    elseif ($sLen > 40) $sFontSize = 'text-[10px]';
                                @endphp
                                <p class="text-white {{ $sFontSize }} font-bold uppercase tracking-tight leading-tight">{{ $stepText }}</p>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($index === 0)
                        <div class="mt-4 text-center backdrop-blur-2xl bg-white/5 border border-white/10 p-5 rounded-3xl shadow-2xl">
                             <span class="text-white/40 text-[8px] font-black uppercase tracking-[0.2em] block mb-1 font-montserrat text-center w-full">TECHNICAL WORKFLOW</span>
                             <h2 class="text-white text-xl font-black font-montserrat leading-none uppercase tracking-tighter" style="color: {{ $brandColor }}">LES ÉTAPES DU CHANTIER</h2>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Slide Contact (Final Slide) --}}
        @php
            $finalIndex = count($slides);
            $isFinalVisible = (int)$slideIndex === $finalIndex;
            $finalAlpine = $slideIndex === null ? "x-show=\"current === $finalIndex\" style=\"display: none;\"" : '';
            $finalStyle = (int)$slideIndex !== $finalIndex && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-0" {!! $finalAlpine !!} style="{{ $finalStyle }}">
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

        @php
            $showFooter = $hasContactInfo ? ((int)$slideIndex < $finalIndex) : true;
            $footerAlpine = $slideIndex === null ? ($hasContactInfo ? "x-show=\"current < $finalIndex\"" : "") : '';
        @endphp
        @if($showFooter)
        <div {!! $footerAlpine !!} class="absolute inset-x-0 bottom-0 py-3 px-10 backdrop-blur-md bg-white/5 flex flex-col gap-1 border-t border-white/10 z-30">
             <div class="flex items-center justify-between">
                 <span class="text-white/50 text-[9px] font-black uppercase tracking-[0.3em] font-montserrat truncate max-w-[200px]">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
                 <div class="flex items-center gap-2">
                      @for($i=0; $i<$total; $i++)
                        <div class="w-4 h-1 rounded-full transition-all duration-300" 
                             @if($preview) :class="current === {{ $i }} ? '' : 'opacity-20 bg-white'" :style="current === {{ $i }} ? 'background-color: {{ $brandColor }}' : ''"
                             @else style="background-color: {{ (int)($slideIndex ?? 0) === $i ? $brandColor : 'rgba(255,255,255,0.2)' }}" @endif>
                        </div>
                      @endfor
                 </div>
             </div>
             <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-md bg-white/5 text-white/30 text-[6px] font-black uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-md bg-white/5 text-white/30 text-[6px] font-black uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $city }}</span>
                @endif
             </div>
        </div>
        @endif

        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-40">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-slate-900/50 text-white flex items-center justify-center hover:bg-slate-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-slate-900/50 text-white flex items-center justify-center hover:bg-slate-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
