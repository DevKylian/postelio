@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'customerName' => 'Client',
    'text' => '',
    'logoUrl' => null,
    'preview' => false,
])

<div class="w-full aspect-square bg-slate-900 flex flex-col relative overflow-hidden {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
    
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0" style="background-image: radial-gradient({{ $brandColor }} 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>

    {{-- Backdrop glass --}}
    <div class="relative flex-1 flex flex-col items-center justify-center p-8">
        
        {{-- Floating Quote Icon --}}
        <div class="absolute top-12 left-10 w-16 h-16 rounded-2xl backdrop-blur-md bg-white/5 border border-white/10 flex items-center justify-center shadow-2xl">
            <svg class="w-8 h-8 text-white/40" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H16.017C14.9124 8 14.017 7.10457 14.017 6V5C14.017 3.89543 14.9124 3 16.017 3H19.017C21.2261 3 23.017 4.79086 23.017 7V15C23.017 18.3137 20.3307 21 17.017 21H14.017ZM1.017 21L1.017 18C1.017 16.8954 1.91243 16 3.017 16H6.017C6.56929 16 7.017 15.5523 7.017 15V9C7.017 8.44772 6.56929 8 6.017 8H3.017C1.91243 8 1.017 7.10457 1.017 6V5C1.017 3.89543 1.91243 3 3.017 3H6.017C8.22614 3 10.017 4.79086 10.017 7V15C10.017 18.3137 7.33071 21 4.017 21H1.017Z" />
            </svg>
        </div>

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
