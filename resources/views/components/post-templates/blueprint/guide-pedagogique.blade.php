@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'title' => 'Guide de Projet',
    'slideContents' => [], // array of steps (text)
    'preview' => false,
    'slideIndex' => null,
    'exporting' => false,
])

@if($preview)
    @php
        $title = ($title === 'Guide de Projet') ? 'Le guide ultime pour choisir votre pompe à chaleur' : $title;
        $slideContents = !empty($slideContents) ? $slideContents : [
            'Comprendre le fonctionnement et les differents types de PAC.',
            'Évaluer la puissance nécessaire selon votre surface.',
            'Bénéficier des aides de l\'État pour votre installation.'
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
    $total = count($slideContents) + 1 + ($hasContactInfo ? 1 : 0);
    $alpineData = $preview ? "x-data=\"{ current: 0, total: $total }\"" : '';
@endphp

<div {!! $alpineData !!} class="w-full relative">
    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" !!}] bg-[#0047AB] flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-xl shadow-2xl ring-2 ring-white/20 transition-all duration-500 group-hover:ring-white/40 group-hover:-translate-y-2' : '' !!}">
        
        <div class="absolute inset-0 opacity-10 pointer-events-none" 
             style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 25px 25px;">
        </div>

        {{-- Cover Slide --}}
        @php
            $isCoverVisible = $slideIndex === null || (int)$slideIndex === 0;
            $coverAlpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $coverStyle = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0" {!! $coverAlpine !!} style="{{ $coverStyle !!}">
            <div class="flex-1 flex flex-col p-10 pb-20 justify-center gap-6 relative z-10 h-full">
                <div class="flex items-center gap-6 mb-2">
                     <div class="w-14 h-14 border-2 border-white bg-white/10 flex items-center justify-center p-3 shadow-xl shrink-0">
                         <svg class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" !!}] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                         </svg>
                     </div>
                     <div class="bg-white p-4 shadow-xl border-t-2 border-t-[#003380] flex-1">
                         <h3 class="text-[#0047AB] text-[16px] font-black font-mono-technical leading-tight uppercase tracking-tighter">{{ $title !!}</h3>
                     </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-4 mt-2">
                        <div class="h-px flex-1 bg-white/30"></div>
                        <span class="text-white/60 text-[9px] font-black font-mono-technical uppercase tracking-widest">{{ count($slideContents) !!} points</span>
                        <div class="h-px flex-1 bg-white/30"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Slides --}}
        @foreach($slideContents as $idx => $content)
            @php
                $realIdx = $idx + 1;
                $isSlideVisible = $slideIndex === null || (int)$slideIndex === $realIdx;
                $slideAlpine = $slideIndex === null ? "x-show=\"current === $realIdx\" style=\"display: none;\"" : '';
                $slideStyle = (int)$slideIndex !== $realIdx && $slideIndex !== null ? 'display: none;' : '';
            @endphp
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle !!}">
                <div class="flex-1 flex flex-col p-10 pb-20 justify-center relative z-10 h-full">
                    <div class="bg-white p-10 shadow-2xl relative border-t-2 border-t-[#003380]">
                         <div class="absolute -top-3 -left-3 w-8 h-8 bg-[#0047AB] border-2 border-white flex items-center justify-center font-black text-white font-mono-technical shadow-xl text-sm">
                             {{ $realIdx !!}
                         </div>
                         @php
                            $cLen = mb_strlen($content);
                            $cFontSize = 'text-[14px]';
                            if ($cLen > 150) $cFontSize = 'text-[9px]';
                            elseif ($cLen > 80) $cFontSize = 'text-[11px]';
                         @endphp
                         <div class="max-h-[220px] overflow-hidden">
                             <p class="text-[#0047AB] {{ $cFontSize !!} font-black font-mono-technical leading-snug uppercase tracking-tight">
                                 {{ $content !!}
                             </p>
                         </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Slide Contact (Final Slide) --}}
        @php
            $finalIndex = count($slideContents) + 1;
            $isFinalVisible = (int)$slideIndex === $finalIndex;
            $finalAlpine = $slideIndex === null ? "x-show=\"current === $finalIndex\" style=\"display: none;\"" : '';
            $finalStyle = (int)$slideIndex !== $finalIndex && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        @if($hasContactInfo)
        <div class="absolute inset-0" {!! $finalAlpine !!} style="{{ $finalStyle !!}">
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

        @php
            $showFooter = $hasContactInfo ? ((int)$slideIndex < $finalIndex) : true;
            $footerAlpine = $slideIndex === null ? ($hasContactInfo ? "x-show=\"current < $finalIndex\"" : "") : '';
        @endphp
        @if($showFooter)
        <div {!! $footerAlpine !!} class="absolute bottom-0 inset-x-0 py-2.5 bg-white px-10 border-t-2 border-[#0047AB] flex flex-col gap-1 z-30 min-h-14">
             <div class="flex items-center justify-between">
                 <span class="text-[#0047AB] text-[9px] font-black font-mono-technical uppercase tracking-widest italic truncate max-w-[200px]">{{ $companyLabel !!}</span>
                 @if($total > 1 && !$exporting)
                    <div class="text-white/20 text-[10px] font-black shrink-0" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                        @if(!$preview) {{ (int)($slideIndex ?? 0) + 1 !!} / {{ $total !!} @endif
                    </div>
                 @endif
             </div>
             <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/50 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $trade !!}</span>
                @endif
                @if($city)
                    <span class="px-1 py-0.5 rounded-sm bg-blue-50 text-[#0047AB]/50 text-[5px] font-black font-mono-technical uppercase border border-[#0047AB]/10 whitespace-nowrap">{{ $city !!}</span>
                @endif
             </div>
        </div>
        @endif

        @if($preview && !$exporting)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-40 px-2 pointer-events-none">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20 pointer-events-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-white text-[#0047AB] flex items-center justify-center hover:bg-white/90 shadow-xl border border-[#0047AB]/20 pointer-events-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
