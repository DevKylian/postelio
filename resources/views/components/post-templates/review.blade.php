@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'customerName' => 'Client',
    'logoUrl' => null,
    'text' => '"Travail impeccable ! L\'équipe a été ponctuelle, propre et le résultat dépasse nos attentes."',
    'preview' => false,
])

<div class="w-full aspect-square flex flex-col items-center justify-center p-10 text-center relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="background-color: {{ $brandColor }}">
    <div class="flex space-x-2 mb-6">
        @for ($i = 0; $i < 5; $i++)
            <svg class="w-7 h-7 text-yellow-300 fill-current drop-shadow-md" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        @endfor
    </div>
    @php
        $len = mb_strlen($text);
        $fontSize = 'text-2xl';
        if ($len > 200) $fontSize = 'text-base';
        elseif ($len > 120) $fontSize = 'text-lg';
        elseif ($len > 60) $fontSize = 'text-xl';
    @endphp
    <div class="flex-1 flex items-center justify-center max-h-[280px] overflow-hidden">
        <p class="text-white font-black uppercase tracking-tight {{ $fontSize }} leading-tight font-montserrat drop-shadow-sm">
            {!! nl2br(e($text)) !!}
        </p>
    </div>
    <div class="mt-6 pt-4 border-t border-white/20 w-full">
        <div class="flex items-center justify-center gap-2">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="h-6 w-6 object-contain rounded">
            @endif
            <p class="text-white/80 font-bold text-xs uppercase tracking-widest font-montserrat">{{ $companyLabel }} • {{ $customerName }}</p>
        </div>
    </div>
    <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-black/10 rounded-full blur-2xl pointer-events-none"></div>
</div>
