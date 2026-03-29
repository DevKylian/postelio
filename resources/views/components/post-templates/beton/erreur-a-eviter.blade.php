@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
    'preview' => false,
])

@if($preview)
    @php
        $text = $text ?: 'L\'erreur fatale : négliger l\'étanchéité sous le carrelage de votre douche.';
    @endphp
@endif

<div class="w-full aspect-square bg-[#D1D5DB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    <div class="flex-1 flex flex-col items-center justify-center p-10 relative z-10">
        <div class="bg-gray-900 p-1 rounded-[2rem] shadow-2xl w-full">
            <div class="bg-white border-2 border-gray-900 p-10 rounded-[1.8rem]">
                <div class="flex items-center gap-6 mb-8">
                     <div class="w-16 h-16 rounded-2xl bg-gray-900 flex items-center justify-center shadow-xl shrink-0" style="background-color: {{ $brandColor }}">
                          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                     </div>
                     <span class="text-gray-900 text-xl font-black uppercase font-montserrat">ALERTE ERREUR</span>
                </div>

                @php
                    $len = mb_strlen($text);
                    $fontSize = 'text-lg';
                    if ($len > 120) $fontSize = 'text-sm';
                    elseif ($len > 60) $fontSize = 'text-base';
                @endphp

                <div class="max-h-[140px] overflow-hidden">
                    <h3 class="text-gray-900 {{ $fontSize }} font-black font-montserrat leading-tight mb-6 uppercase">
                        {{ $text }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="h-14 px-10 flex items-center justify-between">
         <span class="text-gray-900/40 text-[9px] font-black uppercase tracking-widest">{{ $companyLabel }}</span>
    </div>
</div>
