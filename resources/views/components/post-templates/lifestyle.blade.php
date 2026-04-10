@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => 'La passion du métier se lit dans chaque geste.',
    'imageUrl' => null,
    'logoUrl' => null,
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'slideIndex' => null,
    'visualStyle' => 'standard',
    'preview' => false,
    'brandTextColor' => '#FFFFFF',
    'accentColor' => '#0F172A',
    'accentTextColor' => '#FFFFFF',
    'exporting' => false,
])

@php
    if($preview) {
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = $text ?? 'La passion du métier se lit dans chaque geste.';
        $phone = $phone ?? '01 23 45 67 89';
        $email = $email ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade ?? 'Artisan';
        $city = $city ?? 'Ma Ville';
    }

    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total = 1 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
    
    $len = mb_strlen($text ?? '');
    $fontSize = $len > 80 ? 'text-xs' : ($len > 40 ? 'text-sm' : 'text-base');
    $inlineFontSize = $len > 25 ? '10px' : ($len > 18 ? '11px' : '13px');
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? '4:5') === '4:5' ? '4/5' : '1/1' }}] relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }} font-montserrat">

    {{-- Slide 0: Main Content --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        
        @if($visualStyle === 'blueprint')
            {{-- BLUEPRINT style --}}
            <div class="absolute inset-0 bg-[#0047AB]">
                <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 30px 30px;"></div>
                <div class="absolute top-[20px] left-[20px] right-[20px] bottom-[110px] bg-white p-1.5 overflow-hidden">
                    @if($imageUrl) <img src="{{ $imageUrl }}" class="w-full h-full object-cover"> @else <div class="w-full h-full bg-slate-100"></div> @endif
                </div>
                <div class="absolute bottom-[70px] inset-x-5 flex items-center justify-center">
                    <div class="bg-white/10 border border-white/30 px-4 py-2 text-center max-w-full">
                         <span class="text-white font-black uppercase tracking-[0.15em] leading-tight block truncate font-mono" style="font-size: {{ $inlineFontSize }}">{{ $text }}</span>
                    </div>
                </div>
                <div class="absolute bottom-0 inset-x-0 h-[60px] bg-white border-t-2 border-[#0047AB] px-7 flex flex-col justify-center gap-0.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[#0047AB] font-black uppercase tracking-widest text-[10px] truncate font-mono">{{ $companyLabel }}</span>
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $brandColor }}; border: 1px solid rgba(0,71,171,0.2);"></div>
                    </div>
                    <div class="flex items-center gap-1.5 opacity-50">
                        @if($trade)<span class="text-[#0047AB] text-[6px] font-black uppercase border border-current px-1 font-mono">{{ $trade }}</span>@endif
                        @if($city)<span class="text-[#0047AB] text-[6px] font-black uppercase border border-current px-1 font-mono">{{ $city }}</span>@endif
                    </div>
                </div>
            </div>
        @elseif($visualStyle === 'kraft')
            {{-- KRAFT style --}}
            <div class="absolute inset-0 bg-[#BC9A7E]">
                <div class="absolute inset-0 opacity-40 pointer-events-none mix-blend-multiply" style="background-image: url('https://www.transparenttextures.com/patterns/cardboard.png');"></div>
                <div class="absolute top-7 left-7 right-7 bottom-[110px] border-8 border-slate-900 bg-white p-2.5 transform rotate-1 overflow-hidden">
                    @if($imageUrl) <img src="{{ $imageUrl }}" class="w-full h-full object-cover"> @else <div class="w-full h-full bg-slate-100"></div> @endif
                </div>
                <div class="absolute bottom-0 inset-x-0 h-[85px] bg-[#CBB198] border-t-4 border-slate-900 px-7 flex items-center justify-between">
                    <div class="flex flex-col max-w-[200px]">
                        <span class="text-slate-900 font-black uppercase tracking-tight leading-none truncate font-serif" style="font-size: {{ $inlineFontSize }}">{{ $text }}</span>
                        <div class="flex flex-col gap-0.5 mt-1.5 opacity-40">
                             <span class="text-slate-900 text-[8px] font-black uppercase font-mono">{{ $companyLabel }}</span>
                             <div class="flex items-center gap-1">
                                @if($trade)<span class="text-slate-900 text-[6px] font-black uppercase border border-slate-900 px-1">{{ $trade }}</span>@endif
                                @if($city)<span class="text-slate-900 text-[6px] font-black uppercase border border-slate-900 px-1">{{ $city }}</span>@endif
                             </div>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full border-4 border-slate-900 flex items-center justify-center shrink-0">
                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $brandColor }}"></div>
                    </div>
                </div>
            </div>

        @elseif($visualStyle === 'beton')
            {{-- BETON style --}}
            <div class="absolute inset-0 bg-[#4a4a4a]">
                <div class="absolute inset-0 opacity-30 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/concrete-wall.png');"></div>
                <div class="absolute top-5 left-5 right-5 bottom-[110px] overflow-hidden">
                    @if($imageUrl) <img src="{{ $imageUrl }}" class="w-full h-full object-cover"> @else <div class="w-full h-full bg-slate-500"></div> @endif
                </div>
                <div class="absolute bottom-[70px] inset-x-5 flex items-center justify-center">
                    <div class="bg-white/5 border border-white/20 px-4 py-1.5 text-center max-w-full backdrop-blur-sm">
                         <span class="text-white font-black uppercase tracking-[0.15em] leading-tight block truncate" style="font-size: {{ $inlineFontSize }}">{{ $text }}</span>
                    </div>
                </div>
                <div class="absolute bottom-0 inset-x-0 h-[60px] bg-black/30 px-7 flex flex-col justify-center gap-0.5">
                    <div class="flex items-center justify-between">
                        <span class="text-white font-black uppercase tracking-widest text-[10px] opacity-80">{{ $companyLabel }}</span>
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $brandColor }}"></div>
                    </div>
                    <div class="flex items-center gap-1.5 opacity-40">
                        @if($trade)<span class="text-white text-[6px] font-black uppercase border border-white/30 px-1">{{ $trade }}</span>@endif
                        @if($city)<span class="text-white text-[6px] font-black uppercase border border-white/30 px-1">{{ $city }}</span>@endif
                    </div>
                </div>
            </div>

        @else
            {{-- STANDARD style — photo-first, minimal overlay --}}
            @if ($imageUrl)
                <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-slate-800 flex items-center justify-center">
                    <div class="text-center text-white/20">
                        <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <p class="text-[10px] font-black uppercase tracking-widest">Votre photo</p>
                    </div>
                </div>
            @endif

            {{-- Top brand accent line --}}
            <div class="absolute top-0 inset-x-0 h-1 z-20" style="background: linear-gradient(90deg, {{ $brandColor }}, {{ $brandColor }}44)"></div>

            {{-- Bottom gradient for readability --}}
            <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(to top, rgba(0,0,0,0.82) 0%, rgba(0,0,0,0.15) 55%, transparent 100%)"></div>

            {{-- Glass Card — compact horizontal layout --}}
            <div class="absolute inset-x-0 mx-auto w-[85%] bottom-6 z-20 {{ $exporting ? '' : 'animate-fade-up' }}">
                <div class="backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden" 
                     style="background-color: {{ $accentColor }}cc; border: 1px solid rgba(255,255,255,0.1); border-top: 2px solid {{ $brandColor }}">

                    <div class="px-4 py-3 flex items-center gap-3">
                        {{-- Quote text --}}
                        <div class="flex-1 min-w-0">
                            @if($text)
                                <h3 class="font-black {{ $fontSize }} leading-tight tracking-tight line-clamp-3" style="color: {{ $accentTextColor }}">
                                    {!! nl2br(e($text ?? 'La passion du métier.')) !!}
                                </h3>
                            @endif
                        </div>

                        {{-- Vertical separator --}}
                        <div class="w-px self-stretch bg-white/10 shrink-0"></div>

                        {{-- Branding column --}}
                        <div class="shrink-0 flex flex-col items-center justify-center gap-1.5 max-w-[72px]">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded opacity-80 brightness-0 invert">
                            @else
                                <div class="w-4 h-4 rounded-full shrink-0" style="background-color: {{ $brandColor }}"></div>
                            @endif
                            <p class="font-black tracking-[0.15em] text-[6px] uppercase text-center leading-none line-clamp-2" style="color: {{ $accentTextColor }}bf">{{ $companyLabel }}</p>
                            @php
                                $hex = str_replace('#', '', $brandColor);
                                if(strlen($hex) == 3) {
                                    $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                                    $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                                    $b = hexdec(substr($hex,2,1).substr($hex,2,1));
                                } else {
                                    $r = hexdec(substr($hex,0,2));
                                    $g = hexdec(substr($hex,2,2));
                                    $b = hexdec(substr($hex,4,2));
                                }
                                $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
                                $isDarkBrand = $yiq < 120;
                                if ($isDarkBrand) {
                                    $tagBg = 'rgba(255,255,255,0.1)';
                                    $tagBorder = 'rgba(255,255,255,0.2)';
                                    $tagText = 'rgba(255,255,255,0.9)';
                                } else {
                                    $tagBg = $brandColor . '25';
                                    $tagBorder = $brandColor . '40';
                                    $tagText = $brandColor;
                                }
                                $tagStyle = "background-color: $tagBg; border-color: $tagBorder; color: $tagText;";
                            @endphp
                            @if($trade)
                                <span class="px-2 h-3.5 inline-flex items-center justify-center rounded text-[5px] font-black uppercase tracking-wide border whitespace-nowrap leading-none" style="{{ $tagStyle }}">{{ $trade }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Slide 1: Contact (Standardized) --}}
    @php
        $isSlide1 = (int)$slideIndex === 1;
        $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
        $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    @if($hasContactInfo)
    <div class="absolute inset-0" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
         <x-post-templates.contact-slide
            :brand-color="$brandColor"
            :company-label="$companyLabel"
            :phone="$phone"
            :email="$email"
            :website="$website"
            :trade="$trade"
            :city="$city"
            :visual-style="$visualStyle === 'standard' ? 'standard' : $visualStyle"
            :brand-text-color="$brandTextColor"
            :accent-color="$accentColor"
            :accent-text-color="$accentTextColor"
        />
    </div>
    @endif

    @if($preview && !$exporting)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
