@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Guide Pratique',
    'slideContents' => [], // array of steps (text)
    'preview' => false,
    'slideIndex' => null,
])

@php
    $total = count($slideContents) + 1; // +1 for cover
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-square bg-slate-900 flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
        
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient({{ $brandColor }} 2px, transparent 2px); background-size: 40px 40px;"></div>
        </div>

        {{-- Cover Slide --}}
        @php
            $isCoverVisible = $slideIndex === null || $slideIndex === 0;
            $coverAlpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $coverStyle = !$isCoverVisible ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0" {!! $coverAlpine !!} style="{{ $coverStyle }}">
            <div class="flex-1 flex flex-col p-10 justify-center gap-6 relative z-10 h-full">
                <div class="flex items-center gap-4 mb-4">
                     <div class="w-14 h-14 rounded-2xl backdrop-blur-2xl bg-white/10 border border-white/20 flex items-center justify-center shadow-2xl">
                         <svg class="w-7 h-7" style="color: {{ $brandColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                         </svg>
                     </div>
                     <div>
                         <span class="text-white/40 text-[10px] font-black uppercase tracking-[0.3em] block font-montserrat">Guide d'Expert</span>
                         <h3 class="text-white text-xl font-bold leading-tight uppercase tracking-tight">{{ $title }}</h3>
                     </div>
                </div>

                <div class="space-y-3">
                    @foreach(array_slice($slideContents, 0, 3) as $idx => $item)
                        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4 transition-colors shadow-lg {{ $idx % 2 == 0 ? 'translate-x-3' : '' }}">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $idx == 0 ? $brandColor : 'rgba(255,255,255,0.2)' }}"></div>
                            <p class="text-white/90 text-sm font-medium uppercase tracking-tight">{{ $item }}</p>
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
                <div class="flex-1 flex flex-col p-12 justify-center relative z-10 h-full">
                    <div class="backdrop-blur-3xl bg-white/5 border border-white/10 p-10 rounded-[32px] shadow-2xl relative">
                        <div class="absolute -top-6 -left-6 w-16 h-16 rounded-2xl backdrop-blur-2xl bg-white/10 flex items-center justify-center font-black text-white text-2xl shadow-2xl border border-white/20" style="background-color: {{ $brandColor }}">
                            {{ $realIdx }}
                        </div>
                        @php
                            $cLen = mb_strlen($content);
                            $cFontSize = 'text-xl';
                            if ($cLen > 150) $cFontSize = 'text-sm';
                            elseif ($cLen > 80) $cFontSize = 'text-base';
                            elseif ($cLen > 40) $cFontSize = 'text-lg';
                        @endphp
                        <div class="max-h-[200px] overflow-hidden">
                            <p class="text-white {{ $cFontSize }} font-bold leading-snug uppercase pt-4 tracking-tight">
                                {{ $content }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="absolute inset-x-0 bottom-0 h-16 px-10 backdrop-blur-md bg-white/5 flex items-center justify-between border-t border-white/10 z-30">
             <span class="text-white/30 text-[9px] font-black uppercase tracking-[0.3em] font-montserrat">{{ $companyLabel }}</span>
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
