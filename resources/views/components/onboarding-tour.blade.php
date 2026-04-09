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
     x-show="active"
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

@once
@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .tour-overlay {
        position: fixed;
        inset: 0;
        z-index: 9997;
        background: rgba(15, 23, 42, 0.82);
        backdrop-filter: blur(8px);
        transition: clip-path 0.4s cubic-bezier(0.16,1,0.3,1);
        pointer-events: auto;
    }

    .tour-spotlight-ring {
        position: fixed;
        z-index: 9998;
        pointer-events: auto;
        background: transparent;
        border: 1.5px solid rgba(184, 115, 51, 0.4);
        box-shadow: 0 0 15px rgba(184, 115, 51, 0.2);
        transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
    }

    .tour-card {
        position: fixed;
        z-index: 10000;
        width: 320px;
        max-width: calc(100vw - 32px);
        background: #ffffff;
        border-radius: 24px;
        padding: 24px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 32px 64px -12px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.02);
        transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16,1,0.3,1);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('onboardingTour', (config) => ({
        active: false,
        stepIndex: 0,
        name: config.name,
        allSteps: config.steps,
        steps: [],
        perms: config.perms,
        _spotlight: null,
        _cardStyle: 'top:50%;left:50%;transform:translate(-50%,-50%);opacity:0',

        get currentStep() {
            return this.steps[this.stepIndex] ?? {};
        },
        get isFirst() { return this.stepIndex === 0; },
        get isLast()  { return this.stepIndex === this.steps.length - 1; },
        get hasTarget() { return !!this.currentStep.target && !!this._spotlight; },
        get spotlightStyle() {
            const s = this._spotlight;
            if (!s) return 'display:none';
            return `top:${s.top}px;left:${s.left}px;width:${s.width}px;height:${s.height}px;border-radius:${s.radius}px`;
        },
        get cardStyle() {
            return this._cardStyle;
        },
        get overlayClipPath() {
            const s = this._spotlight;
            if (!s || !this.hasTarget) return 'polygon(0% 0%, 0% 100%, 100% 100%, 100% 0%)';
            
            const L = s.left, T = s.top, W = s.width, H = s.height;
            const r = Math.min(s.radius, W/2, H/2);
            
            const getArc = (cx, cy, startDeg, endDeg) => {
                const pts = [];
                for(let i=0; i<=4; i++) {
                    const rad = (startDeg + (endDeg - startDeg) * (i/4)) * Math.PI / 180;
                    pts.push(`${cx + Math.cos(rad)*r}px ${cy + Math.sin(rad)*r}px`);
                }
                return pts;
            };

            const c1 = getArc(L + r, T + r, 180, 270);
            const c2 = getArc(L + W - r, T + r, 270, 360);
            const c3 = getArc(L + W - r, T + H - r, 0, 90);
            const c4 = getArc(L + r, T + H - r, 90, 180);
            
            const holePoints = [...c1, ...c2, ...c3, ...c4].join(', ');
            return `polygon(0% 0%, 0% 100%, ${L}px 100%, ${holePoints}, ${L}px 100%, 100% 100%, 100% 0%)`;
        },

        init() {
            this.steps = this.allSteps.filter(s => !s.perm || this.perms[s.perm]);

            if (config.autoStart && !localStorage.getItem('postelio_tour_' + this.name)) {
                setTimeout(() => {
                    this.start();
                }, 1000);
            }
            
            // Re-position on resize
            window.addEventListener('resize', () => {
                if (this.active) this._positionStep();
            });
        },

        start() {
            if (this.steps.length === 0) return;
            if (localStorage.getItem('postelio_tour_' + this.name)) return;
            
            this.active = true;
            this.stepIndex = 0;
            this._positionStep();
        },

        _positionStep() {
            const step = this.currentStep;
            if (!step) return;

            if (!step.target) {
                this._spotlight = null;
                this._cardStyle = 'top:50%;left:50%;transform:translate(-50%,-50%);opacity:1';
                return;
            }

            const el = document.querySelector(step.target);
            if (!el) {
                // If element is not found, we might be on a multi-step form where it's not yet visible.
                // For now, center the card.
                this._spotlight = null;
                this._cardStyle = 'top:50%;left:50%;transform:translate(-50%,-50%);opacity:1';
                return;
            }

            el.scrollIntoView({ behavior: 'smooth', block: 'center' });

            setTimeout(() => {
                const rect = el.getBoundingClientRect();
                const style = window.getComputedStyle(el);
                const isSidebar = step.target === '#sidebar';
                const pad = isSidebar ? 0 : 8;
                const cardW = 320;
                const cardH = 200;

                this._spotlight = {
                    top:    rect.top    - pad,
                    left:   rect.left   - (isSidebar ? 0 : pad),
                    width:  rect.width  + (isSidebar ? 0 : pad * 2),
                    height: rect.height + (isSidebar ? 0 : pad * 2),
                    radius: isSidebar ? 0 : (parseInt(style.borderRadius) || 12),
                };

                let left = rect.left + rect.width / 2 - cardW / 2;
                left = Math.max(16, Math.min(left, window.innerWidth - cardW - 16));

                let pos = step.position || 'bottom';
                if (pos === 'bottom' && rect.bottom + pad + 20 + cardH > window.innerHeight) pos = 'top';
                if (pos === 'top' && rect.top - pad - 20 - cardH < 0) pos = 'bottom';

                if (pos === 'top') {
                    // Place above the element, but not above the screen
                    let top = rect.top - pad - 14 - cardH;
                    if (top < 16) top = 16;
                    this._cardStyle = `top:${top}px;left:${left}px;transform:none;opacity:1`;
                } else if (pos === 'right') {
                    let top = Math.max(16, Math.min(rect.top + rect.height/2 - 100, window.innerHeight - cardH - 16));
                    const leftPos = rect.right + pad + 14;
                    this._cardStyle = `top:${top}px;left:${leftPos}px;transform:none;opacity:1`;
                } else {
                    // Bottom
                    let top = rect.bottom + pad + 14;
                    if (top + cardH > window.innerHeight - 16) top = window.innerHeight - cardH - 16;
                    this._cardStyle = `top:${top}px;left:${left}px;transform:none;opacity:1`;
                }
            }, 400);
        },

        next() {
            if (this.isLast) { this.finish(); return; }
            this.stepIndex++;
            this._positionStep();
        },

        prev() {
            if (this.stepIndex > 0) {
                this.stepIndex--;
                this._positionStep();
            }
        },

        finish() {
            this.active = false;
            localStorage.setItem('postelio_tour_' + this.name, 'done');
        },

        skip() {
            this.finish();
        }
    }));
});
</script>
@endpush
@endonce
