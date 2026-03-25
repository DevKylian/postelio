@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'SUR LE TERRAIN') ? 'La passion du métier se lit dans chaque geste.' : $text;
    @endphp
@endif

<div class="w-full aspect-square bg-slate-900 relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">

    {{-- Photo Background --}}
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
    @else
        <div class="absolute inset-0 flex items-center justify-center bg-slate-800">
             <svg class="w-16 h-16 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </div>
    @endif

    {{-- Glass card — centered vertically in lower half --}}
    <div class="absolute inset-x-10 bottom-[40px] h-[90px] backdrop-blur-xl bg-slate-900/40 border border-white/20 rounded-2xl shadow-2xl">

        @php
            $displayText = $text ?: 'LIFESTYLE';
            $len = mb_strlen($displayText);
            $fontSize = 'text-sm';
            if ($len > 25) $fontSize = 'text-[10px]';
            elseif ($len > 18) $fontSize = 'text-xs';
        @endphp

        {{-- Text + company --}}
        <div class="absolute inset-x-4 top-[14px] bottom-[6px] flex flex-col items-center justify-center text-center overflow-hidden">
            <h3 class="text-white {{ $fontSize }} font-black uppercase tracking-widest font-montserrat leading-tight max-w-[220px]" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $displayText }}</h3>
            <p class="text-white/60 text-[9px] font-bold uppercase tracking-[0.2em] mt-1 truncate max-w-[180px]">{{ $companyLabel }}</p>
        </div>
    </div>
</div>
