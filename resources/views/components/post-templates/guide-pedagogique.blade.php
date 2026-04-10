@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => '',
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
])

@if($preview)
    @php
        $title = $title ?? 'Le guide ultime pour choisir votre pompe à chaleur';
        $slideContents = !empty($slideContents) ? $slideContents : [
            'Comprendre le fonctionnement et les differents types de PAC.',
            'Évaluer la puissance nécessaire selon votre surface.',
            'Bénéficier des aides de l\'État pour votre installation.'
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
    $total = (!empty($slideContents)) ? count($slideContents) + 1 + ($hasContactInfo ? 1 : 0) : (4 + ($hasContactInfo ? 1 : 0));
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-slate-50 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">

        {{-- Background Sophistication --}}
        <div class="absolute inset-0 opacity-[0.05]" style="background-image: radial-gradient(circle at 2px 2px, #000 1px, transparent 0); background-size: 32px 32px;"></div>
        
        {{-- Cover Slide --}}
        @php
            $isCoverVisible = $slideIndex === null || $slideIndex === 0;
            $coverStyle = "background-color: $brandColor;" . (!$isCoverVisible ? ' display: none;' : '');
            $coverAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';

            $titleLen = mb_strlen($title ?? '');
            $fontSizeClass = 'text-3xl';
            if ($titleLen > 80) {
                $fontSizeClass = 'text-lg';
            } elseif ($titleLen > 45) {
                $fontSizeClass = 'text-2xl';
            }
        @endphp
        <div class="absolute inset-0 p-10 pb-24 flex flex-col justify-center text-center overflow-hidden" {!! $coverAlpine !!} style="{{ $coverStyle }}">
            {{-- Ambient glows --}}
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full blur-[100px] pointer-events-none opacity-50" style="background: radial-gradient(circle, {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.3)' }}, transparent 70%)"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full blur-[100px] pointer-events-none opacity-30" style="background: radial-gradient(circle, {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.05)' : 'rgba(0,0,0,0.2)' }}, transparent 70%)"></div>

            {{-- Label --}}
            <div class="inline-flex items-center justify-center gap-2 mb-6 relative z-10">
                <span class="w-2 h-2 rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.2)' : 'rgba(255,255,255,0.5)' }}"></span>
                <p class="font-black text-[10px] uppercase tracking-[0.3em] font-montserrat select-none" style="color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.3)' : 'rgba(255,255,255,0.4)' }}">GUIDE ARTISAN</p>
            </div>

            @if($title)
                {{-- Glass Title Card --}}
                <div class="relative z-10 backdrop-blur-xl bg-white/10 border border-white/20 p-8 rounded-3xl shadow-2xl overflow-hidden">
                    <h2 class="font-black {{ $fontSizeClass }} leading-tight font-montserrat tracking-tight" style="color: {{ $brandTextColor }}">{!! nl2br(e($title)) !!}</h2>
                </div>
            @else
                <div class="space-y-4">
                    <div class="h-2 w-full rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}"></div>
                    <div class="h-2 w-5/6 mx-auto rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.2)' }}"></div>
                </div>
            @endif
        </div>

        {{-- Content Slides --}}
        @foreach($slideContents as $idx => $content)
            @php
                $realIdx = $idx + 1;
                $isSlideVisible = $slideIndex === null || $slideIndex === $realIdx;
                $slideStyle = !$isSlideVisible ? 'display: none;' : '';
                $slideAlpine = $slideIndex === null ? "x-show=\"current === $realIdx\" style=\"display: none;\"" : '';
            @endphp
            <div class="absolute inset-0 p-8 pb-32 flex flex-col justify-center text-center overflow-hidden" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                {{-- Ambient glows for slides --}}
                <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full blur-[100px] pointer-events-none opacity-20" style="background: radial-gradient(circle, {{ $brandColor }}, transparent 70%)"></div>

                <div class="backdrop-blur-xl bg-white/95 rounded-3xl p-8 shadow-2xl border border-white relative z-10">
                    {{-- Indicator --}}
                    <div class="w-10 h-10 rounded-2xl flex flex-col items-center justify-center shadow-xl mx-auto mb-6 bg-slate-900 border border-slate-800">
                         <span class="text-[8px] font-black font-montserrat text-slate-500 leading-none mb-1 uppercase">Step</span>
                         <span class="font-black text-sm font-montserrat text-white leading-none">{{ sprintf('%02d', $realIdx) }}</span>
                    </div>

                    @php
                        $cLen = mb_strlen($content);
                        $cFontSize = 'text-xl';
                        if ($cLen > 150) $cFontSize = 'text-sm';
                        elseif ($cLen > 80) $cFontSize = 'text-base';
                    @endphp
                    <div class="max-h-[200px] overflow-hidden">
                        <p class="text-slate-800 font-black {{ $cFontSize }} tracking-tight leading-tight font-montserrat">
                            {{ $content }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Contact Slide --}}
        @foreach([count($slideContents) + 1] as $contactIdx)
        @php
            $isContactVisible = $slideIndex === null || (int)$slideIndex === $contactIdx;
            $contactAlpine = $slideIndex === null ? "x-show=\"current === $contactIdx\" style=\"display: none;\"" : '';
            $contactStyle = (int)$slideIndex !== $contactIdx && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-0 bg-white" {!! $contactAlpine !!} style="{{ $contactStyle }}">
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
        @endforeach

        {{-- Branding Overlay (Integrated Glass) --}}
        @php
            $footerAlpine = $slideIndex === null ? "x-show=\"current < $contactIdx\"" : '';
            $showFooter = $hasContactInfo ? ($slideIndex === null || (int)$slideIndex < $contactIdx) : true;
        @endphp
        @if($showFooter && !$exporting)
        <div class="absolute bottom-6 inset-x-0 mx-auto w-[90%] z-20" {!! $footerAlpine !!}>
            <div class="backdrop-blur-xl rounded-3xl px-4 py-3.5 shadow-2xl border border-white/10 flex flex-col items-center"
                 style="background-color: {{ $accentColor }}d9;">
                <div class="flex items-center gap-2 mb-2.5">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-3 w-3 object-contain rounded opacity-80 shrink-0 {{ $accentTextColor === '#FFFFFF' ? 'brightness-0 invert' : '' }}">
                    @endif
                    <p class="font-black tracking-[0.2em] text-[7px] uppercase truncate leading-none" style="color: {{ $accentTextColor }}bf">{{ $companyLabel }}</p>
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
                        <span class="px-2 py-0.5 rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap" style="{{ $tagStyle }}">{{ $trade }}</span>
                    @endif
                    @if($city)
                        <span class="px-2 py-0.5 rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap" style="{{ $tagStyle }}">{{ $city }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Alpine Navigation --}}
        @if($preview && !$exporting)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
