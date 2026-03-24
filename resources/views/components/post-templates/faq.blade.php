@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'question' => 'Pourquoi choisir une isolation par l\'extérieur ?',
    'preview' => false,
])

<div class="w-full aspect-square bg-midnight flex flex-col p-10 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="border-top: 8px solid {{ $brandColor }}">
    <div class="flex-1 flex flex-col justify-center">
        <span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span>
        <h3 class="font-black uppercase tracking-widest text-sm mb-4" style="color: {{ $brandColor }}">FAQ Artisan</h3>
        @php
            $qLen = mb_strlen($question);
            $qFontSize = 'text-xl';
            if ($qLen > 120) $qFontSize = 'text-sm';
            elseif ($qLen > 60) $qFontSize = 'text-base';
            elseif ($qLen > 30) $qFontSize = 'text-lg';
        @endphp
        <div class="max-h-[180px] overflow-hidden">
            <p class="text-white font-black uppercase tracking-tight {{ $qFontSize }} leading-snug font-montserrat">{!! nl2br(e($question)) !!}</p>
        </div>
    </div>
    <div class="pt-4 border-t border-white/10">
        <div class="flex items-center gap-2">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="h-5 w-5 object-contain rounded">
            @endif
            <p class="font-bold text-xs uppercase tracking-widest font-montserrat" style="color: {{ $brandColor }}">{{ $companyLabel }}</p>
        </div>
    </div>
    <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-white/5 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
    </svg>
</div>
