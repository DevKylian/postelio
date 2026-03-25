@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => null,
    'slideContents' => [], // array of text for each inner slide
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $title = $title ?? 'Le guide ultime pour choisir votre pompe à chaleur';
        $slideContents = !empty($slideContents) ? $slideContents : [
            'Comprendre le fonctionnement et les differents types de PAC.',
            'Évaluer la puissance nécessaire selon votre surface.',
            'Bénéficier des aides de l\'État pour votre installation.'
        ];
    @endphp
@endif

@php
    // Determine total slides. If preview without data, pretend there are 3 slides.
    $total = (!empty($slideContents)) ? count($slideContents) + 1 : 3;
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-square bg-slate-50 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
        
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 2px 2px, black 1px, transparent 0); background-size: 24px 24px;"></div>
        <div class="absolute inset-x-0 top-0 h-1.5 z-10" style="background-color: {{ $brandColor }}"></div>

        {{-- Cover Slide --}}
        @php
            $isCoverVisible = $slideIndex === null || $slideIndex === 0;
            $coverStyle = "background-color: $brandColor;" . (!$isCoverVisible ? ' display: none;' : '');
            $coverAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';
            
            // Dynamic Font Sizing for title
            $titleLen = mb_strlen($title ?? '');
            $fontSizeClass = 'text-2xl';
            if ($titleLen > 80) {
                $fontSizeClass = 'text-lg';
            } elseif ($titleLen > 45) {
                $fontSizeClass = 'text-xl';
            }
        @endphp
        <div class="absolute inset-0 p-10 pb-32 flex flex-col justify-center text-center" {!! $coverAlpine !!} style="{{ $coverStyle }}">
            <p class="text-white/40 font-bold text-[10px] uppercase tracking-[0.2em] mb-6 select-none">CONSEIL D'EXPERT</p>
            @if($title)
                <div class="max-h-[160px] overflow-hidden">
                    <h2 class="text-white font-black {{ $fontSizeClass }} uppercase leading-tight font-montserrat">{!! nl2br(e($title)) !!}</h2>
                </div>
            @else
                <div class="space-y-3">
                    <div class="h-2 w-full bg-white/30 rounded-full"></div>
                    <div class="h-2 w-5/6 mx-auto bg-white/30 rounded-full"></div>
                    <div class="h-2 w-4/6 mx-auto bg-white/30 rounded-full"></div>
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
                <div class="bg-white/90 backdrop-blur-md p-10 rounded-3xl shadow-2xl border border-blue-100 relative">
                     <div class="absolute -top-5 left-1/2 -translate-x-1/2 w-10 h-10 rounded-full flex items-center justify-center text-white font-black text-lg shadow-xl" style="background-color: {{ $brandColor }}">
                         {{ $realIdx }}
                     </div>
                     @php
                         $cLen = mb_strlen($content);
                         $cFontSize = 'text-xl';
                         if ($cLen > 150) $cFontSize = 'text-sm';
                         elseif ($cLen > 80) $cFontSize = 'text-base';
                         elseif ($cLen > 40) $cFontSize = 'text-lg';
                     @endphp
                     <div class="max-h-[180px] overflow-hidden">
                         <p class="text-[#1E293B] font-black {{ $cFontSize }} uppercase tracking-tight leading-tight font-montserrat">
                             {{ $content }}
                         </p>
                     </div>
                </div>
            </div>
        @endforeach

        {{-- Footer --}}
        <div class="absolute bottom-0 inset-x-0 h-12 flex items-center justify-center px-6" style="background-color: #1E293B">
            <p class="text-white/60 font-bold uppercase tracking-widest text-[9px] font-montserrat">{{ $companyLabel }}</p>
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
