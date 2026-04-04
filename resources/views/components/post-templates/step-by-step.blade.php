@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'slides' => [],
    'slideContents' => [],
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
        $slides = [
            null,
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?q=80&w=800',
            'https://images.unsplash.com/photo-1517646287270-a5a067f920f0?q=80&w=800',
            'https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?q=80&w=800'
        ];
        $slideContents = [
            'Comment nous réalisons votre isolation par l\'extérieur',
            'Analyse de la structure existante et préparation des supports.',
            'Mise en œuvre des panneaux isolants avec fixation mécanique.',
            'Application de l\'enduit de finition et contrôle qualité.'
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
    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-100 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
        
        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || $slideIndex === $index;
                $slideCondition = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : ($isSlideVisible ? '' : 'style="display: none;"');
                
                $slideInlineStyle = !$isSlideVisible ? 'display: none;' : '';
                if ($index === 0) {
                    $slideInlineStyle .= " background-color: $brandColor;";
                }
            @endphp
            
            <div class="absolute inset-0 {{ $index === 0 ? 'p-10 flex flex-col justify-center' : '' }}" {!! $slideCondition !!} style="{{ $slideInlineStyle }}">
                @if($index === 0)
                    <p class="font-black text-4xl mb-2 select-none tracking-tighter uppercase leading-none opacity-30" style="color: {{ $brandTextColor }}">ÉTAPE PAR ÉTAPE</p>
                    @php
                        $coverText = $slideContents[0] ?? '';
                    @endphp
                    @if(!empty($coverText))
                        @php
                            $cLen = mb_strlen($coverText);
                            $cFontSize = 'text-lg';
                            if ($cLen > 80) $cFontSize = 'text-sm';
                            elseif ($cLen > 40) $cFontSize = 'text-base';
                        @endphp
                        <div class="mt-4 backdrop-blur-sm p-4 rounded-2xl border max-h-[140px] overflow-hidden" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.1)' }}; border-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }};">
                            <p class="font-black {{ $cFontSize }} leading-tight font-montserrat" style="color: {{ $brandTextColor }}">{{ $coverText }}</p>
                        </div>
                    @else
                        <div class="space-y-2 mt-4">
                            <div class="h-1.5 w-full rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}"></div>
                            <div class="h-1.5 w-2/3 rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}"></div>
                        </div>
                    @endif
                @else
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-cover">
                    @else
                        <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-300 flex items-center justify-center font-bold text-gray-500/30 select-none">
                            <div class="text-center">
                                <svg class="w-10 h-10 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <p class="text-[10px] font-bold uppercase tracking-widest leading-none text-gray-500">ÉTAPE {{ sprintf('%02d', $index) }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="absolute top-6 left-6 w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg shadow-lg z-10" style="background-color: {{ $brandColor }}; color: {{ $brandTextColor }}; border: 1px solid {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }};">
                        {{ $index }}
                    </div>

                    @php
                        $stepText = $slideContents[$index] ?? '';
                    @endphp
                    @if(!empty($stepText) && !$imageUrl)
                        @php
                            $sLen = mb_strlen($stepText);
                            $sFontSize = 'text-xs';
                            if ($sLen > 150) $sFontSize = 'text-[9px]';
                            elseif ($sLen > 80) $sFontSize = 'text-[10px]';
                        @endphp
                        <div class="absolute inset-x-6 bottom-16 bg-black/80 backdrop-blur-md p-4 rounded-2xl border border-white/20 shadow-2xl max-h-[100px] overflow-hidden shrink-0">
                            <p class="text-white font-black {{ $sFontSize }} leading-tight font-montserrat">{{ $stepText }}</p>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach

        {{-- Contact Slide --}}
        @php
            $contactIndex = count($slides);
            $isContactVisible = $slideIndex === null || (int)$slideIndex === $contactIndex;
            $contactCondition = $slideIndex === null 
                ? "x-show=\"current === $contactIndex\"" . ' style="display: none;"'
                : ($isContactVisible ? '' : 'style="display: none;"');
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-0" {!! $contactCondition !!} style="{{ !$isContactVisible ? 'display: none;' : '' }}">
            <x-post-templates.contact-slide 
                :brand-color="$brandColor" 
                :company-label="$companyLabel"
                :phone="$phone"
                :email="$email"
                :website="$website"
                :trade="$trade"
                :city="$city"
                :brand-text-color="$brandTextColor"
            />
        </div>
        @endif

        @if($preview)
            <div class="absolute bottom-0 inset-x-0 h-10 flex items-center justify-center px-4" :style="current === 0 ? 'background-color: rgba(0,0,0,0.2)' : 'background-color: transparent'">
                <p class="font-bold text-[9px] tracking-widest font-montserrat" :class="current === 0 || current === {{ $contactIndex }} ? 'text-white/60' : 'text-white'" x-show="current !== {{ $contactIndex }}">{{ $companyLabel }}</p>
            </div>
            
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @else
            @php
                $footerStyle = ($slideIndex === 0) ? 'background-color: rgba(0,0,0,0.2);' : 'background-color: transparent;';
            @endphp
            @php
                $showFooter = $hasContactInfo ? ((int)$slideIndex !== $contactIndex) : true;
            @endphp
            @if($showFooter)
            <div class="absolute bottom-0 inset-x-0 h-14 flex flex-col items-center justify-center px-4" style="{{ $footerStyle }}">
                <p class="font-bold text-[9px] tracking-widest font-montserrat truncate mb-1 {{ ($slideIndex === 0) ? 'text-white/60' : 'text-white' }}">{{ $companyLabel }}</p>
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
            @endif
        @endif
    </div>
</div>
