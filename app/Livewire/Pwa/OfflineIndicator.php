<?php

namespace App\Livewire\Pwa;

use Livewire\Component;

class OfflineIndicator extends Component
{
    public function render()
    {
        return <<<'HTML'
        <div x-data="{ 
            isOffline: !navigator.onLine,
            showToast: false,
            pendingCount: 0,
            init() {
                window.addEventListener('online', () => { 
                    this.isOffline = false;
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 5000);
                });
                window.addEventListener('offline', () => { 
                    this.isOffline = true;
                    this.showToast = true;
                });
            }
        }">
            <!-- Offline Banner -->
            <template x-if="isOffline">
                <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-[100] w-[95%] max-w-lg">
                    <div class="bg-slate-900/90 backdrop-blur-md border border-white/10 rounded-2xl p-4 shadow-2xl flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center flex-shrink-0 animate-pulse">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-bold text-sm">Mode Hors-ligne</p>
                            <p class="text-slate-400 text-xs">Vos créations seront envoyées automatiquement dès le retour d'Internet.</p>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Back Online Toast -->
            <div x-show="showToast && !isOffline" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-10"
                 class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] w-[95%] max-w-sm">
                <div class="bg-green-500 text-white rounded-2xl p-4 shadow-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="font-bold text-sm">Connexion rétablie !</p>
                </div>
            </div>
        </div>
        HTML;
    }
}
