@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'text' => 'La qualité et le choix des matériaux avant tout.',
    'imageUrl' => null,
    'logoUrl' => null,
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'slideIndex' => null,
    'preview' => false,
    'brandTextColor' => '#FFFFFF',
    'accentColor' => '#0F172A',
    'accentTextColor' => '#FFFFFF',
    'exporting' => false,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'La qualité et le choix des matériaux avant tout.') ? 'Le choix des meilleurs matériaux pour une durabilité garantie.' : $text;
        $phone = $phone ?? '01 23 45 67 89';
        $email = $email ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade ?? 'Artisan';
        $city = $city ?? 'Ma Ville';
    @endphp
@endif

@php
    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total = 1 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full aspect-[{{ ($aspectRatio ?? '4:5') === '4:5' ? '4/5' : '1/1' }}] bg-white relative overflow-hidden flex flex-col justify-end {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">

    {{-- Gradient top bar --}}
    <div class="absolute top-0 inset-x-0 h-0.5 z-20" style="background: linear-gradient(90deg, {{ $brandColor }}, {{ $brandColor }}88)"></div>

    {{-- Slide 0 --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div class="absolute inset-0" {!! $slide0Alpine !!} style="{{ $slide0Style }}">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 select-none pointer-events-none">
                <div class="text-center">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Détail Matière</p>
                </div>
            </div>
        @endif

        {{-- Gradient overlay for readability --}}
        <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.2) 50%, transparent 100%)"></div>

        {{-- Branding Overlay (Integrated Glass) --}}
        @php
            $showBranding = $slideIndex === null || (int)$slideIndex === 0;
            $brandingAlpine = $slideIndex === null ? 'x-show="current === 0"' : '';
        @endphp
        @if($showBranding)
        <div {!! $brandingAlpine !!} class="absolute bottom-0 inset-x-0 p-6 z-20">
            <div class="backdrop-blur-xl bg-white/80 rounded-2xl p-4 shadow-2xl border border-white/20 flex flex-col items-center">
                <div class="flex items-center gap-2 mb-2">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-3 w-3 object-contain rounded opacity-80 shrink-0 {{ $accentTextColor === '#FFFFFF' ? 'brightness-0 invert' : '' }}">
                    @else
                        <div class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $brandColor }}"></div>
                    @endif
                    @php
                        $wrappedLabel = mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel;
                    @endphp
                    <p class="font-black tracking-[0.18em] text-[7px] uppercase whitespace-normal leading-tight" style="color: {{ $accentTextColor }}bf">{!! $wrappedLabel !!}</p>
                </div>
                
                <div class="flex items-center gap-2">
                    @if($trade)
                        <span class="px-2 py-0.5 rounded-lg bg-slate-900/5 text-[7px] font-black uppercase tracking-[0.15em] text-slate-900/40 border border-slate-900/5">{{ $trade }}</span>
                    @endif
                    @if($city)
                        <span class="px-2 py-0.5 rounded-lg bg-slate-900/5 text-[7px] font-black uppercase tracking-[0.15em] text-slate-900/40 border border-slate-900/5">{{ $city }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Text overlay (Glass Card) --}}
        <div class="absolute inset-x-6 bottom-[140px] z-10">
            <div class="backdrop-blur-md bg-black/40 p-5 rounded-2xl border border-white/10 shadow-2xl">
                {{-- Label --}}
                <div class="inline-flex items-center gap-2 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $brandColor }}"></span>
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] font-montserrat" style="color: {{ $brandColor }}">Focus Qualité</p>
                </div>
                @php
                    $len = mb_strlen($text);
                    $fontSize = 'text-base';
                    if ($len > 80) $fontSize = 'text-xs';
                    elseif ($len > 40) $fontSize = 'text-sm';
                @endphp
                <div class="max-h-[100px] overflow-hidden">
                    <h3 class="text-white font-black {{ $fontSize }} leading-snug font-montserrat tracking-tight">{!! nl2br(e($text)) !!}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Slide 1: Contact --}}
    @php
        $isSlide1 = (int)$slideIndex === 1;
        $slide1Alpine = $slideIndex === null ? "x-show=\"current === 1\" style=\"display: none;\"" : '';
        $slide1Style = (int)$slideIndex !== 1 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    @if($hasContactInfo)
    <div class="absolute inset-0 bg-white" {!! $slide1Alpine !!} style="{{ $slide1Style }}">
         <x-post-templates.contact-slide
            :brand-color="$brandColor"
            :company-label="$companyLabel"
            :phone="$phone"
            :email="$email"
            :website="$website"
            :trade="$trade"
            :city="$city"
            :brand-text-color="$brandTextColor"
            :accent-color="$accentColor"
            :accent-text-color="$accentTextColor"
        />
    </div>
    @endif

    @if($preview && !$exporting)
        <div class="absolute top-1/2 -translate-y-1/2 inset-x-4 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/40 border border-white/30 transition-all pointer-events-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
