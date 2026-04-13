@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'imageUrl' => null,
    'text' => 'SUR LE TERRAIN',
    'preview' => false,
    'slideIndex' => null,
])

@if($preview)
    @php
        $imageUrl = $imageUrl ?? 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800';
        $text = ($text === 'SUR LE TERRAIN') ? 'La passion du métier se lit dans chaque geste.' : $text;
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
    $displayText = strtoupper($text ?: 'SUR LE TERRAIN');
    $len = mb_strlen($displayText);
@endphp

{{-- Square container — all positions in % so it scales to any parent size --}}
<div {!! $alpineData !!} style="width: 100%; aspect-ratio: 1/1; background: #0047AB; position: relative; overflow: hidden; font-size: 0;" class="{{ $preview ? 'rounded-xl shadow-2xl group transition-all duration-group hover:-translate-y-2' : '' !!}">

    {{-- Grid background --}}
    <div style="position: absolute; inset: 0; opacity: 0.1; pointer-events: none; background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 8.3% 8.3%;"></div>

    {{-- Slide 0: Content --}}
    @php
        $isSlide0 = (int)$slideIndex === 0 || $slideIndex === null;
        $slide0Alpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
        $slide0Style = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
    @endphp
    <div style="position: absolute; inset: 0; {{ $slide0Style !!}" {!! $slide0Alpine !!}>
        {{-- Photo frame: top 6%, sides 6%, height 62% --}}
        <div style="position: absolute; top: 6%; left: 6%; right: 6%; height: 62%; background: white; padding: 1.4%; overflow: hidden;">
            @if($imageUrl)
                <img src="{{ $imageUrl !!}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @else
                <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=600" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @endif
        </div>

        {{-- Text label zone: photo ends at 68%, footer starts at ~86.5% → text in between --}}
        <div style="position: absolute; top: 72%; left: 6%; right: 6%; height: 14%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <div style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.3); padding: 2% 5%; max-width: 100%; overflow: hidden; white-space: nowrap;">
                <span class="{{ $len > 25 ? 'text-[10px]' : ($len > 18 ? 'text-[11px]' : 'text-[12px]') !!}" style="color: white; font-weight: 900; text-transform: uppercase; letter-spacing: 0.15em; line-height: 1.3; text-align: center; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Courier New', monospace;">{{ $displayText !!}</span>
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
    <div style="position: absolute; inset: 0; padding: 6%; {{ $slide1Style !!}" {!! $slide1Alpine !!}>
        <x-post-templates.contact-slide 
            :brand-color="$brandColor" 
            :company-label="$companyLabel"
            :phone="$phone"
            :email="$email"
            :website="$website"
            :trade="$trade"
            :city="$city"
            visualStyle="blueprint"
        />
    </div>
    @endif

    {{-- Footer: bottom 0, height ~13.5% --}}
    @php
        $showFooter = $hasContactInfo ? ((int)$slideIndex === 0) : true;
        $footerAlpine = $slideIndex === null ? ($hasContactInfo ? 'x-show="current === 0"' : '') : '';
    @endphp
    @if($showFooter)
    <div {!! $footerAlpine !!} style="position: absolute; left: 0; right: 0; bottom: 0; height: 16%; background: white; border-top: 2px solid #0047AB; padding: 0 7%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; overflow: hidden; z-index: 20;">
        <div style="width: 100%; display: flex; align-items: center; justify-content: space-between;">
            <span class="text-[9px]" style="color: #0047AB; font-weight: 900; text-transform: uppercase; letter-spacing: 0.2em; font-family: 'Courier New', monospace; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 80%;">{{ $companyLabel !!}</span>
            <div style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $brandColor !!}; flex-shrink: 0;"></div>
        </div>
        <div style="width: 100%; display: flex; align-items: center; gap: 0.4rem; overflow: hidden;">
            @if($trade)
                <span style="padding: 0.1rem 0.3rem; border-radius: 2px; background: rgba(0,71,171,0.05); color: rgba(0,71,171,0.6); font-size: 5px; font-weight: 900; text-transform: uppercase; border: 1px solid rgba(0,71,171,0.1); white-space: nowrap; font-family: 'Courier New', monospace;">{{ $trade !!}</span>
            @endif
            @if($city)
                <span style="padding: 0.1rem 0.3rem; border-radius: 2px; background: rgba(0,71,171,0.05); color: rgba(0,71,171,0.6); font-size: 5px; font-weight: 900; text-transform: uppercase; border: 1px solid rgba(0,71,171,0.1); white-space: nowrap; font-family: 'Courier New', monospace;">{{ $city !!}</span>
            @endif
        </div>
    </div>
    @endif

    @if($preview)
        <div class="absolute inset-y-0 inset-x-2 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity z-30 pointer-events-none">
            <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20 pointer-events-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20 pointer-events-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    @endif
</div>
