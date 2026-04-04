@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'title' => 'Détails du chantier',
    'slides' => [],
    'slideContents' => [],
    'logoUrl' => null,
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'slideIndex' => null,
    'preview' => false,
    'brandTextColor' => '#FFFFFF',
])

@if($preview)
    @php
        $title = 'Focus sur l\'installation du système de chauffage';
        $slides = [
            'https://images.unsplash.com/photo-1585314062340-f1a5a7c9328d?q=80&w=800',
            'https://images.unsplash.com/photo-1590644365607-1c5a519a7a37?q=80&w=800',
            'https://images.unsplash.com/photo-1504307651254-35680f356dfd?q=80&w=800'
        ];
        $slideContents = [
            'Préparation technique et traçage des réseaux.',
            'Installation des collecteurs et mise en pression.',
            'Finalisation des raccordements et test de chauffe.'
        ];
        $phone = $phone ?? '01 23 45 67 89';
        $email = $email ?? 'contact@votre-artisan.fr';
        $website = $website ?? 'www.votre-artisan.fr';
        $trade = $trade ?? 'Artisan';
        $city = $city ?? 'Ma Ville';
    @endphp
@endif

@php
    $hasContactInfo = !empty($phone) || !empty($email) || !empty($website);
    $total = count($slides) + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-800 relative overflow-hidden {{ $preview ? 'rounded-2xl shadow-lg ring-1 ring-gray-200 transition-all duration-300 group-hover:shadow-2xl group-hover:-translate-y-2' : '' }}">
        
        @foreach($slides as $index => $imageUrl)
            @php
                $isSlideVisible = $slideIndex === null || $slideIndex === $index;
                $slideAlpine = $slideIndex === null 
                    ? "x-show=\"current === $index\"" . ($index > 0 ? ' style="display: none;"' : '')
                    : '';
                $slideStyle = !$isSlideVisible ? 'display: none;' : '';
                $currentText = ($index === 0) ? ($slideContents[0] ?? $title) : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                @if ($imageUrl)
                    <img src="{{ $imageUrl }}" class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] object-cover">
                @else
                    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] flex items-center justify-center {{ $index % 2 === 0 ? 'bg-gray-600' : 'bg-gray-500' }}">
                         <div class="text-center">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <p class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em]">Photo {{ $index + 1 }}</p>
                         </div>
                    </div>
                @endif

                @php
                    $slideText = $slideContents[$index] ?? (($index === 0) ? $title : '');
                @endphp

                @if(!empty($slideText) && !$imageUrl)
                    @php
                        $sLen = mb_strlen($slideText);
                        $sFontSize = 'text-[11px]';
                        if ($sLen > 120) $sFontSize = 'text-[7px]';
                        elseif ($sLen > 60) $sFontSize = 'text-[9px]';
                    @endphp
                     <div class="absolute inset-x-4 bottom-20 bg-black/70 backdrop-blur-sm p-4 rounded-xl border border-white/10 shadow-xl">
                        <div class="max-h-[60px] overflow-hidden">
                             <p class="text-white font-black {{ $sFontSize }} leading-snug font-montserrat text-center">{{ $slideText }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Slide Index (Contact) --}}
        @php
            $contactIdx = count($slides);
            $isContactVisible = $slideIndex === null || (int)$slideIndex === $contactIdx;
            $contactAlpine = $slideIndex === null ? "x-show=\"current === $contactIdx\" style=\"display: none;\"" : '';
            $contactStyle = (int)$slideIndex !== $contactIdx && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-0 bg-white" {!! $contactAlpine !!} style="{{ $contactStyle }}">
             <x-post-templates.contact-slide 
                :brand-color="$brandColor" 
                :company-label="$companyLabel"
                :phone="$phone"
                :email="$email"
                :website="$website"
                :trade="$trade"
                :city="$city"
                :brand-text-color="$brandTextColor"
            />
        </div>
        @endif

        @php
            $showBrandBar = $slideIndex === null || (int)$slideIndex < $contactIdx;
            $brandBarAlpine = $slideIndex === null ? "x-show=\"current < $contactIdx\"" : '';
        @endphp
        <div class="absolute inset-x-0 bottom-0 py-2 px-6 flex flex-col items-center justify-center bg-black/85" {!! $brandBarAlpine !!} style="{{ !$showBrandBar ? 'display:none;' : '' }}">
            <p class="text-white font-bold tracking-widest text-[9px] font-montserrat mb-1">{{ $companyLabel }}</p>
            <div class="flex items-center gap-2 mt-1">
                @php
                    $tagTextCol = $brandColor;
                    $hexTag = str_replace('#', '', $brandColor);
                    if (strlen($hexTag) == 3) $hexTag = $hexTag[0].$hexTag[0].$hexTag[1].$hexTag[1].$hexTag[2].$hexTag[2];
                    $rTag = hexdec(substr($hexTag, 0, 2));
                    $gTag = hexdec(substr($hexTag, 2, 2));
                    $bTag = hexdec(substr($hexTag, 4, 2));
                    $yiqTag = (($rTag * 299) + ($gTag * 587) + ($bTag * 114)) / 1000;
                    if ($yiqTag > 235) $tagTextCol = '#1E293B'; 
                @endphp
                
                @if($trade)
                    <span class="px-2 py-0.5 rounded-full bg-white text-[7px] font-black uppercase tracking-[0.15em] shadow-sm ring-1 ring-black/5" style="color: {{ $tagTextCol }}">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-2 py-0.5 rounded-full bg-white text-[7px] font-black uppercase tracking-[0.15em] shadow-sm ring-1 ring-black/5" style="color: {{ $tagTextCol }}">{{ $city }}</span>
                @endif
            </div>
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="h-4 w-4 object-contain rounded absolute right-6">
            @endif
        </div>

        @if($preview)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg" x-text="(current + 1) + ' / ' + total"></div>
        @else
            @if($total > 1)
                <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-lg">
                    {{ ($slideIndex ?? 0) + 1 }} / {{ $total }}
                </div>
            @endif
        @endif
    </div>
</div>
