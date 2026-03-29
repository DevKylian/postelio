@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'preview' => false,
    'template' => null, // Can be CustomTemplate model or array
    'text' => null,     // The actual text to display on image (image_text)
    'slideIndex' => null,
    'hasImageText' => true,
    'imageBeforeUrl' => null,
    'imageAfterUrl' => null,
    'imageUrl' => null,
])

@php
    $ct = $template;
    $photoMode = $ct['photo_mode'] ?? 'none';
    $visualStyle = $ct['visual_style'] ?? 'dark_branded';
    $layoutType = $ct['layout_type'] ?? 'standard';
    $name = $ct['name'] ?? 'Modèle';
    $hasImageText = $hasImageText ?? ($ct['has_image_text'] ?? true);
    
    // Sample text for preview if no text provided
    $displayText = ($hasImageText) 
        ? ($text ?: ($ct['description'] ?? 'Votre contenu apparaîtra ici.'))
        : null;
    
    $isCarousel = $photoMode === 'carousel';
    $isSingle = $photoMode === 'single';
    $isNone = $photoMode === 'none';

    // Helper for style colors
    $styles = [
        'dark_branded' => [
            'bg' => '#111827',
            'title' => $brandColor,
            'text' => '#FFFFFF',
            'label' => $brandColor,
            'sep' => 'rgba(255,255,255,0.1)',
            'ghost' => 'rgba(255,255,255,0.03)'
        ],
        'light_branded' => [
            'bg' => '#F9FAFB',
            'title' => $brandColor,
            'text' => '#111827',
            'label' => $brandColor,
            'sep' => 'rgba(0,0,0,0.08)',
            'ghost' => 'rgba(0,0,0,0.02)'
        ],
        'brand_color' => [
            'bg' => $brandColor,
            'title' => 'rgba(255,255,255,0.9)',
            'text' => '#FFFFFF',
            'label' => 'rgba(255,255,255,0.8)',
            'sep' => 'rgba(0,0,0,0.15)',
            'ghost' => 'rgba(0,0,0,0.1)'
        ]
    ];
    
    $currentStyle = $styles[$visualStyle] ?? $styles['dark_branded'];
@endphp

<div class="w-full aspect-square bg-gray-50 relative overflow-hidden font-montserrat {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}"
     style="background-color: {{ $currentStyle['bg'] }}">

    {{-- Decor elements (Ghost circles) --}}
    @if($visualStyle !== 'light_branded')
        <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full pointer-events-none" style="background: {{ $currentStyle['ghost'] }}"></div>
        <div class="absolute -bottom-20 -left-10 w-48 h-48 rounded-full pointer-events-none" style="background: {{ $currentStyle['ghost'] }}"></div>
    @endif

    @if($isNone)
        {{-- LAYOUTS FOR TEXT ONLY --}}
        
        @if($layoutType === 'centered')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = '0.9rem';
                if ($len > 250) $fSize = '0.65rem';
                elseif ($len > 150) $fSize = '0.75rem';
                elseif ($len > 80) $fSize = '0.85rem';
            @endphp
            <div class="absolute inset-0 flex flex-col items-center justify-center px-10 text-center">
                <div class="w-12 h-1 mb-6 rounded-full" style="background-color: {{ $brandColor }}"></div>
                <h3 class="font-black uppercase tracking-tight mb-4" style="color: {{ $currentStyle['title'] }}; font-size: 1.5rem; line-height: 1.1;">{{ $name }}</h3>
                <div class="max-h-[140px] overflow-hidden">
                    <p class="font-medium leading-relaxed" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize }};">{{ $displayText }}</p>
                </div>
            </div>

        @elseif($layoutType === 'quote')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = '1.1rem';
                if ($len > 150) $fSize = '0.85rem';
                elseif ($len > 80) $fSize = '1rem';
            @endphp
            <div class="absolute inset-x-8 top-12">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-0.5" style="background-color: {{ $brandColor }}"></div>
                    <span class="text-[10px] font-bold uppercase tracking-widest" style="color: {{ $brandColor }}">{{ $name }}</span>
                </div>
            </div>
            <div class="absolute inset-0 flex flex-col items-center justify-center px-12 text-center pt-8">
                <span class="text-6xl font-serif opacity-30 leading-none mb-2" style="color: {{ $brandColor }}">"</span>
                <div class="max-h-[180px] overflow-hidden">
                    <p class="font-bold italic leading-relaxed" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize }};">{{ $displayText }}</p>
                </div>
                <span class="text-6xl font-serif opacity-30 leading-none mt-4 self-end" style="color: {{ $brandColor }}">"</span>
            </div>

        @elseif($layoutType === 'bold_title')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = '1.05rem';
                if ($len > 200) $fSize = '0.75rem';
                elseif ($len > 120) $fSize = '0.85rem';
                elseif ($len > 60) $fSize = '0.95rem';
            @endphp
            <div class="h-1/3 w-full flex items-center justify-center px-8 text-center" style="background-color: {{ $brandColor }}">
                <h3 class="font-black uppercase text-white tracking-tighter" style="font-size: 1.8rem; line-height: 1;">{{ $name }}</h3>
            </div>
            <div class="flex-1 flex flex-col justify-center px-10">
                <div class="w-10 h-1 mb-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
                <div class="max-h-[160px] overflow-hidden">
                    <p class="font-bold leading-relaxed" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize }};">{{ $displayText }}</p>
                </div>
            </div>

        @elseif($layoutType === 'split')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = '0.95rem';
                if ($len > 250) $fSize = '0.7rem';
                elseif ($len > 150) $fSize = '0.8rem';
                elseif ($len > 80) $fSize = '0.9rem';
            @endphp
            <div class="h-1/2 w-full flex flex-col justify-center px-10 pb-4" style="background-color: {{ $brandColor }}">
                <div class="w-8 h-0.5 bg-white/40 mb-3"></div>
                <h3 class="font-black uppercase text-white tracking-tight" style="font-size: 1.4rem; line-height: 1.1;">{{ $name }}</h3>
            </div>
            <div class="h-1/2 w-full flex flex-col justify-center px-10 pt-4">
                <div class="w-8 h-0.5 mb-3" style="background-color: {{ $brandColor }}"></div>
                <div class="max-h-[120px] overflow-hidden">
                    <p class="font-medium leading-relaxed" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize }};">{{ $displayText }}</p>
                </div>
            </div>

        @elseif($layoutType === 'minimal')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = '1rem';
                if ($len > 250) $fSize = '0.75rem';
                elseif ($len > 150) $fSize = '0.85rem';
                elseif ($len > 80) $fSize = '0.95rem';
            @endphp
            <div class="absolute inset-0 flex flex-col justify-between p-10 bg-white">
                <div class="absolute left-0 top-0 bottom-0 w-2" style="background-color: {{ $brandColor }}"></div>
                <div>
                    <h3 class="font-black uppercase tracking-tight mb-6" style="color: {{ $brandColor }}; font-size: 1.3rem;">{{ $name }}</h3>
                    <div class="w-full h-px bg-gray-100 mb-8"></div>
                    <div class="max-h-[160px] overflow-hidden">
                        <p class="font-semibold text-gray-700 leading-relaxed" style="font-size: {{ $fSize }};">{{ $displayText }}</p>
                    </div>
                </div>
            </div>

        @elseif($layoutType === 'dynamic')
            {{-- AI Generated Dynamic Layout --}}
            <div class="absolute inset-0 flex flex-col w-full h-full overflow-hidden">
                {!! Blade::render($ct['dynamic_content'] ?? '', [
                    'brandColor' => $brandColor,
                    'displayText' => $displayText,
                    'name' => $name,
                    'currentStyle' => $currentStyle,
                    'companyLabel' => $companyLabel,
                    'logoUrl' => $logoUrl,
                    'beforeImage' => $imageBeforeUrl,
                    'afterImage' => $imageAfterUrl,
                    'image' => $imageUrl
                ]) !!}
            </div>

        @else {{-- standard --}}
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = '1rem';
                if ($len > 250) $fSize = '0.7rem';
                elseif ($len > 150) $fSize = '0.85rem';
                elseif ($len > 80) $fSize = '0.95rem';
            @endphp
            <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background-color: {{ $brandColor }}"></div>
            <div class="absolute inset-0 flex flex-col justify-between px-10 py-10">
                <div>
                    <h3 class="font-black uppercase leading-none mb-4" style="color: {{ $currentStyle['title'] }}; font-size: 1.4rem; letter-spacing: -0.01em;">{{ $name }}</h3>
                    <div class="w-8 h-0.5 mb-6 rounded-full" style="background-color: {{ $brandColor }}; opacity: 0.6;"></div>
                    <div class="max-h-[220px] overflow-hidden">
                        <p class="font-bold leading-relaxed" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize }};">{{ $displayText }}</p>
                    </div>
                </div>
            </div>
        @endif

    @elseif($isSingle)
        {{-- PHOTO SINGLE MODE --}}
        <div class="absolute inset-0 bg-gray-700 flex items-center justify-center">
            @if($preview)
                <div class="text-center text-white/30 uppercase tracking-[0.2em]">
                    <svg class="w-16 h-16 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-xs font-black">Votre photo</p>
                </div>
            @endif
        </div>
        {{-- Header bar --}}
        <div class="absolute inset-x-0 top-0 h-1.5 z-10" style="background-color: {{ $brandColor }}"></div>
        {{-- Overlay bas avec nom template --}}
        <div class="absolute inset-x-0 bottom-0 pt-16 pb-12 px-10" style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%)">
            <div class="w-10 h-0.5 mb-3 rounded-full" style="background-color: {{ $brandColor }}"></div>
            <p class="text-white font-black text-lg uppercase tracking-tight leading-none">{{ $name }}</p>
        </div>

    @elseif($isCarousel)
        {{-- PHOTO CAROUSEL MODE --}}
        @php
            $slidesToRender = $slideIndex !== null ? [$slideIndex] : [0, 1, 2];
            $cFontSize = 'text-lg';
        @endphp

        @foreach($slidesToRender as $idx)
            <div class="absolute inset-0" 
                 @if($slideIndex === null) x-show="slide === {{ $idx }}" @endif>
                
                <div class="absolute inset-0 bg-gray-800 flex items-center justify-center">
                    @if($preview)
                        <div class="text-center text-white/20 uppercase tracking-[0.2em]">
                            <p class="text-[10px] font-black">Slide {{ $idx + 1 }} / 3</p>
                        </div>
                    @endif
                </div>

                {{-- Header bar --}}
                <div class="absolute inset-x-0 top-0 h-1.5 z-10" style="background-color: {{ $brandColor }}"></div>

                {{-- Indicator --}}
                @if($preview)
                    <div class="absolute top-6 right-6 px-3 py-1 bg-black/40 backdrop-blur-md rounded-full z-10">
                        <p class="text-white text-[10px] font-black uppercase tracking-widest">{{ $idx + 1 }} / 3</p>
                    </div>
                @endif

                {{-- Bottom Overlay (only on first slide) --}}
                @if($idx === 0)
                    <div class="absolute inset-x-0 bottom-0 pt-16 pb-12 px-10" style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%)">
                        <div class="w-10 h-0.5 mb-3 rounded-full" style="background-color: {{ $brandColor }}"></div>
                        <p class="text-white font-black text-lg uppercase tracking-tight leading-none">{{ $name }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    {{-- Universal Footer (Company info) --}}
    @php
        $footerBg = $isNone ? ($layoutType === 'minimal' ? 'rgba(0,0,0,0.02)' : $currentStyle['sep']) : 'rgba(0,0,0,0.8)';
        $footerText = $isNone ? ($layoutType === 'minimal' ? '#64748B' : $currentStyle['label']) : '#FFFFFF';
        $footerHeight = '48px';
    @endphp
    
    <div class="absolute inset-x-0 bottom-0 flex items-center px-10 justify-between" 
         style="height: {{ $footerHeight }}; background-color: {{ $footerBg }}; backdrop-blur: {{ $isNone ? '0' : '8px' }}; border-top: 1px solid {{ $isNone ? $currentStyle['sep'] : 'transparent' }}">
        
        <div class="flex items-center gap-2">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="h-5 w-5 object-contain rounded shrink-0 {{ $isNone && $visualStyle === 'light_branded' ? '' : 'brightness-0 invert' }}">
            @endif
            <p class="font-bold uppercase tracking-[0.2em] text-[10px] truncate" style="color: {{ $footerText }}">{{ $companyLabel }}</p>
        </div>
        
        @if($preview)
            <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-full bg-copper text-white uppercase tracking-tighter shadow-sm">
                <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                IA
            </span>
        @endif
    </div>

    {{-- Preview specific buttons --}}
    @if($preview && isset($ct['id']))
        <div class="absolute top-4 right-4 z-20 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
             <button wire:click="startEditingTemplate({{ $ct['id'] }})"
                    class="w-8 h-8 rounded-full bg-black/30 backdrop-blur-sm text-white/70 hover:bg-copper hover:text-white transition-all flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button wire:click="deleteTemplate({{ $ct['id'] }})" wire:confirm="Supprimer ce modèle personnalisé ?"
                    class="w-8 h-8 rounded-full bg-black/30 backdrop-blur-sm text-white/70 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif
    @if($preview && $isCarousel)
             {{-- Carousel Arrows for Preview --}}
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between z-10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                <button @click.prevent="slide = (slide - 1 + 3) % 3" class="w-9 h-9 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click.prevent="slide = (slide + 1) % 3" class="w-9 h-9 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
</div>
