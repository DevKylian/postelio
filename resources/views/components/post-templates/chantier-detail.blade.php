@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'title' => 'Rénovation complète d\'une toiture à Nantes',
    'slides' => [null, null, null], // array of image URLs
    'slideContents' => [], // array of texts corresponding to slides
    'preview' => false,
    'slideIndex' => null, // null = interactive alpine preview, int = render specific slide
])

@if($preview)
    @php
        $title = 'Focus sur l\'installation du système de chauffage';
        $slides = [
            'https://images.unsplash.com/photo-1585314062340-f1a5a7c9328d?q=80&w=800',
            'https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?q=80&w=800',
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800'
        ];
        $slideContents = [
            'Préparation technique et traçage des réseaux.',
            'Installation des collecteurs et mise en pression.',
            'Finalisation des raccordements et test de chauffe.'
        ];
    @endphp
@endif

@php
    $total = count($slides);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-square bg-slate-800 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
        
        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || $slideIndex === $index;
                $slideAlpine = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : '';
                $slideStyle = !$isSlideVisible ? 'display: none;' : '';
                $currentText = ($index === 0) ? ($slideContents[0] ?? $title) : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                @if ($imageUrl)
                    <img src="{{ $imageUrl }}" class="w-full h-full object-contain">
                @else
                    <div class="w-full h-full flex items-center justify-center {{ $index % 2 === 0 ? 'bg-slate-600' : 'bg-slate-500' }}">
                         <div class="text-center">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <p class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em]">Photo {{ $index + 1 }}</p>
                         </div>
                    </div>
                @endif

                @php
                    $slideText = $slideContents[$index] ?? (($index === 0) ? $title : '');
                @endphp

                @if(!empty($slideText) && !$imageUrl)
                    @php
                        $sLen = mb_strlen($slideText);
                        $sFontSize = 'text-[11px]';
                        if ($sLen > 120) $sFontSize = 'text-[7px]';
                        elseif ($sLen > 60) $sFontSize = 'text-[9px]';
                    @endphp
                    <div class="absolute inset-x-4 bottom-20 bg-black/70 backdrop-blur-sm p-4 rounded-xl border border-white/10 shadow-xl">
                        <div class="max-h-[60px] overflow-hidden">
                             <p class="text-white font-black {{ $sFontSize }} uppercase leading-snug font-montserrat text-center">{{ $slideText }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="absolute inset-x-0 bottom-0 py-2.5 px-6 flex items-center justify-center" style="background-color: rgba(0,0,0,0.85)">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="h-5 w-5 object-contain rounded absolute right-6">
            @endif
            <p class="text-white font-bold uppercase tracking-widest text-[10px] font-montserrat">{{ $companyLabel }}</p>
        </div>

        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg" x-text="(current + 1) + ' / ' + total"></div>
        @else
            @if($total > 1)
                <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg">
                    {{ ($slideIndex ?? 0) + 1 }} / {{ $total }}
                </div>
            @endif
        @endif
    </div>
</div>
