@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Détails Techniques',
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
    <div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
        
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
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 flex items-center justify-center bg-blue-900/40">
                        <span class="text-white/10 text-[10px] font-black font-mono-technical uppercase tracking-widest">Image Technique {{ $index + 1 }}</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-blue-900/20"></div>
                
                @if(!empty($currentText))
                    <div class="absolute bottom-20 left-8 right-8 z-20">
                        @php
                            $cLen = mb_strlen($currentText);
                            $cFontSize = 'text-xs';
                            if ($cLen > 100) $cFontSize = 'text-[8px]';
                            elseif ($cLen > 50) $cFontSize = 'text-[10px]';
                        @endphp
                        <div class="bg-white p-4 shadow-2xl border-l-4 max-h-[100px] overflow-hidden shrink-0" style="border-left-color: {{ $brandColor }}">
                             <p class="text-[#0047AB] {{ $cFontSize }} font-black font-mono-technical leading-tight uppercase">{{ $currentText }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

         <div class="absolute bottom-0 inset-x-0 h-14 bg-white px-8 flex items-center justify-between border-t-2 border-[#0047AB] z-30">
              <span class="text-[#0047AB] text-[10px] font-black font-mono-technical uppercase tracking-widest italic">{{ $companyLabel }}</span>
              <div class="flex items-center gap-4">
                   @if($total > 1)
                    <div class="text-[#0047AB]/40 text-[9px] font-black font-mono-technical" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                        @if(!$preview) {{ ($slideIndex ?? 0) + 1 }} / {{ $total }} @endif
                    </div>
                   @endif
                   <div class="w-3 h-3 border border-[#0047AB]" style="background-color: {{ $brandColor }}"></div>
              </div>
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
