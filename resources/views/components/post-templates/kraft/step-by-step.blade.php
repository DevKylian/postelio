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
    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#BC9A7E] relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
        {{-- Kraft Texture Overlay --}}
        <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply z-20" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>

        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || (int)$slideIndex === $index;
                $slideAlpine = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : '';
                $slideStyle = (int)$slideIndex !== $index && $slideIndex !== null ? 'display: none;' : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                {{-- Show image if index > 0 (steps) or if it's the cover with image --}}
                @if($imageUrl)
                    <div class="absolute inset-0">
                        <img src="{{ $imageUrl }}" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-cover">
                        <div class="absolute inset-0 bg-gray-900/40"></div>
                    </div>
                @endif

                <div class="absolute inset-0 flex flex-col p-8 pb-20 justify-center gap-3 z-10">
                    @foreach($slideContents as $stepIndex => $stepText)
                        @php
                            $isCurrentStep = $index === ($stepIndex + 1);
                            $isPastStep = $index > ($stepIndex + 1);
                            $rot = ($stepIndex % 2 == 0) ? 'rotate-1' : '-rotate-1';
                        @endphp
                        <div class="relative flex items-center gap-4 group/step transition-all duration-300 {{ $isCurrentStep ? 'scale-105 translate-x-2' : ($isPastStep ? 'opacity-40' : 'opacity-20') }}">
                            <div class="w-10 h-10 flex items-center justify-center font-black text-white text-base font-montserrat shadow-xl border-2 border-white shrink-0 {{ $rot }}"
                                 style="background-color: {{ $isCurrentStep ? $brandColor : 'rgb(15, 23, 42)' }}">
                                {{ str_pad($stepIndex + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="flex-1 bg-white border-2 border-gray-900 p-3 shadow-[4px_4px_0px_0px_rgba(15,23,42,0.1)] max-h-[70px] overflow-hidden">
                                @php
                                    $sLen = mb_strlen($stepText);
                                    $sFontSize = 'text-[9px]';
                                    if ($sLen > 80) $sFontSize = 'text-[7px]';
                                    elseif ($sLen > 40) $sFontSize = 'text-[8px]';
                                @endphp
                                <p class="text-gray-900 {{ $sFontSize }} font-black font-mono-technical uppercase leading-tight text-center">
                                    {{ $stepText }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($index === 0)
                        <div class="bg-gray-900 p-6 border-4 border-white shadow-2xl rotate-1">
                             <span class="text-white/40 text-[8px] font-black uppercase tracking-[0.2em] mb-1 block">TECHNICAL WORKFLOW</span>
                             <h2 class="text-white text-2xl font-black font-montserrat leading-none uppercase tracking-tighter">LES ÉTAPES DU CHANTIER</h2>
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
                visualStyle="kraft"
            />
        </div>
        @endif

        @php
            $showFooter = $hasContactInfo ? ((int)$slideIndex < $finalIndex) : true;
            $footerAlpine = $slideIndex === null ? ($hasContactInfo ? "x-show=\"current < $finalIndex\"" : "") : '';
        @endphp
        @if($showFooter)
        <div {!! $footerAlpine !!} class="absolute inset-x-0 bottom-0 py-2.5 px-10 bg-gray-900 flex flex-col gap-1 border-t-4 z-30 min-h-16" style="border-color: {{ $brandColor }}">
             <div class="flex items-center justify-between">
                 <span class="text-white text-[10px] font-black font-mono-technical uppercase tracking-widest italic truncate max-w-[200px]">{{ $companyLabel }}</span>
                 <div class="flex items-center gap-2 shrink-0">
                      @for($i=0; $i<$total; $i++)
                        <div class="w-6 h-1 transition-all duration-300" 
                             @if($preview) :class="current === {{ $i }} ? '' : 'opacity-20 bg-white'" :style="current === {{ $i }} ? 'background-color: {{ $brandColor }}' : ''"
                             @else style="background-color: {{ (int)($slideIndex ?? 0) === $i ? $brandColor : 'rgba(255,255,255,0.2)' }}" @endif>
                        </div>
                      @endfor
                 </div>
             </div>
             <div class="flex items-center gap-1.5 overflow-hidden font-mono-technical">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/40 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-sm bg-white/5 text-white/40 text-[6px] font-black uppercase tracking-wider border border-white/10 whitespace-nowrap">{{ $city }}</span>
                @endif
             </div>
        </div>
        @endif

        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-40 px-2 pointer-events-none">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 pointer-events-auto border-2 border-white shadow-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center hover:bg-gray-800 pointer-events-auto border-2 border-white shadow-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
