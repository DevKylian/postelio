@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'question' => '',
    'answer' => '',
    'logoUrl' => null,
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $question = $question ?: 'Quels sont les délais moyens pour une rénovation complète de toiture ?';
        $answer = $answer ?: 'Il faut compter entre 1 et 2 semaines selon la surface et la complexité de la charpente.';
    @endphp
@endif

@php
    $total = 2;
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full aspect-square bg-[#0047AB] flex flex-col relative overflow-hidden {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' }}">
    
    {{-- Grid Overlay --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none" 
         style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
    </div>

    <div class="flex-1 flex flex-col p-12 justify-center relative z-10">
        
        {{-- Slide 0: Question --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="relative pl-14">
            <div class="absolute left-0 top-0 w-12 h-12 border-2 border-[#0047AB] bg-white flex items-center justify-center font-mono-technical text-[#0047AB] font-black z-20 text-xl shadow-lg">Q</div>
            <div class="bg-white p-8 shadow-2xl ring-1 ring-black/5">
                @php
                    $qLen = mb_strlen($question);
                    $qFontSize = 'text-base';
                    if ($qLen > 100) $qFontSize = 'text-xs';
                    elseif ($qLen > 50) $qFontSize = 'text-sm';
                @endphp
                <div class="max-h-[160px] overflow-hidden">
                    <p class="text-[#0047AB] {{ $qFontSize }} font-black font-mono-technical leading-tight uppercase tracking-tight">
                        {{ $question }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Slide 1: Answer --}}
        @php
            $isSlide1 = $slideIndex === 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = $slideIndex !== 1 ? 'display: none;' : '';
        @endphp
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}" class="relative pl-14">
            <div class="absolute left-0 top-0 w-12 h-12 border-2 border-white flex items-center justify-center font-mono-technical text-white font-black z-20 text-xl shadow-lg" style="background-color: {{ $brandColor }}">R</div>
            <div class="bg-white p-8 shadow-2xl ring-1 ring-black/5 border-l-8" style="border-left-color: {{ $brandColor }}">
                @php
                    $aLen = mb_strlen($answer);
                    $aFontSize = 'text-sm';
                    if ($aLen > 150) $aFontSize = 'text-[11px]';
                    elseif ($aLen > 80) $aFontSize = 'text-xs';
                @endphp
                <div class="max-h-[180px] overflow-hidden">
                    <p class="text-[#0047AB] {{ $aFontSize }} font-black font-mono-technical leading-snug uppercase tracking-tight">
                        {{ $answer }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="h-14 px-10 bg-white border-t-2 border-[#0047AB] flex items-center justify-between text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest">
        <span>{{ $companyLabel }}</span>
        
        @if($preview || $slideIndex !== null)
            <span class="opacity-50" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                @if(!$preview) {{ $slideIndex + 1 }} / {{ $total }} @endif
            </span>
        @endif
    </div>

    {{-- Controls (Preview Only) --}}
    @if($preview)
        <div class="absolute inset-y-0 inset-x-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-8 h-8 bg-white border border-[#0047AB] text-[#0047AB] flex items-center justify-center hover:bg-[#0047AB] hover:text-white pointer-events-auto transition-colors shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-8 h-8 bg-white border border-[#0047AB] text-[#0047AB] flex items-center justify-center hover:bg-[#0047AB] hover:text-white pointer-events-auto transition-colors shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
