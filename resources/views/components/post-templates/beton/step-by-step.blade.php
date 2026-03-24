@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'slides' => [null, null, null, null], // array of image URLs
    'slideContents' => [], // array of steps (text)
    'preview' => false,
    'slideIndex' => null,
])

@php
    $total = count($slides);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-square bg-[#CBD5E1] relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
        {{-- Concrete Texture --}}
        <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay z-20" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>

        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || $slideIndex === $index;
                $slideAlpine = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : '';
                $slideStyle = !$isSlideVisible ? 'display: none;' : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                {{-- Show image if index > 0 (steps) or if it's the cover with image --}}
                @if($imageUrl)
                    <div class="absolute inset-0">
                        <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-slate-900/40"></div>
                    </div>
                @endif

                <div class="absolute inset-0 flex flex-col p-10 justify-center gap-4 z-10">
                    @foreach($slideContents as $stepIndex => $stepText)
                        @php
                            $isCurrentStep = $index === ($stepIndex + 1);
                            $isPastStep = $index > ($stepIndex + 1);
                        @endphp
                        <div class="flex items-center gap-4 transition-all duration-300 {{ $isCurrentStep ? 'scale-105' : ($isPastStep ? 'opacity-50 grayscale' : 'opacity-30') }}">
                            <div class="w-10 h-10 rounded bg-slate-900 flex items-center justify-center font-black text-white text-sm shadow-xl shrink-0"
                                 @if($isCurrentStep) style="background-color: {{ $brandColor }}" @endif>
                                {{ str_pad($stepIndex + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="flex-1 bg-white border-2 border-slate-900 p-3 rounded shadow-lg max-h-[80px] overflow-hidden">
                                <p class="text-slate-900 text-[10px] font-black font-montserrat uppercase leading-tight">
                                    {{ $stepText }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($index === 0)
                        <div class="bg-slate-900 p-6 rounded-xl border-t-4 shadow-2xl" style="border-color: {{ $brandColor }}">
                             <span class="text-white/40 text-[9px] font-black uppercase tracking-[0.2em] mb-2 block">DÉROULÉ TECHNIQUE</span>
                             <h2 class="text-white text-3xl font-black font-montserrat leading-none uppercase tracking-tighter">ÉTAPES DU PROJET</h2>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="absolute inset-x-0 bottom-0 h-16 px-10 bg-slate-900 flex items-center justify-between border-t border-white/10 z-30">
             <span class="text-white text-[10px] font-black font-montserrat uppercase tracking-widest">{{ $companyLabel }}</span>
             <div class="flex gap-1.5" @if($preview) x-show="total > 1" @endif>
                  @for($i=0; $i<$total; $i++)
                    <div class="w-2 h-2 rounded-full transition-all duration-300" 
                         @if($preview) :class="current === {{ $i }} ? '' : 'opacity-20 bg-white'" :style="current === {{ $i }} ? 'background-color: {{ $brandColor }}' : ''"
                         @else style="background-color: {{ ($slideIndex ?? 0) === $i ? $brandColor : 'rgba(255,255,255,0.2)' }}" @endif>
                    </div>
                  @endfor
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
