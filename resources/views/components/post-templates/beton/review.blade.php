@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'customerName' => 'Client',
    'text' => '',
    'preview' => false,
])

<div class="w-full aspect-square bg-[#CBD5E1] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-2xl shadow-2xl transition-all duration-500 hover:-translate-y-2' : '' }}">
    {{-- Concrete Texture --}}
    <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
    
    @php
        $len = mb_strlen($text);
        $fontSize = 'text-2xl';
        if ($len > 200) $fontSize = 'text-base';
        elseif ($len > 100) $fontSize = 'text-lg';
        elseif ($len > 40) $fontSize = 'text-xl';
    @endphp
    
    <div class="flex-1 flex flex-col items-center justify-center p-12 relative z-10 w-full">
        <div class="bg-white border-4 border-slate-900 p-8 rounded-2xl shadow-[20px_20px_0px_0px_rgba(15,23,42,0.1)] w-full max-w-[400px]">
            <div class="flex gap-1 mb-6">
                @for($i=0; $i<5; $i++)
                    <svg class="w-6 h-6" style="color: {{ $brandColor }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            
            <div class="max-h-[240px] overflow-hidden">
                <p class="text-slate-900 {{ $fontSize }} font-black font-montserrat leading-snug uppercase mb-8">
                    "{{ $text }}"
                </p>
            </div>

            <div class="flex items-center justify-between border-t-2 border-slate-100 pt-6">
                <span class="text-slate-900 text-sm font-black uppercase font-montserrat">{{ $customerName }}</span>
                <span class="text-slate-900/40 text-[10px] font-bold uppercase tracking-widest">Client Vérifié</span>
            </div>
        </div>
    </div>

    <div class="h-16 bg-slate-900 px-10 flex items-center justify-between">
         <span class="text-white text-[10px] font-black font-montserrat uppercase tracking-[0.2em]">{{ $companyLabel }}</span>
         <div class="w-4 h-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
    </div>
</div>
