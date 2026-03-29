@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

@if($preview)
    @php
        $text = $text ?: 'L\'erreur fatale : négliger l\'étanchéité sous le carrelage de votre douche.';
    @endphp
@endif

<div class="w-full aspect-square bg-gray-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-red-500/20 group-hover:-translate-y-2' : '' }}">
    
    {{-- Danger glow --}}
    <div class="absolute -top-24 -left-24 w-64 h-64 rounded-full blur-[100px] opacity-20 bg-red-600"></div>

    <div class="flex-1 flex flex-col items-center justify-center p-8 relative z-10">
        
        {{-- Warning Icon Glass --}}
        <div class="w-14 h-14 rounded-2xl backdrop-blur-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center shadow-2xl mb-4 animate-pulse">
             <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
             </svg>
        </div>

        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl w-full max-w-[320px] text-center border-t-4 border-t-red-500">
            <span class="text-[10px] font-black text-red-500/60 uppercase tracking-[0.3em] block mb-2 font-montserrat">Attention : Erreur courante</span>
            @php
                $len = mb_strlen($text);
                $fontSize = 'text-base';
                if ($len > 150) $fontSize = 'text-[10px]';
                elseif ($len > 80) $fontSize = 'text-xs';
                elseif ($len > 40) $fontSize = 'text-sm';
            @endphp
            <div class="max-h-[140px] overflow-hidden">
                <h3 class="text-white {{ $fontSize }} font-bold leading-tight">
                    {{ $text }}
                </h3>
            </div>
        </div>

    </div>

    <div class="px-8 py-4 backdrop-blur-md bg-white/5 border-t border-white/10 flex items-center justify-between">
        <span class="text-white/30 text-[8px] font-black uppercase tracking-[0.3em] font-montserrat">{{ $companyLabel }}</span>
        <div class="flex items-center gap-1">
             @for($i=0; $i<3; $i++)
             <div class="w-1.5 h-1.5 rounded-full bg-red-500/40"></div>
             @endfor
        </div>
    </div>
</div>
