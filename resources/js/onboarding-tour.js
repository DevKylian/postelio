document.addEventListener('alpine:init', () => {
    Alpine.data('onboardingTour', (config) => ({
        active: false,
        stepIndex: 0,
        name: config.name,
        allSteps: config.steps || [],
        steps: [],
        perms: config.perms || {},
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

            if (this.steps.length > 0 && config.autoStart && !localStorage.getItem('postelio_tour_' + this.name)) {
                setTimeout(() => {
                    this.start();
                }, 300);
            }
            
            // Re-position on resize
            window.addEventListener('resize', () => {
                if (this.active) this._positionStep();
            });
        },

        start() {
            if (this.steps.length === 0) return;
            // Removed localStorage check here to allow start() to be called manually if not active
            if (this.active) return;
            
            this.active = true;
            this.stepIndex = 0;
            this._positionStep();
        },

        replay() {
            localStorage.removeItem('postelio_tour_' + this.name);
            this.start();
        },

        _positionStep() {
            const step = this.currentStep;
            if (!step) return;

            const isMobile = window.innerWidth < 1024;
            let targetSelector = step.target;
            if (isMobile && step.mobileTarget) {
                targetSelector = step.mobileTarget;
            }

            if (!targetSelector) {
                this._spotlight = null;
                this._cardStyle = 'top:50%;left:50%;transform:translate(-50%,-50%);opacity:1';
                return;
            }

            const el = document.querySelector(targetSelector);
            
            // Re-check visibility more robustly
            const isVisible = el && (el.offsetWidth > 0 || el.offsetHeight > 0 || el.getClientRects().length > 0);

            if (!isVisible) {
                // If element is not found or hidden, center the card
                this._spotlight = null;
                this._cardStyle = 'top:50%;left:50%;transform:translate(-50%,-50%);opacity:1';
                return;
            }

            el.scrollIntoView({ behavior: 'smooth', block: 'center' });

            const update = () => {
                if (!el) return;
                const rect = el.getBoundingClientRect();
                
                // If element has no size yet, retry later
                if (rect.width === 0 && rect.height === 0) return;

                const style = window.getComputedStyle(el);
                const isSidebar = targetSelector === '#sidebar';
                const pad = isSidebar ? 0 : 8;
                
                // Responsive card sizing
                const cardW = Math.min(320, window.innerWidth - 32);
                const cardH = isMobile ? 240 : 220; // Allow more height on mobile

                this._spotlight = {
                    top:    rect.top    - pad,
                    left:   rect.left   - (isSidebar ? 0 : pad),
                    width:  rect.width  + (isSidebar ? 0 : pad * 2),
                    height: rect.height + (isSidebar ? 0 : pad * 2),
                    radius: isSidebar ? 0 : (parseInt(style.borderRadius) || 12),
                };

                // Horizontal centering relative to target
                let left = rect.left + rect.width / 2 - cardW / 2;
                left = Math.max(16, Math.min(left, window.innerWidth - cardW - 16));

                let pos = step.position || 'bottom';
                
                // Re-calculate position if it overflows
                const spaceBelow = window.innerHeight - (rect.bottom + pad + 20);
                const spaceAbove = rect.top - pad - 20;

                if (pos === 'bottom' && spaceBelow < cardH && spaceAbove > spaceBelow) pos = 'top';
                if (pos === 'top' && spaceAbove < cardH && spaceBelow > spaceAbove) pos = 'bottom';
                if (isMobile && (pos === 'right' || pos === 'left')) pos = 'bottom'; 

                if (pos === 'top') {
                    let top = rect.top - pad - 14 - cardH;
                    if (top < 16) top = 16;
                    this._cardStyle = `top:${top}px;left:${left}px;transform:none;opacity:1;width:${cardW}px`;
                } else if (pos === 'right' && !isMobile) {
                    let top = Math.max(16, Math.min(rect.top + rect.height/2 - 100, window.innerHeight - cardH - 16));
                    const leftPos = rect.right + pad + 14;
                    this._cardStyle = `top:${top}px;left:${leftPos}px;transform:none;opacity:1;width:${cardW}px`;
                } else {
                    let top = rect.bottom + pad + 14;
                    if (top + cardH > window.innerHeight - 16) top = window.innerHeight - cardH - 16;
                    this._cardStyle = `top:${top}px;left:${left}px;transform:none;opacity:1;width:${cardW}px`;
                }
            };

            // Tracking loop to keep spotlight attached during scroll/animation
            let startTime = Date.now();
            const track = () => {
                if (this.currentStep !== step) return; // Stop if step changed
                update();
                if (Date.now() - startTime < 2000) { // Track for 2 seconds
                    requestAnimationFrame(track);
                }
            };
            requestAnimationFrame(track);
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
            window.dispatchEvent(new CustomEvent('tour:finished', { detail: { name: this.name } }));
        },

        skip() {
            this.finish();
        }
    }));
});
