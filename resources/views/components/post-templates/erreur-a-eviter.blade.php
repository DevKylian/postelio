@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'text' => 'Attendre que la fuite empire avant d\'appeler un professionnel.',
    'preview' => false,
])

@if($preview)
    @php
        $text = 'L\'erreur fatale : négliger l\'étanchéité sous le carrelage de votre douche.';
    @endphp
@endif

<div class="w-full aspect-square relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="background-color: #111827; border-top: 8px solid {{ $brandColor }};">
    <div class="absolute inset-0 overflow-hidden pointer-events-none" style="opacity:0.045">
        <div class="absolute bg-white w-full h-3 top-1/2 left-0" style="transform: rotate(45deg) scaleX(1.6) translateY(-50%)"></div>
        <div class="absolute bg-white w-full h-3 top-1/2 left-0" style="transform: rotate(-45deg) scaleX(1.6) translateY(-50%)"></div>
    </div>
    <div class="absolute inset-0 p-10 flex flex-col justify-center">
        <span class="inline-block w-12 h-1 mb-6" style="background-color: {{ $brandColor }}"></span>
        <h3 class="font-black uppercase tracking-widest text-sm mb-5 font-montserrat" style="color: {{ $brandColor }}">Erreur à Éviter</h3>
        @php
            $len = mb_strlen($text);
            $fontSize = 'text-2xl';
            if ($len > 150) $fontSize = 'text-base';
            elseif ($len > 80) $fontSize = 'text-lg';
            elseif ($len > 40) $fontSize = 'text-xl';
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
