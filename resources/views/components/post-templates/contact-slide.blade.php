@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'visualStyle' => 'standard', // standard, glass, blueprint, kraft, beton
    'brandTextColor' => '#FFFFFF',
    'accentColor' => '#0F172A',
    'accentTextColor' => '#FFFFFF',
])

@php
    $bgStyle = match($visualStyle) {
        'glass' => '', // Handled by inline style
        'dark-glass' => '', 
        'minimal' => 'bg-transparent',
        'transparent' => 'bg-transparent',
        'blueprint' => 'bg-[#003366]',
        'kraft' => 'bg-[#d2b48c]',
        'beton' => 'bg-[#4a4a4a]',
        default => 'bg-slate-50',
    };

    $containerBgStyle = in_array($visualStyle, ['glass', 'dark-glass']) ? "background-color: $accentColor;" : "";

    $cardStyle = match($visualStyle) {
        'glass' => 'bg-white/10 backdrop-blur-2xl border border-white/20 text-white',
        'dark-glass' => 'bg-white/10 backdrop-blur-2xl border border-white/20 text-white shadow-2xl shadow-black/50',
        'minimal' => 'bg-white/10 backdrop-blur-[20px] border border-white/20 text-white shadow-3xl shadow-black/40',
        'transparent' => 'bg-white/90 backdrop-blur-2xl border border-white/40 text-slate-900 shadow-2xl',
        'blueprint' => 'bg-white/5 border-2 border-white/30 text-white font-mono',
        'kraft' => 'bg-white/40 border-2 border-black/10 text-slate-900 font-serif',
        'beton' => 'bg-black/20 border-2 border-white/10 text-white font-black',
        default => 'bg-white/80 backdrop-blur-xl shadow-2xl border border-white/40 text-slate-900',
    };

    // Contrast Logic for Brand Color
    $hex = str_replace('#', '', $brandColor);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    $isBrandColorTooLight = $yiq > 200; // Close to white

    // Icon Accent Logic
    $isLightBg = in_array($visualStyle, ['standard', 'kraft', 'transparent']);
    $isDarkGlass = in_array($visualStyle, ['glass', 'dark-glass', 'minimal']);

    if ($isDarkGlass) {
        $iconColor = '#FFFFFF';
    } elseif ($isLightBg) {
        // Fallback to dark if brand color is too light for light background
        $iconColor = $isBrandColorTooLight ? '#0f172a' : $brandColor;
    } else {
        $iconColor = $brandTextColor === '#FFFFFF' ? '#FBBF24' : $brandColor;
    }
    $accentStyle = "color: $iconColor;";

    // Branding Tag Logic
    if ($isDarkGlass) {
        $brandTagBg = 'rgba(255,255,255,0.1)';
        $brandTagBorder = 'rgba(255,255,255,0.2)';
        $brandTagText = '#FFFFFF';
    } else {
        $effectiveColor = $isBrandColorTooLight ? ($isDarkGlass ? $accentTextColor : '#0f172a') : $brandColor;
        $brandTagBg = ($isBrandColorTooLight ? 'rgba(255,255,255,0.05)' : $brandColor . '15');
        $brandTagBorder = ($isBrandColorTooLight ? 'rgba(255,255,255,0.1)' : $brandColor . '33');
        $brandTagText = $effectiveColor;
    }
    $brandTagStyle = "background-color: $brandTagBg; border-color: $brandTagBorder; color: $brandTagText;";
@endphp

<div class="absolute inset-0 flex items-center justify-center p-8 {{ $bgStyle }} overflow-hidden font-montserrat" style="{{ $containerBgStyle }}">
    {{-- Ambient glows --}}
    @if($isLightBg && $visualStyle !== 'transparent')
        <div class="absolute -top-24 -right-24 w-80 h-80 opacity-10 rounded-full blur-[100px] pointer-events-none" style="background-color: {{ $brandColor }}"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 opacity-10 rounded-full blur-[100px] pointer-events-none" style="background-color: {{ $brandColor }}"></div>
    @elseif(!$isLightBg && $visualStyle !== 'minimal')
        <div class="absolute -top-24 -right-24 w-80 h-80 opacity-20 rounded-full blur-[100px] pointer-events-none" style="background: radial-gradient(circle, {{ $brandColor }}, transparent 70%)"></div>
    @endif

    {{-- Contact Card --}}
    <div class="relative z-10 w-full max-w-[260px] rounded-[2.5rem] p-8 flex flex-col items-center text-center {{ $cardStyle }}">
        
        <h2 class="text-xl font-black tracking-tighter mb-1 leading-tight uppercase">{{ $companyLabel }}</h2>
        
        @if($trade || $city)
            <div class="mb-6 flex flex-wrap justify-center gap-1.5">
                @if($trade)
                    <span class="px-3 py-1 rounded-lg text-[7px] font-black uppercase tracking-[0.15em] border" style="{{ $brandTagStyle }}">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-3 py-1 rounded-lg text-[7px] font-black uppercase tracking-[0.15em] border" style="{{ $brandTagStyle }}">{{ $city }}</span>
                @endif
            </div>
        @else
            <div class="h-4"></div>
        @endif

        <div class="w-full space-y-4">
            {{-- Contact Items --}}
            @if($phone)
                <div class="flex items-center gap-3 text-left group">
                    <div class="w-9 h-9 rounded-[1rem] flex items-center justify-center shrink-0 shadow-md transition-transform group-hover:scale-105" 
                         style="background-color: {{ $isDarkGlass ? 'rgba(255,255,255,0.06)' : 'white' }}; border: 1px solid {{ $isDarkGlass ? 'rgba(255,255,255,0.08)' : '#f1f1f1' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" style="{{ $accentStyle }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[7px] font-black uppercase tracking-[0.2em] opacity-30 mb-0.5 leading-none">Téléphone</p>
                        <p class="text-[12px] font-black tracking-tight truncate leading-tight">{{ $phone }}</p>
                    </div>
                </div>
            @endif

            @if($email)
                <div class="flex items-center gap-3 text-left group">
                    <div class="w-9 h-9 rounded-[1rem] flex items-center justify-center shrink-0 shadow-md transition-transform group-hover:scale-105" 
                         style="background-color: {{ $isDarkGlass ? 'rgba(255,255,255,0.06)' : 'white' }}; border: 1px solid {{ $isDarkGlass ? 'rgba(255,255,255,0.08)' : '#f1f1f1' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" style="{{ $accentStyle }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[7px] font-black uppercase tracking-[0.2em] opacity-30 mb-0.5 leading-none">Email</p>
                        <p class="text-[11px] font-black tracking-tight truncate leading-tight">{{ $email }}</p>
                    </div>
                </div>
            @endif

            @if($website)
                <div class="flex items-center gap-3 text-left group">
                    <div class="w-9 h-9 rounded-[1rem] flex items-center justify-center shrink-0 shadow-md transition-transform group-hover:scale-105" 
                         style="background-color: {{ $isDarkGlass ? 'rgba(255,255,255,0.06)' : 'white' }}; border: 1px solid {{ $isDarkGlass ? 'rgba(255,255,255,0.08)' : '#f1f1f1' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" style="{{ $accentStyle }}" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[7px] font-black uppercase tracking-[0.2em] opacity-30 mb-0.5 leading-none">Site internet</p>
                        <p class="text-[11px] font-black tracking-tight truncate leading-tight">{{ preg_replace('(^https?://)', '', $website) }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-8 pt-6 w-full border-t border-white/5 items-center flex flex-col" style="border-top-color: {{ $isDarkGlass ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)' }}">
            <p class="text-[8px] font-black uppercase tracking-[0.4em] opacity-20">Contactez-nous</p>
        </div>
    </div>
</div>
