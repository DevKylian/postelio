@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Guide de Projet',
    'slideContents' => [], // array of steps (text)
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $title = ($title === 'Guide de Projet') ? 'Le guide ultime pour choisir votre pompe à chaleur' : $title;
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
    <div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
        
        <div class="absolute inset-0 opacity-10 pointer-events-none" 
             style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
        </div>

        {{-- Cover Slide --}}
        @php
            $isCoverVisible = $slideIndex === null || $slideIndex === 0;
            $coverAlpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $coverStyle = !$isCoverVisible ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0" {!! $coverAlpine !!} style="{{ $coverStyle }}">
            <div class="flex-1 flex flex-col p-10 pb-20 justify-center gap-6 relative z-10 h-full">
                <div class="flex items-center gap-6 mb-2">
                     <div class="w-14 h-14 border-2 border-white bg-white/10 flex items-center justify-center p-3 shadow-xl shrink-0">
                         <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                         </svg>
                     </div>
                     <div class="bg-white p-4 shadow-xl border-t-2 border-t-[#003380] flex-1">
                         <h3 class="text-[#0047AB] text-[16px] font-black font-mono-technical leading-tight uppercase tracking-tighter">{{ $title }}</h3>
                     </div>
                </div>

                <div class="space-y-3">
                    @foreach(array_slice($slideContents, 0, 3) as $idx => $item)
                        <div class="flex items-center gap-4 bg-white p-4 shadow-lg ring-1 ring-black/5 {{ $idx % 2 == 0 ? 'translate-x-3' : '' }}">
                            <div class="w-2 h-2" style="background-color: {{ $idx == 0 ? $brandColor : '#e2e8f0' }}"></div>
                            <p class="text-[#0047AB] text-[10px] font-black font-mono-technical uppercase tracking-tight">{{ $item }}</p>
                        </div>
                    @endforeach
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
                    <div class="bg-white p-10 shadow-2xl relative border-t-2 border-t-[#003380]">
                         <div class="absolute -top-3 -left-3 w-8 h-8 bg-[#0047AB] border-2 border-white flex items-center justify-center font-black text-white font-mono-technical shadow-xl text-sm">
                             {{ $realIdx }}
                         </div>
                         @php
                            $cLen = mb_strlen($content);
                            $cFontSize = 'text-[14px]';
                            if ($cLen > 150) $cFontSize = 'text-[9px]';
                            elseif ($cLen > 80) $cFontSize = 'text-[11px]';
                         @endphp
                         <div class="max-h-[220px] overflow-hidden">
                             <p class="text-[#0047AB] {{ $cFontSize }} font-black font-mono-technical leading-snug uppercase tracking-tight">
                                 {{ $content }}
                             </p>
                         </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="absolute inset-x-0 bottom-0 h-14 px-10 bg-white border-t-2 border-[#0047AB] flex items-center justify-between z-30">
             <span class="text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest italic">{{ $companyLabel }}</span>
             @if($total > 1)
                <div class="text-[#0047AB]/40 text-[9px] font-black font-mono-technical" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                    @if(!$preview) {{ ($slideIndex ?? 0) + 1 }} / {{ $total }} @endif
                </div>
             @endif
        </div>

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
