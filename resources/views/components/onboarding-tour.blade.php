@props([
    'name' => 'genericTour',
    'steps' => [],
    'perms' => [],
    'autoStart' => true
])

<div x-data="onboardingTour({ 
        name: '{{ $name }}', 
        steps: @js($steps), 
        perms: @js($perms),
        autoStart: @js($autoStart)
     })"
     @if(isset($attributes['wire:key'])) wire:key="{{ $attributes['wire:key'] }}" @endif
     x-on:replay-tour.window="if($event.detail.name === name) replay()"
     x-show="typeof active !== 'undefined' && active && steps.length > 0"
     x-cloak
     class="relative z-[9999]">

    {{-- Click-to-skip overlay (behind everything) --}}
    <div class="tour-overlay" 
         @click="skip()"
         :style="{ 'clip-path': overlayClipPath }"></div>

    {{-- Spotlight highlight (border only, subtle) --}}
    <div x-show="hasTarget" class="tour-spotlight-ring" :style="spotlightStyle"></div>

    {{-- Tooltip card --}}
    <div class="tour-card" :style="cardStyle">

        {{-- Close --}}
        <button @click="skip()"
                class="absolute top-3 right-3 w-6 h-6 flex items-center justify-center rounded-full text-slate-300 hover:text-slate-500 hover:bg-slate-100 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Progress dots --}}
        <div class="flex items-center gap-1.5 mb-5">
            <template x-for="(s, i) in steps" :key="i">
                <div class="h-1.5 rounded-full transition-all duration-300"
                     :class="i === stepIndex ? 'w-5 bg-copper' : (i < stepIndex ? 'w-1.5 bg-copper/30' : 'w-1.5 bg-slate-200')">
                </div>
            </template>
        </div>

        {{-- Icon + content --}}
        <div class="flex items-start gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-50 to-orange-100 flex items-center justify-center text-lg shrink-0 shadow-sm">
                <span x-text="currentStep.icon"></span>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="font-bold text-midnight text-sm leading-snug mb-1.5" x-text="currentStep.title"></p>
                <p class="text-xs text-slate-500 leading-relaxed" x-text="currentStep.desc"></p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button @click="prev()"
                    x-show="!isFirst"
                    class="text-xs font-semibold text-slate-400 hover:text-slate-600 transition-colors flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Précédent
            </button>
            <span x-show="isFirst" class="text-[11px] text-slate-400 font-medium">Visite rapide</span>
            <button @click="next()"
                    class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-gradient-to-r from-amber-500 to-copper text-white text-xs font-bold rounded-lg shadow-sm hover:shadow-md hover:brightness-105 transition-all"
                    x-text="isLast ? 'C\'est parti ! 🚀' : 'Suivant →'">
            </button>
        </div>

    </div>
</div>
