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

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-50 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">

        {{-- Subtle dot pattern --}}
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 2px 2px, black 1px, transparent 0); background-size: 24px 24px;"></div>

        {{-- Gradient top bar --}}
        <div class="absolute inset-x-0 top-0 h-0.5 z-10" style="background: linear-gradient(90deg, {{ $brandColor }}, {{ $brandColor }}66)"></div>

        {{-- Cover Slide --}}
        @php
            $isCoverVisible = $slideIndex === null || $slideIndex === 0;
            $coverStyle = "background-color: $brandColor;" . (!$isCoverVisible ? ' display: none;' : '');
            $coverAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';

            $titleLen = mb_strlen($title ?? '');
            $fontSizeClass = 'text-2xl';
            if ($titleLen > 80) {
                $fontSizeClass = 'text-lg';
            } elseif ($titleLen > 45) {
                $fontSizeClass = 'text-xl';
            }
        @endphp
        <div class="absolute inset-0 p-10 pb-24 flex flex-col justify-center text-center" {!! $coverAlpine !!} style="{{ $coverStyle }}">
            {{-- Label --}}
            <div class="inline-flex items-center justify-center gap-1.5 mb-5">
                <span class="inline-block w-1.5 h-1.5 rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.3)' : 'rgba(255,255,255,0.5)' }}"></span>
                <p class="font-bold text-[10px] uppercase tracking-[0.2em] font-montserrat select-none" style="color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.4)' : 'rgba(255,255,255,0.5)' }}">CONSEIL D'EXPERT</p>
            </div>

            @if($title)
                <div class="max-h-[160px] overflow-hidden">
                    <h2 class="font-black {{ $fontSizeClass }} leading-tight font-montserrat" style="color: {{ $brandTextColor }}">{!! nl2br(e($title)) !!}</h2>
                </div>
            @else
                <div class="space-y-3">
                    <div class="h-2 w-full rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.3)' }}"></div>
                    <div class="h-2 w-5/6 mx-auto rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.3)' }}"></div>
                    <div class="h-2 w-4/6 mx-auto rounded-full" style="background-color: {{ $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.3)' }}"></div>
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
            <div class="absolute inset-0 p-10 pb-20 flex flex-col justify-center text-center" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                <div class="rounded-2xl shadow-xl border relative overflow-hidden" style="background: white; border-color: rgba(0,0,0,0.06); padding: 2rem 2rem 2rem">
                    {{-- Number badge --}}
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full flex items-center justify-center font-black text-sm shadow-lg" style="background-color: {{ $brandColor }}; color: {{ $brandTextColor }}">
                        {{ $realIdx }}
                    </div>
                    {{-- Top accent --}}
                    <div class="absolute top-0 inset-x-0 h-0.5" style="background: linear-gradient(90deg, transparent, {{ $brandColor }}, transparent)"></div>

                    @php
                        $cLen = mb_strlen($content);
                        $cFontSize = 'text-xl';
                        if ($cLen > 150) $cFontSize = 'text-sm';
                        elseif ($cLen > 80) $cFontSize = 'text-base';
                        elseif ($cLen > 40) $cFontSize = 'text-lg';
                    @endphp
                    <div class="max-h-[180px] overflow-hidden mt-3">
                        <p class="text-gray-800 font-black {{ $cFontSize }} tracking-tight leading-tight font-montserrat">
                            {{ $content }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Contact Slide --}}
        @php
            $contactIdx = count($slideContents) + 1;
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
            />
        </div>
        @endif

        {{-- Footer --}}
        <div class="absolute bottom-0 inset-x-0 py-2.5 flex flex-col items-center justify-center px-6 gap-1.5" style="background-color: #1E293B">
            <p class="text-white/70 font-bold tracking-widest text-[9px] font-montserrat">{{ $companyLabel }}</p>
            <div class="flex items-center gap-2">
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

        {{-- Alpine Navigation --}}
        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
