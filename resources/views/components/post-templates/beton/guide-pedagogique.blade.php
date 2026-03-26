@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Guide Pratique',
    'slideContents' => [], // array of steps (text)
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $title = ($title === 'Guide Pratique') ? 'Le guide ultime pour choisir votre pompe à chaleur' : $title;
        $slideContents = !empty($slideContents) ? $slideContents : [
            'Comprendre le fonctionnement et les differents types de PAC.',
            'Évaluer la puissance nécessaire selon votre surface.',
            'Bénéficier des aides de l\'État pour votre installation.'
        ];
    @endphp
@endif

@php
    $total = count($slideContents) + 1; // +1 for cover
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-square bg-[#CBD5E1] relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
        {{-- Concrete Texture --}}
        <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay z-20" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>

        {{-- Cover Slide --}}
        @php
            $isCoverVisible = $slideIndex === null || $slideIndex === 0;
            $coverAlpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $coverStyle = !$isCoverVisible ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0" {!! $coverAlpine !!} style="{{ $coverStyle }}">
            <div class="flex-1 flex flex-col p-10 justify-center gap-8 relative z-10 h-full">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-white border-2 border-slate-900 flex items-center justify-center p-3 shadow-xl shrink-0">
                        <svg class="w-full h-full text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-slate-900/40 text-[8px] font-black uppercase tracking-widest mb-0.5">Guide Pratique</span>
                        <h3 class="text-slate-900 text-lg font-black font-montserrat leading-none uppercase tracking-tighter">{{ $title }}</h3>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 mt-2">
                        <div class="h-px flex-1 bg-slate-900/15"></div>
                        <span class="text-slate-900/30 text-[9px] font-black font-montserrat uppercase tracking-widest">{{ count($slideContents) }} conseils</span>
                        <div class="h-px flex-1 bg-slate-900/15"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Slides --}}
        @foreach($slideContents as $idx => $content)
            @php
                $realIdx = $idx + 1;
                $isSlideVisible = $slideIndex === null || $slideIndex === $realIdx;
                $slideAlpine = $slideIndex === null ? "x-show=\"current === $realIdx\" style=\"display: none;\"" : '';
                $slideStyle = !$isSlideVisible ? 'display: none;' : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                <div class="flex-1 flex flex-col p-10 pb-20 justify-center relative z-10 h-full">
                    <div class="bg-white border-2 border-slate-900 p-8 rounded-2xl shadow-2xl relative">
                        <div class="absolute -top-6 -left-4 w-14 h-14 rounded-xl bg-slate-900 flex items-center justify-center font-black text-white text-2xl shadow-2xl" style="background-color: {{ $brandColor }}">
                            {{ $realIdx }}
                        </div>
                        @php
                            $cLen = mb_strlen($content);
                            $cFontSize = 'text-lg';
                            if ($cLen > 150) $cFontSize = 'text-xs';
                            elseif ($cLen > 80) $cFontSize = 'text-sm';
                            elseif ($cLen > 40) $cFontSize = 'text-base';
                        @endphp
                        <div class="max-h-[180px] overflow-hidden pt-2">
                            <p class="text-slate-900 {{ $cFontSize }} font-black font-montserrat leading-tight uppercase">
                                {{ $content }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="absolute inset-x-0 bottom-0 h-16 px-10 bg-slate-900 flex items-center justify-between border-t-4 border-slate-700 z-30">
            <span class="text-white text-xs font-black font-montserrat uppercase tracking-widest">{{ $companyLabel }}</span>
            @if($total > 1)
                <div class="text-white/20 text-[10px] font-black" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                    @if(!$preview) {{ ($slideIndex ?? 0) + 1 }} / {{ $total }} @endif
                </div>
            @endif
        </div>

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
