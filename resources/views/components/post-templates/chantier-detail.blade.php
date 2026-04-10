@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Détails du chantier',
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
])

@if($preview)
    @php
        $title = 'Focus sur l\'installation du système de chauffage';
        $slides = !empty($slides) ? $slides : [
            'https://images.unsplash.com/photo-1585314062340-f1a5a7c9328d?q=80&w=800',
            'https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?q=80&w=800',
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800'
        ];
        $slideContents = !empty($slideContents) ? $slideContents : [
            'Préparation technique et traçage des réseaux.',
            'Installation des collecteurs et mise en pression.',
            'Finalisation des raccordements et test de chauffe.'
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

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-800 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">

        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || $slideIndex === $index;
                $slideAlpine = $slideIndex === null
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : '';
                $slideStyle = !$isSlideVisible ? 'display: none;' : '';
                $currentText = ($index === 0) ? ($slideContents[0] ?? $title) : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                @if ($imageUrl)
                    <img src="{{ $imageUrl }}" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-cover">
                @else
                    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] flex items-center justify-center {{ $index % 2 === 0 ? 'bg-gray-600' : 'bg-gray-500' }}">
                         <div class="text-center">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <p class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em]">Photo {{ $index + 1 }}</p>
                         </div>
                    </div>
                @endif

                {{-- Gradient top bar on every slide --}}
                <div class="absolute top-0 inset-x-0 h-0.5 z-10" style="background: linear-gradient(90deg, {{ $brandColor }}, {{ $brandColor }}55)"></div>

                @php
                    $slideText = $slideContents[$index] ?? (($index === 0) ? $title : '');
                @endphp

                @if(!empty($slideText) && $index === 0)
                    @php
                        $sLen = mb_strlen($slideText);
                        $sFontSize = 'text-[11px]';
                        if ($sLen > 120) $sFontSize = 'text-[7px]';
                        elseif ($sLen > 60) $sFontSize = 'text-[9px]';
                    @endphp
                    <div class="absolute inset-x-4 bottom-20 backdrop-blur-sm p-4 rounded-xl border shadow-xl" style="background: rgba(0,0,0,0.65); border-color: rgba(255,255,255,0.08)">
                        <div class="max-h-[60px] overflow-hidden">
                            <p class="text-white font-black {{ $sFontSize }} leading-snug font-montserrat text-center">{{ $slideText }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Contact Slide --}}
        @php
            $contactIdx = count($slides);
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

        {{-- Brand bar (Glass Overlay) --}}
        @php
            $showBrandBar = $slideIndex === null || (int)$slideIndex < $contactIdx;
            $brandBarAlpine = $slideIndex === null ? "x-show=\"current < $contactIdx\"" : '';

            // Contrast for tags & badge
            $hex = str_replace('#', '', $brandColor);
            if(strlen($hex) == 3) {
                $bR = hexdec(substr($hex,0,1).substr($hex,0,1));
                $bG = hexdec(substr($hex,1,1).substr($hex,1,1));
                $bB = hexdec(substr($hex,2,1).substr($hex,2,1));
            } else {
                $bR = hexdec(substr($hex,0,2));
                $bG = hexdec(substr($hex,2,2));
                $bB = hexdec(substr($hex,4,2));
            }
            $bYiq = (($bR * 299) + ($bG * 587) + ($bB * 114)) / 1000;
            $badgeTextCol = ($bYiq >= 186) ? '#1E293B' : '#FFFFFF';
            $isDarkBrand  = $bYiq < 120;
            if ($isDarkBrand) {
                $tagBg = 'rgba(255,255,255,0.1)'; $tagBorder = 'rgba(255,255,255,0.2)'; $tagText = 'rgba(255,255,255,0.9)';
            } else {
                $tagBg = $brandColor . '25'; $tagBorder = $brandColor . '40'; $tagText = $brandColor;
            }
            $tagStyle = "background-color: $tagBg; border-color: $tagBorder; color: $tagText;";
        @endphp
        <div class="absolute bottom-5 inset-x-0 mx-auto w-[85%] z-20" {!! $brandBarAlpine !!} style="{{ !$showBrandBar ? 'display:none;' : '' }}">
            <div class="backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden" 
                 style="background-color: {{ $accentColor }}cc; border: 1px solid rgba(255,255,255,0.1); border-top: 2px solid {{ $brandColor }}">
                <div class="px-4 py-2.5 flex items-center justify-between gap-3">
                    {{-- Left: logo + company --}}
                    <div class="flex items-center gap-2 min-w-0">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded opacity-80 shrink-0 {{ $accentTextColor === '#FFFFFF' ? 'brightness-0 invert' : '' }}">
                        @else
                            <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $brandColor }}"></div>
                        @endif
                        <p class="font-black tracking-[0.18em] text-[7px] uppercase truncate leading-none" style="color: {{ $accentTextColor }}bf">{{ $companyLabel }}</p>
                    </div>
                    {{-- Right: tags --}}
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($trade)
                            <span class="px-2 h-4 inline-flex items-center justify-center rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap leading-none" style="{{ $tagStyle }}">{{ $trade }}</span>
                        @endif
                        @if($city)
                            <span class="px-2 h-4 inline-flex items-center justify-center rounded-md text-[6px] font-black uppercase tracking-[0.05em] border whitespace-nowrap leading-none" style="{{ $tagStyle }}">{{ $city }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($preview && !$exporting)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-5 right-5 text-[10px] font-bold px-3 py-1.5 rounded-full z-30 border shadow-xl pointer-events-none" style="background-color: {{ $brandColor }}; color: {{ $badgeTextCol }}; border-color: rgba(255,255,255,0.2);" x-text="(current + 1) + ' / ' + total"></div>
        @else
            @if($total > 1 && !$exporting)
                <div class="absolute top-5 right-5 text-[10px] font-bold px-3 py-1.5 rounded-full z-30 border shadow-xl pointer-events-none" style="background-color: {{ $brandColor }}; color: {{ $badgeTextCol }}; border-color: rgba(255,255,255,0.2);">
                    {{ ($slideIndex ?? 0) + 1 }} / {{ $total }}
                </div>
            @endif
        @endif
    </div>
