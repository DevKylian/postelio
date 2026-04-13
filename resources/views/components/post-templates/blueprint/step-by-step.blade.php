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
    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-[#0047AB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
        
        <div class="absolute inset-0 opacity-10 pointer-events-none" 
             style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
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
                    </div>
                @endif

                <div class="absolute inset-0 flex flex-col p-8 pb-16 justify-center gap-3 z-10">
                    @foreach($slideContents as $stepIndex => $stepText)
                        @php
                            $isCurrentStep = $index === ($stepIndex + 1);
                            $isPastStep = $index > ($stepIndex + 1);
                        @endphp
                        <div class="flex items-center gap-4 group/step transition-all duration-300 {{ $isCurrentStep ? 'scale-105 translate-x-2' : ($isPastStep ? 'opacity-40' : 'opacity-10') }}">
                            <div class="w-8 h-8 border-2 border-[#0047AB] bg-white flex items-center justify-center font-black text-[#0047AB] text-[10px] font-mono-technical shadow-lg shrink-0"
                                 style="{{ $isCurrentStep ? 'background-color: '.$brandColor.'; color: white; border-color: white;' : '' }}">
                                {{ sprintf('%02d', $stepIndex + 1) }}
                            </div>
                            <div class="flex-1 bg-white p-3 shadow-xl border-t border-blue-100 max-h-[70px] overflow-hidden {{ $isCurrentStep ? 'border-l-4' : '' }}"
                                 style="{{ $isCurrentStep ? 'border-left-color: '.$brandColor : '' }}">
                                @php
                                    $sLen = mb_strlen($stepText);
                                    $sFontSize = 'text-[9px]';
                                    if ($sLen > 80) $sFontSize = 'text-[7px]';
                                    elseif ($sLen > 40) $sFontSize = 'text-[8px]';
                                @endphp
                                <p class="text-[#0047AB] {{ $sFontSize }} font-black font-mono-technical uppercase leading-tight">{{ $stepText }}</p>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($index === 0)
                        <div class="mt-4 bg-white p-5 shadow-2xl border-t-2 border-t-[#003380] relative">
                             <span class="text-[#0047AB]/40 text-[7px] font-black font-mono-technical uppercase tracking-[0.3em] block mb-0.5">PROGRAMME TECHNIQUE</span>
                             <h2 class="text-[#0047AB] text-base font-black font-mono-technical leading-none uppercase tracking-tighter">LES ÉTAPES DU PROJET</h2>
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
                visualStyle="blueprint"
            />
        </div>
        @endif

        @php
            $showFooter = $hasContactInfo ? ((int)$slideIndex < $finalIndex) : true;
            $footerAlpine = $slideIndex === null ? ($hasContactInfo ? "x-show=\"current < $finalIndex\"" : '') : '';
        @endphp
        @if($showFooter)
        <div {!! $footerAlpine !!} class="absolute bottom-0 inset-x-0 py-2.5 bg-white px-10 border-t-2 border-[#0047AB] flex flex-col gap-1 z-30 min-h-14">
             <div class="flex items-center justify-between">
                 <span class="text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest italic truncate max-w-[200px]">{{ $companyLabel }}</span>
                 <div class="flex items-center gap-3">
                      @for($i=0; $i<$total; $i++)
                        <div class="w-4 h-1 border border-[#0047AB]" 
                             @if($preview) :class="current === {{ $i }} ? '' : 'opacity-20'" :style="current === {{ $i }} ? 'background-color: {{ $brandColor }}' : ''"
                             @else style="background-color: {{ (int)($slideIndex ?? 0) === $i ? $brandColor : 'rgba(0, 71, 171, 0.1)' }}" @endif>
                        </div>
                      @endfor
                 </div>
             </div>
             <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/50 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/50 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $city }}</span>
                @endif
             </div>
        </div>
        @endif

        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-40">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-blue-900/50 text-white flex items-center justify-center hover:bg-blue-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-blue-900/50 text-white flex items-center justify-center hover:bg-blue-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
