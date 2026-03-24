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
    <div class="w-full aspect-square bg-slate-900 flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
        
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
                    <div class="absolute inset-0 flex items-center justify-center bg-slate-800">
                        <span class="text-white/10 text-xs font-black uppercase tracking-widest">Photo {{ $index + 1 }}</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-slate-900/40"></div>
                
                @if(!empty($currentText))
                    <div class="absolute inset-x-8 bottom-24 z-20">
                        @php
                            $cLen = mb_strlen($currentText);
                            $cFontSize = 'text-base';
                            if ($cLen > 80) $cFontSize = 'text-xs';
                            elseif ($cLen > 40) $cFontSize = 'text-sm';
                        @endphp
                        <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-6 shadow-2xl max-h-[100px] overflow-hidden shrink-0">
                             <p class="text-white {{ $cFontSize }} font-bold leading-tight uppercase tracking-tight">{{ $currentText }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Center Floating Glass Badge / Branding --}}
        <div class="absolute inset-x-0 bottom-0 h-20 backdrop-blur-2xl bg-slate-900/60 border-t border-white/10 px-8 flex items-center justify-between z-30">
             <div class="flex flex-col">
                 <span class="text-white/60 text-[8px] font-black uppercase tracking-[0.4em] font-montserrat mb-0.5">{{ $companyLabel }}</span>
                 <span class="text-white text-xs font-black uppercase tracking-[0.2em] font-montserrat" style="color: {{ $brandColor }}">Zoom Chantier</span>
             </div>
             @if($total > 1)
                <div class="text-white/20 text-[10px] font-black" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                    @if(!$preview) {{ ($slideIndex ?? 0) + 1 }} / {{ $total }} @endif
                </div>
             @endif
        </div>

        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-40">
                <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full backdrop-blur-md bg-white/10 border border-white/20 text-white flex items-center justify-center hover:bg-white/20 shadow-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full backdrop-blur-md bg-white/10 border border-white/20 text-white flex items-center justify-center hover:bg-white/20 shadow-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
