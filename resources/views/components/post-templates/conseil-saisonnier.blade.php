@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'text' => 'Vérifiez vos canalisations avant les premiers grands froids.',
    'preview' => false,
])

@if($preview)
    @php
        $text = '3 gestes essentiels pour protéger votre toiture avant l\'hiver.';
    @endphp
@endif

<div class="w-full aspect-square relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="background-color: #0B1426; border-top: 8px solid {{ $brandColor }}">
    <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full pointer-events-none" style="background: rgba(255,255,255,0.03)"></div>
    <div class="absolute -top-5 -right-5 w-[104px] h-[104px] rounded-full pointer-events-none" style="background: rgba(255,255,255,0.04)"></div>
    <div class="absolute inset-0 p-10 flex flex-col justify-center">
        <span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span>
        <h3 class="font-black uppercase tracking-widest text-sm mb-5 font-montserrat" style="color: {{ $brandColor }}">Conseil Saisonnier</h3>
        @php
            $len = mb_strlen($text);
            $fontSize = 'text-lg';
            if ($len > 150) $fontSize = 'text-[11px]';
            elseif ($len > 80) $fontSize = 'text-sm';
            elseif ($len > 40) $fontSize = 'text-base';
        @endphp
        <div class="max-h-[160px] overflow-hidden">
            <p class="text-white font-black uppercase tracking-tight {{ $fontSize }} leading-snug font-montserrat">{!! nl2br(e($text)) !!}</p>
        </div>
    </div>
    <div class="absolute bottom-0 inset-x-0 border-t px-10 py-4" style="border-color: rgba(255,255,255,0.1)">
        <div class="flex items-center gap-2">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="h-5 w-5 object-contain rounded">
            @endif
            <p class="font-bold text-xs uppercase tracking-widest font-montserrat" style="color: {{ $brandColor }}">{{ $companyLabel }}</p>
        </div>
    </div>
</div>
