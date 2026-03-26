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

<div {!! $alpineData !!} class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    <div class="flex-1 flex flex-col p-10 justify-center relative z-10">
        
        {{-- Slide 0: Question --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="relative pl-16">
            <div class="absolute left-0 top-0 w-12 h-12 bg-slate-900 flex items-center justify-center font-montserrat text-white text-xl font-black shadow-xl -rotate-3 border-2 border-white">Q</div>
            <div class="bg-white border-4 border-slate-900 p-8 shadow-[10px_10px_0px_0px_rgba(15,23,42,0.1)]">
                @php
                    $qLen = mb_strlen($question);
                    $qFontSize = 'text-xl';
                    if ($qLen > 100) $qFontSize = 'text-base';
                    elseif ($qLen > 50) $qFontSize = 'text-lg';
                @endphp
                <div class="max-h-[160px] overflow-hidden">
                    <p class="text-slate-900 {{ $qFontSize }} font-black font-montserrat leading-tight uppercase">
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
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}" class="relative pl-16">
            <div class="absolute left-0 top-0 w-12 h-12 flex items-center justify-center font-black text-white text-xl font-montserrat shadow-xl rotate-3 border-2 border-white" style="background-color: {{ $brandColor }}">R</div>
            <div class="bg-white border-4 border-slate-900 p-8 shadow-[10px_10px_0px_0px_rgba(15,23,42,0.1)] border-l-[12px] border-l-slate-900">
                @php
                    $aLen = mb_strlen($answer);
                    $aFontSize = 'text-base';
                    if ($aLen > 150) $aFontSize = 'text-xs';
                    elseif ($aLen > 80) $aFontSize = 'text-sm';
                @endphp
                <div class="max-h-[180px] overflow-hidden">
                    <p class="text-slate-900 {{ $aFontSize }} font-bold font-montserrat leading-snug uppercase">
                        {{ $answer }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="h-16 px-10 border-t-4 border-slate-900 bg-[#CBB198] flex items-center justify-between">
         <span class="text-slate-900 text-[11px] font-black uppercase tracking-[0.2em] font-montserrat">{{ $companyLabel }}</span>
         
         @if($preview || $slideIndex !== null)
            <div class="text-slate-900/50 text-[10px] font-black font-montserrat tracking-widest" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                @if(!$preview) {{ $slideIndex + 1 }} / {{ $total }} @endif
            </div>
        @endif
    </div>

    {{-- Controls (Preview Only) --}}
    @if($preview)
        <div class="absolute inset-y-0 inset-x-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 border-4 border-slate-900 bg-white text-slate-900 flex items-center justify-center hover:bg-slate-900 hover:text-white pointer-events-auto transition-all shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 border-4 border-slate-900 bg-white text-slate-900 flex items-center justify-center hover:bg-slate-900 hover:text-white pointer-events-auto transition-all shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
