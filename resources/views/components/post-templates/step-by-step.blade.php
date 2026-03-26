@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'slides' => [null, null, null], // array of image URLs
    'slideContents' => [], // array of texts corresponding to slides
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $slides = [
            null,
            'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?q=80&w=800',
            'https://images.unsplash.com/photo-1517646287270-a5a067f920f0?q=80&w=800',
            'https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?q=80&w=800'
        ];
        $slideContents = [
            'Comment nous réalisons votre isolation par l\'extérieur',
            'Analyse de la structure existante et préparation des supports.',
            'Mise en œuvre des panneaux isolants avec fixation mécanique.',
            'Application de l\'enduit de finition et contrôle qualité.'
        ];
    @endphp
@endif

@php
    $total = count($slides);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-square bg-slate-100 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
        
        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || $slideIndex === $index;
                $slideCondition = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : ($isSlideVisible ? '' : 'style="display: none;"');
                
                $slideInlineStyle = !$isSlideVisible ? 'display: none;' : '';
                if ($index === 0) {
                    $slideInlineStyle .= " background-color: $brandColor;";
                }
                // Only show text on the first slide (cover) if available
                $currentText = ($index === 0) ? ($slideContents[0] ?? '') : '';
            @endphp
            
            <div class="absolute inset-0 {{ $index === 0 ? 'p-10 flex flex-col justify-center' : '' }}" {!! $slideCondition !!} style="{{ $slideInlineStyle }}">
                @if($index === 0)
                    <p class="text-white/30 font-black text-4xl mb-2 select-none tracking-tighter uppercase leading-none">ÉTAPE PAR ÉTAPE</p>
                    @php
                        $coverText = $slideContents[0] ?? '';
                    @endphp
                    @if(!empty($coverText))
                        @php
                            $cLen = mb_strlen($coverText);
                            $cFontSize = 'text-lg';
                            if ($cLen > 80) $cFontSize = 'text-sm';
                            elseif ($cLen > 40) $cFontSize = 'text-base';
                        @endphp
                        <div class="mt-4 bg-white/10 backdrop-blur-sm p-4 rounded-2xl border border-white/20 max-h-[140px] overflow-hidden">
                            <p class="text-white font-black {{ $cFontSize }} uppercase leading-tight font-montserrat">{{ $coverText }}</p>
                        </div>
                    @else
                        <div class="space-y-2 mt-4">
                            <div class="h-1.5 w-full bg-white/40 rounded-full"></div>
                            <div class="h-1.5 w-2/3 bg-white/40 rounded-full"></div>
                        </div>
                    @endif
                @else
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" class="w-full h-full object-contain">
                    @else
                        <div class="w-full h-full bg-slate-300 flex items-center justify-center font-bold text-slate-500/30 select-none">
                            <div class="text-center">
                                <svg class="w-10 h-10 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <p class="text-[10px] font-bold uppercase tracking-widest leading-none">ÉTAPE {{ sprintf('%02d', $index) }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="absolute top-6 left-6 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg z-10" style="background-color: {{ $brandColor }}">
                        {{ $index }}
                    </div>

                    @php
                        $stepText = $slideContents[$index] ?? '';
                    @endphp
                    @if(!empty($stepText))
                        @php
                            $sLen = mb_strlen($stepText);
                            $sFontSize = 'text-xs';
                            if ($sLen > 150) $sFontSize = 'text-[9px]';
                            elseif ($sLen > 80) $sFontSize = 'text-[10px]';
                        @endphp
                        <div class="absolute inset-x-6 bottom-16 bg-black/80 backdrop-blur-md p-4 rounded-2xl border border-white/20 shadow-2xl max-h-[100px] overflow-hidden shrink-0">
                            <p class="text-white font-black {{ $sFontSize }} uppercase leading-tight font-montserrat">{{ $stepText }}</p>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach

        @if($preview)
            <div class="absolute bottom-0 inset-x-0 h-10 flex items-center justify-center px-4" :style="current === 0 ? 'background-color: rgba(0,0,0,0.2)' : 'background-color: transparent'">
                <p class="font-bold text-[9px] uppercase tracking-widest font-montserrat" :class="current === 0 ? 'text-white/60' : 'text-white'">{{ $companyLabel }}</p>
            </div>
            
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @else
            @php
                $footerStyle = ($slideIndex === 0) ? 'background-color: rgba(0,0,0,0.2);' : 'background-color: transparent;';
            @endphp
            <div class="absolute bottom-0 inset-x-0 h-10 flex items-center justify-center px-4" style="{{ $footerStyle }}">
                <p class="font-bold text-[9px] uppercase tracking-widest font-montserrat {{ ($slideIndex === 0) ? 'text-white/60' : 'text-white' }}">{{ $companyLabel }}</p>
            </div>
        @endif
    </div>
</div>
