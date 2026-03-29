@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

@if($preview)
    @php
        $text = $text ?: '3 gestes essentiels pour protéger votre toiture avant l\'hiver.';
    @endphp
@endif

<div class="w-full aspect-square bg-gray-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    {{-- Background Season Glow --}}
    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full blur-[120px] opacity-20" style="background-color: {{ $brandColor }}"></div>
    <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full blur-[100px] opacity-10 bg-gray-500"></div>

    <div class="flex-1 flex flex-col items-center justify-center p-8 relative z-10">
        
        {{-- Season Icon Glass --}}
        <div class="w-14 h-14 rounded-2xl backdrop-blur-2xl bg-white/10 border border-white/20 flex items-center justify-center shadow-2xl mb-4 transition-transform duration-500">
             <svg class="w-7 h-7" style="color: {{ $brandColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
             </svg>
        </div>

        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl w-full max-w-[320px] text-center">
            <span class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] block mb-2 font-montserrat">Conseil de saison</span>
            @php
                $len = mb_strlen($text);
                $fontSize = 'text-base';
                if ($len > 150) $fontSize = 'text-[10px]';
                elseif ($len > 80) $fontSize = 'text-xs';
                elseif ($len > 40) $fontSize = 'text-sm';
            @endphp
            <div class="max-h-[160px] overflow-hidden">
                <p class="text-white {{ $fontSize }} font-bold leading-relaxed">
                    {{ $text }}
                </p>
            </div>
        </div>

    </div>

    <div class="absolute bottom-6 left-1/2 -ml-20 w-40 h-10 border border-white/10 rounded-full bg-white/5 backdrop-blur-md flex items-center justify-center">
        <span class="text-white/30 text-[8px] font-black uppercase tracking-[0.3em] font-montserrat">{{ $companyLabel }}</span>
    </div>
</div>
