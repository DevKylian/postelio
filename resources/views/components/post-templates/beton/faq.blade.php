@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'question' => '',
    'answer' => '',
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

<div {!! $alpineData !!} class="w-full aspect-square bg-[#CBD5E1] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    <div class="flex-1 flex flex-col p-10 justify-center relative z-10">
        
        {{-- Slide 0: Question --}}
        @php
            $isSlide0 = $slideIndex === null || $slideIndex === 0;
            $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $slide0Style = !$isSlide0 ? 'display: none;' : '';
        @endphp
        <div {!! $slide0Alpine !!} style="{{ $slide0Style }}" class="bg-slate-900 p-8 rounded-2xl shadow-2xl border-b-4 border-slate-700">
            <div class="flex items-center gap-4 mb-6">
                 <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-white text-lg shadow-lg" style="background-color: {{ $brandColor }}">Q</div>
                 <span class="text-white/40 text-[10px] font-black uppercase tracking-[0.2em]">Question Client</span>
            </div>
            @php
                $qLen = mb_strlen($question);
                $qFontSize = 'text-2xl';
                if ($qLen > 100) $qFontSize = 'text-lg';
                elseif ($qLen > 50) $qFontSize = 'text-xl';
            @endphp
            <div class="max-h-[160px] overflow-hidden">
                <p class="text-white {{ $qFontSize }} font-black font-montserrat leading-tight uppercase tracking-tight">
                    {{ $question }}
                </p>
            </div>
        </div>

        {{-- Slide 1: Answer --}}
        @php
            $isSlide1 = $slideIndex !== null && $slideIndex >= 1;
            $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
            $slide1Style = !$isSlide1 ? 'display: none;' : '';
        @endphp
        <div {!! $slide1Alpine !!} style="{{ $slide1Style }}" class="bg-white border-4 border-slate-900 p-8 rounded-2xl shadow-2xl ml-6">
            <div class="flex items-center gap-4 mb-6">
                 <div class="w-10 h-10 rounded-full bg-slate-900 flex items-center justify-center font-black text-white text-lg shadow-lg">R</div>
                 <span class="text-slate-900/40 text-[10px] font-black uppercase tracking-[0.2em]">Réponse Expert</span>
            </div>
            @php
                $aLen = mb_strlen($answer);
                $aFontSize = 'text-lg';
                if ($aLen > 150) $aFontSize = 'text-sm';
                elseif ($aLen > 80) $aFontSize = 'text-base';
            @endphp
            <div class="max-h-[180px] overflow-hidden">
                <p class="text-slate-900 {{ $aFontSize }} font-black font-montserrat leading-snug uppercase tracking-tight">
                    {{ $answer }}
                </p>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="h-16 px-10 border-t border-slate-900/10 flex items-center justify-between">
         <span class="text-slate-900 text-xs font-black font-montserrat uppercase tracking-[0.2em]">{{ $companyLabel }}</span>
         
         @if($preview || $slideIndex !== null)
            <div class="flex items-center gap-3">
                <div class="text-slate-900/40 text-[10px] font-black" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                    @if(!$preview) {{ $slideIndex + 1 }} / {{ $total }} @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Controls (Preview Only) --}}
    @if($preview)
        <div class="absolute inset-y-0 inset-x-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 pointer-events-auto transition-all shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 pointer-events-auto transition-all shadow-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
