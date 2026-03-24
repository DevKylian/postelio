@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

<div class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10">
        
        {{-- Technical Memo Box --}}
        <div class="bg-white border-4 border-slate-900 p-10 shadow-[20px_20px_0px_0px_rgba(15,23,42,0.1)] w-full max-w-[400px] relative">
            <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-24 h-24 bg-[#BC9A7E] border-4 border-slate-900 rounded-full flex items-center justify-center shadow-xl p-4">
                 <svg class="w-full h-full text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                 </svg>
            </div>

            <div class="mt-12 text-center">
                <h3 class="text-slate-900 text-xl font-black font-montserrat leading-tight mb-6 uppercase">CONSEIL D'EXPERT</h3>
                @php
                    $len = mb_strlen($text);
                    $fontSize = 'text-sm';
                    if ($len > 250) $fontSize = 'text-[9px]';
                    elseif ($len > 120) $fontSize = 'text-xs';
                @endphp
                <div class="max-h-[160px] overflow-hidden">
                    <p class="text-slate-900 {{ $fontSize }} font-black font-mono-technical leading-normal uppercase bg-slate-50 p-4 border-2 border-dashed border-slate-200">
                        {{ $text }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div class="h-16 bg-slate-900 px-10 flex items-center justify-between">
        <span class="text-white text-[10px] font-black font-montserrat uppercase tracking-widest">{{ $companyLabel }}</span>
        <div class="w-10 h-3" style="background-color: {{ $brandColor }}"></div>
    </div>
</div>
