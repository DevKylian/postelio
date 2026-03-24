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
    <div class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
        
        <div class="absolute inset-0 opacity-10 pointer-events-none" 
             style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
        </div>

        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || $slideIndex === $index;
                $slideAlpine = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : '';
                $slideStyle = !$isSlideVisible ? 'display: none;' : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                {{-- Show image if available --}}
                @if($imageUrl)
                    <div class="absolute inset-0">
                        <img src="{{ $imageUrl }}" class="w-full h-full object-cover opacity-20">
                    </div>
                @endif

                <div class="absolute inset-0 flex flex-col p-12 justify-center gap-6 z-10">
                    @foreach($slideContents as $stepIndex => $stepText)
                        @php
                            $isCurrentStep = $index === ($stepIndex + 1);
                            $isPastStep = $index > ($stepIndex + 1);
                        @endphp
                        <div class="flex items-center gap-6 group/step transition-all duration-300 {{ $isCurrentStep ? 'scale-105 translate-x-4' : ($isPastStep ? 'opacity-40' : 'opacity-10') }}">
                            <div class="w-10 h-10 border-2 border-[#0047AB] bg-white flex items-center justify-center font-black text-[#0047AB] text-xs font-mono-technical shadow-lg"
                                 style="{{ $isCurrentStep ? 'background-color: '.$brandColor.'; color: white; border-color: white;' : '' }}">
                                {{ sprintf('%02d', $stepIndex + 1) }}
                            </div>
                            <div class="flex-1 bg-white p-4 shadow-xl border-t border-blue-100 max-h-[80px] overflow-hidden {{ $isCurrentStep ? 'border-l-4' : '' }}"
                                 style="{{ $isCurrentStep ? 'border-left-color: '.$brandColor : '' }}">
                                @php
                                    $sLen = mb_strlen($stepText);
                                    $sFontSize = 'text-[10px]';
                                    if ($sLen > 80) $sFontSize = 'text-[7px]';
                                    elseif ($sLen > 40) $sFontSize = 'text-[8px]';
                                @endphp
                                <p class="text-[#0047AB] {{ $sFontSize }} font-black font-mono-technical uppercase leading-tight">{{ $stepText }}</p>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($index === 0)
                        <div class="mt-8 bg-white p-6 shadow-2xl border-t-2 border-t-[#003380] relative">
                             <span class="text-[#0047AB]/40 text-[7px] font-black font-mono-technical uppercase tracking-[0.3em] block mb-1">PROGRAMME TECHNIQUE</span>
                             <h2 class="text-[#0047AB] text-lg font-black font-mono-technical leading-none uppercase tracking-tighter">LES ÉTAPES DU PROJET</h2>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="absolute inset-x-0 bottom-0 h-14 bg-white px-10 border-t-2 border-[#0047AB] flex items-center justify-between z-30">
             <span class="text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest italic">{{ $companyLabel }}</span>
             <div class="flex items-center gap-3">
                  @for($i=0; $i<$total; $i++)
                    <div class="w-4 h-1 border border-[#0047AB]" 
                         @if($preview) :class="current === {{ $i }} ? '' : 'opacity-20'" :style="current === {{ $i }} ? 'background-color: {{ $brandColor }}' : ''"
                         @else style="background-color: {{ ($slideIndex ?? 0) === $i ? $brandColor : 'rgba(0, 71, 171, 0.1)' }}" @endif>
                    </div>
                  @endfor
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
