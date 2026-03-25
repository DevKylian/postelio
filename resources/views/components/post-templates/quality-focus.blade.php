@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'logoUrl' => null,
    'imageUrl' => null,
    'text' => 'La qualité et le choix des matériaux avant tout.',
    'preview' => false,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'La qualité et le choix des matériaux avant tout.') ? 'Le choix des meilleurs matériaux pour une durabilité garantie.' : $text;
    @endphp
@endif

<div class="w-full aspect-square bg-white relative overflow-hidden flex flex-col justify-end {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}" style="border: 6px solid {{ $brandColor }}">
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
    @else
        <div class="absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-400 select-none pointer-events-none">
            <div class="text-center">
                <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Détail Matière</p>
            </div>
        </div>
    @endif
    
    <div class="absolute inset-x-4 bottom-20 bg-black/70 backdrop-blur-sm p-4 rounded-2xl border border-white/10 shadow-xl z-10 text-center">
        @php
            $len = mb_strlen($text);
            $fontSize = 'text-xs';
            if ($len > 80) $fontSize = 'text-[9px]';
            elseif ($len > 40) $fontSize = 'text-[10px]';
        @endphp
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-2 text-white/50">Focus Qualité</p>
        <div class="max-h-[80px] overflow-hidden">
            <h3 class="text-white font-black {{ $fontSize }} uppercase leading-snug font-montserrat">{!! nl2br(e($text)) !!}</h3>
        </div>
    </div>

    <div class="absolute inset-x-0 bottom-0 py-2.5 px-6 flex items-center justify-center bg-black/80">
        @if ($logoUrl)
            <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded absolute right-6">
        @endif
        <p class="font-bold text-[9px] uppercase tracking-widest font-montserrat text-white/80">{{ $companyLabel }}</p>
    </div>
</div>
