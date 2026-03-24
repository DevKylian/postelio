@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Zoom Chantier',
    'slides' => [null, null, null, null], // array of image URLs
    'slideContents' => [], // array of texts corresponding to slides
    'preview' => false,
    'slideIndex' => null, 
])

@php
    $total = count($slides);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-square bg-[#BC9A7E] relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
        {{-- Kraft Texture Overlay --}}
        <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply z-20" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>

        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || $slideIndex === $index;
                $slideAlpine = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : '';
                $slideStyle = !$isSlideVisible ? 'display: none;' : '';
                $currentText = ($index === 0) ? ($slideContents[0] ?? $title) : ($slideContents[$index] ?? '');
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                <div class="absolute inset-0 p-8">
                    <div class="w-full h-full relative overflow-hidden border-4 border-slate-900 bg-slate-800 shadow-lg {{ $index % 2 == 0 ? '-rotate-1' : 'rotate-1' }}">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-700">
                                <p class="text-[10px] font-bold text-white/20 uppercase tracking-[0.2em]">Photo {{ $index + 1 }}</p>
                            </div>
                        @endif
                        
                        @if(!empty($currentText))
                            @php
                                $cLen = mb_strlen($currentText);
                                $cFontSize = 'text-[10px]';
                                if ($cLen > 120) $cFontSize = 'text-[7px]';
                                elseif ($cLen > 60) $cFontSize = 'text-[8px]';
                            @endphp
                            <div class="absolute inset-x-4 bottom-4 bg-white border-2 border-slate-900 p-4 shadow-xl shrink-0">
                                <div class="max-h-[60px] overflow-hidden">
                                     <p class="text-slate-900 font-black {{ $cFontSize }} uppercase leading-tight font-montserrat">{{ $currentText }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Footer/Branding --}}
        <div class="absolute inset-x-0 bottom-0 h-16 px-10 bg-white border-t-4 border-slate-900 flex items-center justify-between z-30 shadow-[0_-10px_20px_rgba(0,0,0,0.05)]">
             <span class="text-slate-900 text-xs font-black font-montserrat uppercase tracking-[0.2em]">{{ $companyLabel }}</span>
             <div class="flex items-center gap-4">
                  @if($total > 1)
                    <div class="text-slate-900/20 text-[10px] font-black" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                        @if(!$preview) {{ ($slideIndex ?? 0) + 1 }} / {{ $total }} @endif
                    </div>
                  @endif
                  <div class="w-4 h-4" style="background-color: {{ $brandColor }}"></div>
             </div>
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
