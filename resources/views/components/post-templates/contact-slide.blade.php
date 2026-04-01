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
])

@php
    $bgStyle = match($visualStyle) {
        'glass' => 'bg-gradient-to-br from-slate-900 to-slate-800',
        'blueprint' => 'bg-[#003366]',
        'kraft' => 'bg-[#d2b48c]',
        'beton' => 'bg-[#4a4a4a]',
        default => 'bg-gray-50',
    };

    $cardStyle = match($visualStyle) {
        'glass' => 'bg-white/10 backdrop-blur-xl border border-white/20 text-white',
        'blueprint' => 'bg-white/5 border-2 border-white/30 text-white font-mono',
        'kraft' => 'bg-white/40 border-2 border-black/10 text-slate-900 font-serif',
        'beton' => 'bg-black/20 border-2 border-white/10 text-white font-black',
        default => 'bg-white shadow-2xl border border-gray-100 text-slate-800',
    };

    // Icon Accent Logic
    $isLightBg = in_array($visualStyle, ['standard', 'kraft']);
    if ($isLightBg) {
        // On light cards, if brand is too light, use Soft Black
        $iconColor = $brandTextColor === '#1E293B' ? '#1E293B' : $brandColor;
    } else {
        // On dark cards (glass, blueprint, beton), if brand is too dark, use White or Amber
        $iconColor = $brandTextColor === '#FFFFFF' ? '#FBBF24' : $brandColor;
    }
    $accentStyle = "color: $iconColor;";
@endphp

<div class="absolute inset-0 flex items-center justify-center p-4 {{ $bgStyle }} overflow-hidden">
    {{-- Decorative elements — Back layer --}}
    @if($visualStyle === 'standard')
        <div class="absolute -top-12 -right-12 w-48 h-48 opacity-[0.08] rounded-full blur-3xl pointer-events-none z-0" style="background-color: {{ $brandColor }}"></div>
        <div class="absolute -bottom-16 -left-16 w-56 h-56 opacity-[0.08] rounded-full blur-3xl pointer-events-none z-0" style="background-color: {{ $brandColor }}"></div>
    @endif

    {{-- Contact Card — Front layer --}}
    <div class="relative z-10 w-full max-w-[250px] rounded-[1.5rem] p-5 flex flex-col items-center text-center {{ $cardStyle }}">
        
        <h2 class="text-lg font-black tracking-tight mb-1 leading-tight">{{ $companyLabel }}</h2>
        
        @if($trade || $city)
            <div class="mb-4 flex flex-wrap justify-center gap-2">
                @if($trade)
                    <span class="px-2 py-0.5 rounded-md text-[7px] font-black uppercase tracking-[0.1em] opacity-40 border border-current shrink-0">
                        {{ $trade }}
                    </span>
                @endif
                @if($city)
                    <span class="px-2 py-0.5 rounded-md text-[7px] font-black uppercase tracking-[0.1em] opacity-40 border border-current shrink-0">
                        {{ $city }}
                    </span>
                @endif
            </div>
        @else
            <div class="h-2"></div>
        @endif

        <div class="w-full space-y-2.5">
            {{-- Contact Items --}}
            @if($phone)
                <div class="flex items-center gap-2.5 text-left">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" 
                         style="{{ $accentStyle }} background-color: {{ $isLightBg ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.1)' }};">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" style="{{ $accentStyle }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[6px] font-bold uppercase tracking-widest opacity-30 mb-0 leading-none">Téléphone</p>
                        <p class="text-[11px] font-bold truncate leading-tight mt-0.5">{{ $phone }}</p>
                    </div>
                </div>
            @endif

            @if($email)
                <div class="flex items-center gap-2.5 text-left">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" 
                         style="{{ $accentStyle }} background-color: {{ $isLightBg ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.1)' }};">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" style="{{ $accentStyle }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[6px] font-bold uppercase tracking-widest opacity-30 mb-0 leading-none">Email</p>
                        <p class="text-[10px] font-bold truncate leading-tight mt-0.5">{{ $email }}</p>
                    </div>
                </div>
            @endif

            @if($website)
                <div class="flex items-center gap-2.5 text-left">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" 
                         style="{{ $accentStyle }} background-color: {{ $isLightBg ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.1)' }};">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" style="{{ $accentStyle }}" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[6px] font-bold uppercase tracking-widest opacity-30 mb-0 leading-none">Site internet</p>
                        <p class="text-[10px] font-bold truncate leading-tight mt-0.5">{{ preg_replace('(^https?://)', '', $website) }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 pt-4 w-full" style="border-top: 1px solid {{ $isLightBg ? 'rgba(0,0,0,0.08)' : 'rgba(255,255,255,0.1)' }}">
            <p class="text-[8px] font-black uppercase tracking-[0.25em] opacity-30">Contactez-nous</p>
        </div>
    </div>
</div>
