<div x-data="{
    open: false,
    color: @entangle($attributes->wire('model')),
    init() {
        if (!this.color) this.color = '#F59E0B';
    }
}" class="relative">
    <!-- Trigger Button (Reverted to original professional style) -->
    <div @click="open = !open" 
         class="flex items-center gap-3 p-1 pr-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-slate-400 transition-all shadow-sm group h-9">
        <div class="w-7 h-7 rounded-lg shadow-inner ring-1 ring-black/5 shrink-0" 
             :style="`background-color: ${color}`"></div>
        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest font-mono-technical" x-text="color"></span>
        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
    </div>

    <!-- Color Picker Modal (Teleported to Body to avoid clipping) -->
    <template x-teleport="body">
        <div x-show="open" 
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-0"
             style="display: none;">
            
            <!-- Backdrop -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-400"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false" 
                 class="absolute inset-0 bg-slate-900/50 backdrop-blur-md"></div>

            <!-- Modal Content -->
            <div x-show="open"
                 x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-12 sm:translate-y-0"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 @click.away="open = false"
                 x-data="spectrumColorPicker(color)"
                 x-init="$watch('internalColor', v => color = v); $watch('color', v => internalColor = v)"
                 class="relative bg-white rounded-[2.5rem] shadow-2xl border border-white/20 w-full max-w-[320px] overflow-hidden">
                
                <!-- Premium Header -->
                <div class="px-8 pt-8 pb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Couleur</h3>
                        <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider mt-0.5">Spectre complet</p>
                    </div>
                    <button @click="open = false" class="p-2.5 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-all text-slate-400 hover:text-slate-600 group">
                        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-8 pt-2 space-y-8">
                    <!-- Spectrum Picker Container -->
                    <div class="flex justify-center py-4 bg-slate-50/50 rounded-[2rem] border border-slate-100/50">
                        <div x-init="initPicker($el)" x-ref="picker" class="spectrum-picker" wire:ignore></div>
                    </div>

                    <!-- Hex & Action -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-[1.5rem] border border-slate-100/50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">Code Hex</p>
                            <div class="flex items-center gap-1.5">
                                <div class="w-3 h-3 rounded-full shadow-sm" :style="`background-color: ${internalColor}`"></div>
                                <span class="text-xs font-bold text-slate-700 font-mono tracking-wider uppercase" x-text="internalColor"></span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button @click="open = false" 
                                class="w-full py-4 bg-midnight text-white text-xs font-black rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-midnight/20 uppercase tracking-[0.2em] relative overflow-hidden group">
                            <span class="relative z-10">Valider la couleur</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-copper/0 via-copper/10 to-copper/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
