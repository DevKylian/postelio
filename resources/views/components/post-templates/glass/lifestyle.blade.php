@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
])

<div class="w-full aspect-square bg-slate-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">

    {{-- Photo Background --}}
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
    @else
        <div class="absolute inset-0 flex items-center justify-center bg-slate-800">
             <svg class="w-16 h-16 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </div>
    @endif

    {{-- Glass overlay card — fixed height, anchored to bottom --}}
    <div class="absolute inset-x-8 bottom-8 h-[100px] backdrop-blur-xl bg-slate-900/40 border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
        {{-- Icon floating above --}}
        <div class="absolute -top-10 left-1/2 -ml-10 w-20 h-20 rounded-2xl backdrop-blur-2xl bg-white/10 border border-white/30 flex items-center justify-center shadow-2xl">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-white/5" style="border: 2px solid {{ $brandColor }}">
                <svg class="w-6 h-6" style="color: {{ $brandColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        </div>

        @php
            $displayText = mb_substr($text ?: 'LIFESTYLE', 0, 45);
            $len = mb_strlen($displayText);
            $fontSize = 'text-sm';
            if ($len > 35) $fontSize = 'text-[10px]';
            elseif ($len > 25) $fontSize = 'text-xs';
        @endphp

        {{-- Text zone: fixed area inside the card --}}
        <div class="absolute inset-x-4 top-[16px] bottom-[8px] flex flex-col items-center justify-center text-center overflow-hidden">
            <h3 class="text-white {{ $fontSize }} font-black uppercase tracking-widest font-montserrat leading-tight max-w-[240px]" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $displayText }}</h3>
            <p class="text-white/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-1 truncate max-w-[200px]">{{ $companyLabel }}</p>
        </div>
    </div>
</div>
