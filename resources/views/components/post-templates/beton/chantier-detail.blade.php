@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Zoom Chantier',
    'slides' => [null, null, null, null], // array of image URLs
    'slideContents' => [], // array of texts corresponding to slides
    'preview' => false,
    'slideIndex' => null, 
])

@if($preview)
    @php
        $title = 'Focus sur l\'installation du système de chauffage';
        $slides = [
            'https://images.unsplash.com/photo-1585314062340-f1a5a7c9328d?q=80&w=800',
            'https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?q=80&w=800',
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800',
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800'
        ];
        $slideContents = [
            'Préparation technique et traçage des réseaux.',
            'Installation des collecteurs et mise en pression.',
            'Finalisation des raccordements et test de chauffe.',
            'Contrôle final et mise en service.'
        ];
    @endphp
@endif

@php
    $total = count($slides);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-square bg-[#D1D5DB] relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
        {{-- Concrete Texture --}}
        <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay z-20" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>

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
                <div class="absolute inset-0 p-4">
                    <div class="w-full h-full relative rounded-lg overflow-hidden border-2 border-gray-900 bg-gray-800 shadow-xl">
                        @if ($imageUrl)
                            <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-700">
                                <p class="text-[10px] font-bold text-white/20 uppercase tracking-[0.2em]">Photo {{ $index + 1 }}</p>
                            </div>
                        @endif
                        
                        @if(!empty($currentText) && !$imageUrl)
                            @php
                                $cLen = mb_strlen($currentText);
                                $cFontSize = 'text-[10px]';
                                if ($cLen > 100) $cFontSize = 'text-[8px]';
                            @endphp
                            <div class="absolute inset-x-4 bottom-16 bg-gray-900/90 backdrop-blur-sm p-4 rounded border-l-4 shadow-xl shrink-0" style="border-color: {{ $brandColor }}">
                                <div class="max-h-[60px] overflow-hidden">
                                    <p class="text-white font-black {{ $cFontSize }} uppercase leading-tight font-montserrat">{{ $currentText }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="absolute inset-x-0 bottom-0 h-16 px-6 bg-gray-900 flex items-center justify-between z-30">
             <div class="flex items-center gap-3">
                  <div class="w-2 h-8" style="background-color: {{ $brandColor }}"></div>
                  <span class="text-white text-xs font-black font-montserrat uppercase tracking-[0.2em]">ZOOM CHANTIER</span>
             </div>
             <div class="flex flex-col items-end">
                 <span class="text-white/40 text-[9px] font-black uppercase tracking-widest italic leading-none mb-1">{{ $companyLabel }}</span>
                 @if($total > 1)
                    <div class="text-white/20 text-[8px] font-bold" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                        @if(!$preview) {{ ($slideIndex ?? 0) + 1 }} / {{ $total }} @endif
                    </div>
                 @endif
             </div>
        </div>

        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-40">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-gray-900/50 text-white flex items-center justify-center hover:bg-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-gray-900/50 text-white flex items-center justify-center hover:bg-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
