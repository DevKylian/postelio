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
    'brandTextColor' => '#FFFFFF',
    'accentColor' => '#0F172A',
    'accentTextColor' => '#FFFFFF',
    'exporting' => false,
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
            'bg' => $accentColor,
            'title' => $accentTextColor,
            'text' => $accentTextColor . 'b3', // 70% opacity in hex approx
            'label' => $brandColor,
            'glow' => $brandColor,
            'card' => 'rgba(255,255,255,0.05)',
            'border' => 'rgba(255,255,255,0.1)'
        ],
        'light_branded' => [
            'bg' => '#F8FAFC',
            'title' => '#0F172A',
            'text' => '#475569',
            'label' => $brandColor,
            'glow' => $brandColor,
            'card' => '#FFFFFF',
            'border' => 'rgba(0,0,0,0.05)'
        ],
        'brand_color' => [
            'bg' => $brandColor,
            'title' => $brandTextColor,
            'text' => $brandTextColor,
            'label' => $brandTextColor,
            'glow' => $brandTextColor === '#1E293B' ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.3)',
            'card' => 'rgba(255,255,255,0.1)',
            'border' => 'rgba(255,255,255,0.2)'
        ]
    ];
    
    $currentStyle = $styles[$visualStyle] ?? $styles['dark_branded'];
@endphp

<div class="w-full {{ ($aspectRatio ?? '4:5') === '4:5' ? 'aspect-[4/5]' : 'aspect-square' }} bg-gray-50 relative overflow-hidden font-montserrat {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}"
     style="background-color: {{ $currentStyle['bg'] }}">

    {{-- Ambient glows --}}
    <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full blur-[100px] pointer-events-none opacity-[0.15]" style="background: radial-gradient(circle, {{ $currentStyle['glow'] }}, transparent 70%)"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full blur-[100px] pointer-events-none opacity-[0.1]" style="background: radial-gradient(circle, {{ $currentStyle['glow'] }}, transparent 70%)"></div>

    @if($isNone)
        {{-- LAYOUTS FOR TEXT ONLY --}}
        
        @if($layoutType === 'centered')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = 'text-lg';
                if ($len > 250) $fSize = 'text-xs';
                elseif ($len > 150) $fSize = 'text-sm';
                elseif ($len > 80) $fSize = 'text-base';
            @endphp
            <div class="absolute inset-0 flex flex-col items-center justify-center p-10 text-center relative z-10">
                <div class="backdrop-blur-xl p-8 rounded-[2.5rem] border shadow-2xl w-full max-w-[320px]" style="background: {{ $currentStyle['card'] }}; border-color: {{ $currentStyle['border'] }}">
                    <div class="w-10 h-1 mb-6 rounded-full mx-auto" style="background-color: {{ $brandColor }}"></div>
                    <h3 class="font-black uppercase tracking-tight mb-4 text-2xl" style="color: {{ $currentStyle['title'] }}; line-height: 1.1;">{{ $name }}</h3>
                    <div class="max-h-[160px] overflow-hidden">
                        <p class="font-bold leading-tight" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize === 'text-lg' ? '1.125rem' : ($fSize === 'text-base' ? '1rem' : ($fSize === 'text-sm' ? '0.875rem' : '0.75rem')) }};">{!! nl2br(e($displayText)) !!}</p>
                    </div>
                </div>
            </div>

        @elseif($layoutType === 'quote')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = 'text-xl';
                if ($len > 150) $fSize = 'text-base';
                elseif ($len > 80) $fSize = 'text-lg';
            @endphp
            <div class="absolute inset-0 flex flex-col items-center justify-center p-10 text-center relative z-10">
                 <div class="backdrop-blur-xl p-8 rounded-[2.5rem] border shadow-2xl w-full max-w-[320px]" style="background: {{ $currentStyle['card'] }}; border-color: {{ $currentStyle['border'] }}">
                    <div class="flex items-center justify-center gap-3 mb-6">
                        <div class="w-8 h-px" style="background-color: {{ $brandColor }}"></div>
                        <span class="text-[10px] font-black uppercase tracking-[0.3em]" style="color: {{ $brandColor }}">{{ $name }}</span>
                        <div class="w-8 h-px" style="background-color: {{ $brandColor }}"></div>
                    </div>
                    
                    <div class="max-h-[180px] overflow-hidden">
                         <p class="font-black italic leading-tight tracking-tight" style="color: {{ $currentStyle['title'] }}; font-size: {{ $fSize === 'text-xl' ? '1.25rem' : ($fSize === 'text-lg' ? '1.125rem' : '1rem') }};">
                            "{!! nl2br(e($displayText)) !!}"
                        </p>
                    </div>
                </div>
            </div>

        @elseif($layoutType === 'bold_title')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = 'text-lg';
                if ($len > 200) $fSize = 'text-xs';
                elseif ($len > 120) $fSize = 'text-sm';
            @endphp
            <div class="absolute inset-x-6 top-10 z-10">
                <div class="backdrop-blur-xl p-6 rounded-3xl border shadow-xl text-center" style="background: {{ $brandColor !!}; border-color: rgba(255,255,255,0.1)">
                     <h3 class="font-black uppercase tracking-tighter text-2xl leading-none" style="color: {{ $brandTextColor }}">{{ $name }}</h3>
                </div>
            </div>
            <div class="flex-1 flex flex-col justify-center px-10 pt-24 pb-20">
                <div class="backdrop-blur-xl p-8 rounded-3xl border shadow-xl" style="background: {{ $currentStyle['card'] }}; border-color: {{ $currentStyle['border'] }}">
                    <div class="w-10 h-1 mb-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
                    <div class="max-h-[160px] overflow-hidden">
                        <p class="font-black leading-tight tracking-tight" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize === 'text-lg' ? '1.125rem' : ($fSize === 'text-sm' ? '0.875rem' : '0.75rem') }};">{!! nl2br(e($displayText)) !!}</p>
                    </div>
                </div>
            </div>

        @elseif($layoutType === 'split')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = 'text-lg';
                if ($len > 250) $fSize = 'text-xs';
                elseif ($len > 150) $fSize = 'text-sm';
            @endphp
            <div class="h-1/2 w-full flex flex-col justify-center px-10 pb-4 relative z-10" style="background-color: {{ $brandColor }}">
                <div class="w-10 h-1 bg-white/30 mb-4 rounded-full"></div>
                <h3 class="font-black uppercase text-white tracking-tighter text-3xl leading-none" style="color: {{ $brandTextColor }}">{{ $name }}</h3>
            </div>
            <div class="h-1/2 w-full flex flex-col justify-center px-10 pt-4 relative z-10">
                <div class="w-10 h-1 mb-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
                <div class="max-h-[120px] overflow-hidden">
                    <p class="font-black leading-tight tracking-tight" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize === 'text-lg' ? '1.125rem' : ($fSize === 'text-sm' ? '0.875rem' : '0.75rem') }};">{!! nl2br(e($displayText)) !!}</p>
                </div>
            </div>

        @elseif($layoutType === 'minimal')
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = 'text-xl';
                if ($len > 250) $fSize = 'text-sm';
                elseif ($len > 150) $fSize = 'text-base';
            @endphp
            <div class="absolute inset-0 flex flex-col justify-center p-10 bg-white z-10">
                <div class="absolute left-0 top-0 bottom-0 w-2" style="background-color: {{ $brandColor }}"></div>
                <div>
                    <h3 class="font-black uppercase tracking-tighter mb-6 text-2xl" style="color: #0F172A;">{{ $name }}</h3>
                    <div class="w-12 h-1 mb-8" style="background-color: {{ $brandColor }}"></div>
                    <div class="max-h-[220px] overflow-hidden">
                        <p class="font-black text-slate-500 leading-tight tracking-tight" style="font-size: {{ $fSize === 'text-xl' ? '1.25rem' : ($fSize === 'text-base' ? '1rem' : '0.875rem') }};">{!! nl2br(e($displayText)) !!}</p>
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
                    'image' => $imageUrl,
                    'brandTextColor' => $brandTextColor,
                ]) !!}
            </div>

        @else {{-- standard --}}
            @php
                $len = mb_strlen($displayText ?? '');
                $fSize = 'text-xl';
                if ($len > 250) $fSize = 'text-sm';
                elseif ($len > 150) $fSize = 'text-base';
                elseif ($len > 80) $fSize = 'text-lg';
            @endphp
            <div class="absolute left-0 top-0 bottom-0 w-2" style="background-color: {{ $brandColor }}"></div>
            <div class="absolute inset-0 flex flex-col justify-center px-12 py-10 z-10">
                <div>
                     <h3 class="font-black uppercase leading-none mb-4 text-3xl tracking-tight" style="color: {{ $currentStyle['title'] }}">{{ $name }}</h3>
                    <div class="w-12 h-1 mb-8 rounded-full" style="background-color: {{ $brandColor }}"></div>
                    <div class="max-h-[240px] overflow-hidden">
                        <p class="font-black leading-tight tracking-tight" style="color: {{ $currentStyle['text'] }}; font-size: {{ $fSize === 'text-xl' ? '1.25rem' : ($fSize === 'text-lg' ? '1.125rem' : ($fSize === 'text-base' ? '1rem' : '0.875rem')) }};">{!! nl2br(e($displayText)) !!}</p>
                    </div>
                </div>
            </div>
        @endif

    @elseif($isSingle)
        {{-- PHOTO SINGLE MODE --}}
        <div class="absolute inset-0 bg-slate-200 flex items-center justify-center">
             @if($imageUrl)
                <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
             @elseif($preview)
                <div class="text-center text-slate-400 uppercase tracking-[0.2em]">
                    <svg class="w-16 h-16 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-xs font-black">Votre photo</p>
                </div>
            @endif
        </div>
        {{-- Header bar --}}
        <div class="absolute inset-x-0 top-0 h-1.5 z-10" style="background-color: {{ $brandColor }}"></div>
        {{-- Overlay bas avec nom template --}}
        <div class="absolute inset-x-0 bottom-0 pt-24 pb-20 px-10" style="background: linear-gradient(to top, {{ $accentColor }}f2 0%, transparent 100%)">
            <div class="w-12 h-1 mb-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
            <p class="font-black text-2xl uppercase tracking-tight leading-none" style="color: {{ $accentTextColor }}">{{ $name }}</p>
        </div>

    @elseif($isCarousel)
        {{-- PHOTO CAROUSEL MODE --}}
        @php
            $slidesToRender = $slideIndex !== null ? [$slideIndex] : [0, 1, 2];
        @endphp

        @foreach($slidesToRender as $idx)
            <div class="absolute inset-0" 
                 @if($slideIndex === null) x-show="slide === {{ $idx }}" @endif>
                
                <div class="absolute inset-0 bg-slate-200 flex items-center justify-center">
                    @if($preview)
                        <div class="text-center text-slate-400 uppercase tracking-[0.2em]">
                            <p class="text-[10px] font-black">Slide {{ $idx + 1 }} / 3</p>
                        </div>
                    @endif
                </div>

                {{-- Header bar --}}
                <div class="absolute inset-x-0 top-0 h-1.5 z-10" style="background-color: {{ $brandColor }}"></div>

                {{-- Indicator --}}
                @if($preview)
                    <div class="absolute top-8 right-8 px-4 py-1.5 bg-slate-900/40 backdrop-blur-md rounded-full z-10 border border-white/10 shadow-xl">
                        <p class="text-white text-[10px] font-black uppercase tracking-[0.2em]">{{ $idx + 1 }} / 3</p>
                    </div>
                @endif

                {{-- Bottom Overlay (only on first slide) --}}
                @if($idx === 0)
                    <div class="absolute inset-x-0 bottom-0 pt-24 pb-20 px-10" style="background: linear-gradient(to top, {{ $accentColor }}f2 0%, transparent 100%)">
                        <div class="w-12 h-1 mb-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
                        <p class="font-black text-2xl uppercase tracking-tight leading-none" style="color: {{ $accentTextColor }}">{{ $name }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    {{-- Universal Footer (Modern Glass Branding) --}}
    @php
        $footerAlpine = $slideIndex === null ? 'x-show="!$isCarousel || slide === 0"' : '';
    @endphp
    <div class="absolute bottom-0 inset-x-0 p-6 z-20" {!! $footerAlpine !!}>
        <div class="backdrop-blur-xl rounded-2xl p-4 shadow-2xl border flex items-center justify-between" 
             style="background: {{ $isNone ? ($layoutType === 'minimal' ? 'rgba(15,23,42,0.03)' : 'rgba(255,255,255,0.8)') : 'rgba(255,255,255,0.85)' }}; 
                    border-color: {{ $isNone && $layoutType === 'minimal' ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.2)' }}">
            
            <div class="flex items-center gap-2.5">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded opacity-80 {{ $isNone && $visualStyle === 'light_branded' ? '' : 'brightness-0 invert' }}">
                @endif
                <p class="font-black uppercase tracking-[0.25em] text-[9px] truncate" style="color: {{ $isNone && $layoutType === 'minimal' ? '#94A3B8' : ($isNone && $visualStyle === 'light_branded' ? '#0F172A' : $accentTextColor) }}">{{ $companyLabel }}</p>
            </div>
            
            @if($preview)
                <span class="inline-flex items-center gap-1 text-[8px] font-black px-2 py-0.5 rounded-lg bg-slate-900 text-white uppercase tracking-widest shadow-lg border border-slate-800">
                    <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Custom
                </span>
            @endif
        </div>
    </div>

    {{-- Preview specific buttons --}}
    @if($preview && isset($ct['id']) && !$exporting)
        <div class="absolute top-4 left-4 z-20 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
             <button wire:click="startEditingTemplate({{ $ct['id'] }})"
                    class="w-8 h-8 rounded-full bg-slate-900/40 backdrop-blur-md text-white/70 hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center border border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <button wire:click="deleteTemplate({{ $ct['id'] }})" wire:confirm="Supprimer ce modèle personnalisé ?"
                    class="w-8 h-8 rounded-full bg-slate-900/40 backdrop-blur-md text-white/70 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center border border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif
    
    @if($preview && $isCarousel && !$exporting)
             {{-- Carousel Arrows for Preview --}}
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between z-10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                <button @click.prevent="slide = (slide - 1 + 3) % 3" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click.prevent="slide = (slide + 1) % 3" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
</div>

