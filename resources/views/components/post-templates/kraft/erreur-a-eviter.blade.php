@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

<div class="w-full aspect-square bg-[#BC9A7E] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Kraft Texture Overlay --}}
    <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
    
    {{-- Safety Stripes Border Top --}}
    <div class="h-6 w-full flex relative z- 20">
        @for($i=0; $i<20; $i++)
            <div class="flex-1 h-full skew-x-[35deg] {{ $i % 2 == 0 ? 'bg-orange-600' : 'bg-slate-900' }}"></div>
        @endfor
    </div>

    <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10">
        <div class="bg-white border-[6px] border-slate-900 p-10 shadow-[25px_25px_0px_0px_rgba(15,23,42,0.1)] w-full max-w-[420px] relative">
            <div class="flex items-center gap-6 mb-8">
                 <div class="w-16 h-16 bg-orange-600 flex items-center justify-center shadow-xl border-4 border-slate-900 rotate-3 shrink-0" style="background-color: {{ $brandColor }}">
                      <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                 </div>
                 <span class="text-slate-900 text-xl font-black uppercase font-montserrat">ALERTE ERREUR</span>
            </div>

            @php
                $len = mb_strlen($text);
                $fontSize = 'text-2xl';
                if ($len > 80) $fontSize = 'text-base';
                elseif ($len > 40) $fontSize = 'text-lg';
            @endphp
            <div class="max-h-[160px] overflow-hidden">
                <h3 class="text-slate-900 {{ $fontSize }} font-black font-montserrat leading-[1.1] mb-8 uppercase italic underline decoration-[6px] underline-offset-8 text-center" style="text-decoration-color: {{ $brandColor }}">
                    {{ $text }}
                </h3>
            </div>
        </div>
    </div>

    <div class="h-16 px-10 bg-slate-900 flex items-center justify-between border-t-4 border-white/10">
        <span class="text-white text-xs font-black uppercase font-mono-technical tracking-[0.3em]">{{ $companyLabel }}</span>
    </div>

    {{-- Safety Stripes Border Bottom --}}
    <div class="h-6 w-full flex relative z-20">
        @for($i=0; $i<20; $i++)
            <div class="flex-1 h-full -skew-x-[35deg] {{ $i % 2 == 0 ? 'bg-orange-600' : 'bg-slate-900' }}"></div>
        @endfor
    </div>
</div>
