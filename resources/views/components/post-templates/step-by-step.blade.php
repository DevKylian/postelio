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
    'accentColor' => '#0F172A',
    'accentTextColor' => '#FFFFFF',
    'exporting' => false,
    'aspectRatio' => '4:5',
])

@if($preview)
    @php
        $slides = !empty($slides) ? $slides : [
            null,
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?q=80&w=800',
            'https://images.unsplash.com/photo-1517646287270-a5a067f920f0?q=80&w=800',
            'https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?q=80&w=800'
        ];
        $slideContents = !empty($slideContents) ? $slideContents : [
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

<div {!! $alpineData !!} class="w-full {{ ($aspectRatio ?? '4:5') === '4:5' ? 'aspect-[4/5]' : 'aspect-square' }} bg-gray-100 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">

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

            <div class="absolute inset-0 {{ $index === 0 ? 'p-10 flex flex-col justify-center overflow-hidden' : '' }}" {!! $slideCondition !!} style="{{ $slideInlineStyle }}">
                @if($index === 0)
                    {{-- Cover: sophisticated ambient glows --}}
                    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full blur-[100px] pointer-events-none opacity-50" style="background: radial-gradient(circle, {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.2)' : 'rgba(255,255,255,0.3)' }}, transparent 70%)"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full blur-[100px] pointer-events-none opacity-30" style="background: radial-gradient(circle, {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(0,0,0,0.2)' }}, transparent 70%)"></div>

                    {{-- Cover label --}}
                    <div class="inline-flex items-center gap-2 mb-6 relative z-10">
                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.2)' : 'rgba(255,255,255,0.5)' }}"></span>
                        <p class="font-black text-[10px] uppercase tracking-[0.3em] font-montserrat leading-none" style="color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.3)' : 'rgba(255,255,255,0.4)' }}">GUIDE ARTISAN</p>
                    </div>

                    @php
                        $coverText = $slideContents[0] ?? '';
                    @endphp
                    @if(!empty($coverText))
                        @php
                            $cLen = mb_strlen($coverText);
                            $cFontSize = 'text-3xl';
                            if ($cLen > 80) $cFontSize = 'text-xl';
                            elseif ($cLen > 40) $cFontSize = 'text-2xl';
                        @endphp
                        {{-- Glass Card for Title --}}
                        <div class="relative z-10 backdrop-blur-xl bg-white/10 border border-white/20 p-8 rounded-3xl shadow-2xl overflow-hidden">
                            <h2 class="font-black {{ $cFontSize }} leading-tight font-montserrat tracking-tight" style="color: {{ $brandTextColor }}">{{ $coverText }}</h2>
                        </div>
                    @else
                        <div class="space-y-3 mt-4">
                            <div class="h-2 w-full rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}"></div>
                            <div class="h-2 w-2/3 rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}"></div>
                        </div>
                    @endif
                @else
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" class="w-full {{ ($aspectRatio ?? '4:5') === '4:5' ? 'aspect-[4/5]' : 'aspect-square' }} object-cover">
                    @else
                        <div class="w-full {{ ($aspectRatio ?? '4:5') === '4:5' ? 'aspect-[4/5]' : 'aspect-square' }} bg-slate-900 flex items-center justify-center">
                            <div class="text-center opacity-20">
                                <svg class="w-16 h-16 mx-auto mb-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-white">ÉTAPE {{ sprintf('%02d', $index) }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Step number glass badge --}}
                    <div class="absolute top-6 left-6 w-12 h-12 rounded-2xl flex flex-col items-center justify-center shadow-2xl z-20 backdrop-blur-xl bg-white/90 border border-white/20">
                        <span class="text-[8px] font-black font-montserrat text-slate-400 leading-none mb-1.5 uppercase tracking-wider">Step</span>
                        <span class="font-black text-xl font-montserrat text-slate-950 leading-none">{{ sprintf('%02d', $index) }}</span>
                    </div>

                    {{-- Subtle top indicator --}}
                    <div class="absolute top-0 inset-x-0 h-1 z-20" style="background: linear-gradient(90deg, {{ $brandColor }}, transparent)"></div>

                    @php
                        $stepText = $slideContents[$index] ?? '';
                    @endphp
                    @if(!empty($stepText))
                        @php
                            $sLen = mb_strlen($stepText);
                            $sFontSize = 'text-base';
                            if ($sLen > 150) $sFontSize = 'text-xs';
                            elseif ($sLen > 80) $sFontSize = 'text-sm';
                        @endphp
                        {{-- Glass Card for Step Content --}}
                        <div class="absolute inset-x-6 bottom-16 backdrop-blur-xl bg-black/40 p-6 rounded-2xl border border-white/10 shadow-2xl z-20">
                            <p class="text-white font-black {{ $sFontSize }} leading-snug font-montserrat tracking-tight">{!! nl2br(e($stepText)) !!}</p>
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
                :accent-color="$accentColor"
                :accent-text-color="$accentTextColor"
            />
        </div>
        @endif

        {{-- Branding Footer (Refined Glass Overlay) --}}
        @php
            $footerAlpine = $slideIndex === null ? "x-show=\"current < $contactIndex\"" : '';
            $showFooter = $hasContactInfo ? ($slideIndex === null || (int)$slideIndex < $contactIndex) : true;
        @endphp
        @if($showFooter && !$exporting)
        <div class="absolute bottom-6 inset-x-0 mx-auto w-[90%] z-20" {!! $footerAlpine !!}>
            <div class="backdrop-blur-xl rounded-2xl px-4 py-3.5 shadow-2xl border border-white/10 flex flex-col items-center"
                 style="background-color: {{ $accentColor }}d9;">
                <div class="flex items-center gap-2 mb-2.5">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-3 w-3 object-contain rounded opacity-80 shrink-0 {{ $accentTextColor === '#FFFFFF' ? 'brightness-0 invert' : '' }}">
                    @endif
                    @php
                        $wrappedLabel = mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel;
                    @endphp
                    <p class="font-black tracking-[0.18em] text-[7px] uppercase whitespace-normal leading-tight" style="color: {{ $accentTextColor }}bf">{!! $wrappedLabel !!}</p>
                </div>
                
                <div class="flex items-center gap-1.5 overflow-hidden">
                    @php
                        $hex = str_replace('#', '', $brandColor);
                        if(strlen($hex) == 3) {
                            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
                        } else {
                            $r = hexdec(substr($hex,0,2));
                            $g = hexdec(substr($hex,2,2));
                            $b = hexdec(substr($hex,4,2));
                        }
                        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
                        $isDarkBrand = $yiq < 120;

                        if ($isDarkBrand) {
                            $tagBg = 'rgba(255,255,255,0.1)';
                            $tagBorder = 'rgba(255,255,255,0.2)';
                            $tagText = 'rgba(255,255,255,0.9)';
                        } else {
                            $tagBg = $brandColor . '25';
                            $tagBorder = $brandColor . '40';
                            $tagText = $brandColor;
                        }
                        $tagStyle = "background-color: $tagBg; border-color: $tagBorder; color: $tagText;";
                    @endphp
                    @if($trade)
                        <span class="px-2 h-4 inline-flex items-center justify-center rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap leading-none" style="{{ $tagStyle }}">{{ $trade }}</span>
                    @endif
                    @if($city)
                        <span class="px-2 h-4 inline-flex items-center justify-center rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap leading-none" style="{{ $tagStyle }}">{{ $city }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($preview && !$exporting)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-6 right-6 bg-black/40 backdrop-blur-md text-white text-[10px] font-black px-3 py-1.5 rounded-full z-30 border border-white/10 shadow-xl" x-text="(current + 1) + ' / ' + total"></div>
        @else
            @if($total > 1 && !$exporting)
                 <div class="absolute top-6 right-6 bg-black/40 backdrop-blur-md text-white text-[10px] font-black px-3 py-1.5 rounded-full z-30 border border-white/10 shadow-xl">
                    {{ ($slideIndex ?? 0) + 1 }} / {{ $total }}
                </div>
            @endif
        @endif
    </div>

