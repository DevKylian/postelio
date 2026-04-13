@props([
    'brandColor' => '#F59E0B',
    'companyLabel' => 'Votre Société',
    'phone' => null,
    'email' => null,
    'website' => null,
    'trade' => null,
    'city' => null,
    'title' => 'Guide Pratique',
    'slideContents' => [], // array of steps (text)
    'preview' => false,
    'slideIndex' => null,
    'exporting' => false,
])

@if($preview)
    @php
        $title = ($title === 'Guide Pratique') ? 'Le guide ultime pour choisir votre pompe à chaleur' : $title;
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
    <div class="w-full aspect-[{{ ($aspectRatio ?? "4:5") === "4:5" ? "4/5" : "1/1" }}] bg-gray-900 flex flex-col relative overflow-hidden group {{ $preview ? 'rounded-3xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 group-hover:shadow-copper/30 group-hover:-translate-y-2' : '' }}">
        
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient({{ $brandColor }} 2px, transparent 2px); background-size: 40px 40px;"></div>
        </div>

        {{-- Cover Slide --}}
        @php
            $isCoverVisible = $slideIndex === null || (int)$slideIndex === 0;
            $coverAlpine = $slideIndex === null ? "x-show=\"current === 0\"" : '';
            $coverStyle = (int)$slideIndex !== 0 && $slideIndex !== null ? 'display: none;' : '';
        @endphp
        <div class="absolute inset-0" {!! $coverAlpine !!} style="{{ $coverStyle }}">
            <div class="flex-1 flex flex-col p-8 pb-20 justify-center gap-4 relative z-10 h-full">
                <div class="flex items-center gap-4 mb-2">
                     <div class="w-12 h-12 rounded-2xl backdrop-blur-2xl bg-white/10 border border-white/20 flex items-center justify-center shadow-2xl shrink-0">
                         <svg class="w-6 h-6" style="color: {{ $brandColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                         </svg>
                     </div>
                     <div>
                         <span class="text-white/40 text-[9px] font-black uppercase tracking-[0.2em] block font-montserrat">Guide d'Expert</span>
                         <h3 class="text-white text-lg font-bold leading-tight uppercase tracking-tight">{{ $title }}</h3>
                     </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-3 mt-2">
                        <div class="h-px flex-1 bg-white/10"></div>
                        <span class="text-white/30 text-[9px] font-bold uppercase tracking-widest">{{ count($slideContents) }} conseils</span>
                        <div class="h-px flex-1 bg-white/10"></div>
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
            <div class="absolute inset-0" {!! $slideAlpine !!} style="{{ $slideStyle }}">
                <div class="flex-1 flex flex-col p-10 pb-20 justify-center relative z-10 h-full">
                    <div class="backdrop-blur-3xl bg-white/5 border border-white/10 p-8 rounded-[32px] shadow-2xl relative">
                        <div class="absolute -top-5 -left-5 w-14 h-14 rounded-2xl backdrop-blur-2xl bg-white/10 flex items-center justify-center font-black text-white text-xl shadow-2xl border border-white/20" style="background-color: {{ $brandColor }}">
                            {{ $realIdx }}
                        </div>
                        @php
                            $cLen = mb_strlen($content);
                            $cFontSize = 'text-lg';
                            if ($cLen > 150) $cFontSize = 'text-xs';
                            elseif ($cLen > 80) $cFontSize = 'text-sm';
                            elseif ($cLen > 40) $cFontSize = 'text-base';
                        @endphp
                        <div class="max-h-[180px] overflow-hidden">
                            <p class="text-white {{ $cFontSize }} font-bold leading-tight uppercase pt-2 tracking-tight">
                                {{ $content }}
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
        <div class="absolute inset-0" {!! $finalAlpine !!} style="{{ $finalStyle }}">
            <x-post-templates.contact-slide 
                :brand-color="$brandColor" 
                :company-label="$companyLabel"
                :phone="$phone"
                :email="$email"
                :website="$website"
                :trade="$trade"
                :city="$city"
                visualStyle="glass"
            />
        </div>
        @endif

        @php
            $showFooter = $hasContactInfo ? ((int)$slideIndex < $finalIndex) : true;
            $footerAlpine = $slideIndex === null ? ($hasContactInfo ? "x-show=\"current < $finalIndex\"" : "") : '';
        @endphp
        @if($showFooter)
        <div {!! $footerAlpine !!} class="absolute inset-x-0 bottom-0 py-3 px-10 backdrop-blur-md bg-white/5 flex flex-col gap-1 border-t border-white/10 z-30">
             <div class="flex items-center justify-between">
                 <span class="text-white/50 text-[9px] font-black uppercase tracking-[0.3em] font-montserrat truncate max-w-[200px]">{!! mb_strlen($companyLabel) > 15 ? wordwrap($companyLabel, 15, "<br>", true) : $companyLabel !!}</span>
                 @if($total > 1 && !$exporting)
                    <div class="text-white/20 text-[10px] font-black" @if($preview) x-text="(current + 1) + ' / ' + total" @endif>
                        @if(!$preview) {{ ($slideIndex ?? 0) + 1 }} / {{ $total }} @endif
                    </div>
                 @endif
             </div>
             <div class="flex items-center gap-1.5 overflow-hidden">
                @if($trade)
                    <span class="px-1.5 py-0.5 rounded-md bg-white/5 text-white/30 text-[6px] font-black uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $trade }}</span>
                @endif
                @if($city)
                    <span class="px-1.5 py-0.5 rounded-md bg-white/5 text-white/30 text-[6px] font-black uppercase tracking-wider border border-white/5 whitespace-nowrap">{{ $city }}</span>
                @endif
             </div>
        </div>
        @endif

        @if($preview && !$exporting)
            <div class="absolute top-1/2 -translate-y-1/2 inset-x-2 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity z-40">
                <button @click="current = (current - 1 + total) % total" class="w-8 h-8 rounded-full bg-slate-900/50 text-white flex items-center justify-center hover:bg-slate-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="current = (current + 1) % total" class="w-8 h-8 rounded-full bg-slate-900/50 text-white flex items-center justify-center hover:bg-slate-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        @endif
    </div>
</div>
