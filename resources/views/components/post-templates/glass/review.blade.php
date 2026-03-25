@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'customerName' => 'Client',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

@if($preview)
    @php
        $customerName = 'Jean-Pierre D.';
        $text = $text ?: 'Une expérience parfaite de A à Z. L\'équipe a su transformer notre vision en réalité avec un professionnalisme exemplaire. Les délais ont été respectés et les finitions sont impeccables. Nous recommandons vivement pour vos projets !';
    @endphp
@endif

<div class="w-full aspect-square bg-slate-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0" style="background-image: radial-gradient({{ $brandColor }} 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>

    {{-- Backdrop glass --}}
    <div class="relative flex-1 flex flex-col items-center justify-center p-8">
        
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-8 shadow-2xl w-full max-w-[340px] relative z-20">
            <div class="flex items-center gap-1 mb-4">
                @for($i=0; $i<5; $i++)
                <svg class="w-4 h-4" style="color: {{ $brandColor }}" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                @endfor
            </div>

            @php
                $len = mb_strlen($text);
                $fontSize = 'text-lg';
                if ($len > 150) $fontSize = 'text-xs';
                elseif ($len > 80) $fontSize = 'text-sm';
                elseif ($len > 40) $fontSize = 'text-base';
            @endphp
            <div class="max-h-[220px] overflow-hidden">
                <p class="text-white {{ $fontSize }} font-medium leading-relaxed italic mb-6 text-center">
                    "{{ $text }}"
                </p>
            </div>

            <div class="flex items-center justify-between border-t border-white/10 pt-4">
                <span class="text-white/80 font-bold text-sm">{{ $customerName }}</span>
                <span class="text-white/40 text-[10px] font-bold uppercase tracking-widest">{{ $companyLabel }}</span>
            </div>
        </div>
    </div>

    {{-- Brand glow --}}
    <div class="absolute -bottom-20 -right-20 w-64 h-64 rounded-full blur-[100px] opacity-30" style="background-color: {{ $brandColor }}"></div>
</div>
