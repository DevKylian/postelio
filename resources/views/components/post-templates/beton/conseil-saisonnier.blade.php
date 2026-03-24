@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
    'preview' => false,
])

<div class="w-full aspect-square bg-[#CBD5E1] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10">
        <div class="bg-white border-2 border-slate-900 p-10 rounded-2xl shadow-[15px_15px_0px_0px_{{ $brandColor }}33] w-full max-w-[420px] relative">
            <div class="absolute -top-6 left-10 bg-slate-900 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-xl">DOSSIER TECHNIQUE</div>
            
            @php
                $len = mb_strlen($text);
                $fontSize = 'text-sm';
                if ($len > 250) $fontSize = 'text-xs';
            @endphp
            
            <div class="max-h-[160px] overflow-hidden">
                <p class="text-slate-900/60 {{ $fontSize }} font-bold font-montserrat leading-relaxed uppercase bg-slate-50 p-6 rounded-xl border border-slate-200">
                    {{ $text }}
                </p>
            </div>
        </div>
    </div>

    <div class="h-16 px-10 bg-slate-900 flex items-center justify-between">
         <span class="text-white text-[10px] font-black font-montserrat uppercase tracking-widest">{{ $companyLabel }}</span>
         <div class="w-16 h-1 rounded-full opacity-30" style="background-color: {{ $brandColor }}"></div>
    </div>
</div>
