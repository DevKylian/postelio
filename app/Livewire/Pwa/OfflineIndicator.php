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
            async updateCount() {
                try {
                    const { getQueueCount } = await import('/js/pwa/offline-queue.js');
                    this.pendingCount = await getQueueCount();
                } catch (e) {
                    console.error('Failed to update offline count', e);
                }
            },
            init() {
                this.updateCount();
                window.addEventListener('online', () => { 
                    this.isOffline = false;
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 5000);
                    this.updateCount();
                });
                window.addEventListener('offline', () => { 
                    this.isOffline = true;
                    this.showToast = true;
                    this.updateCount();
                });
                window.addEventListener('pwa:queued', () => {
                    this.updateCount();
                });
            }
        }">
            <!-- Offline Banner (Long version) -->
            <template x-if="isOffline">
                <div class="fixed bottom-0 left-0 right-0 z-[100] animate-slide-up">
                    <div class="bg-slate-900 border-t border-white/10 px-4 py-3 flex items-center justify-between shadow-[0_-8px_30px_rgb(0,0,0,0.12)]">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3"/>
                                </svg>
                            </div>
                            <div class="min-w-0 text-left">
                                <p class="text-white font-bold text-sm leading-tight flex items-center gap-2">
                                    Mode Hors-ligne
                                    <template x-if="pendingCount > 0">
                                        <span class="bg-amber-500 text-slate-900 text-[10px] font-black px-1.5 py-0.5 rounded-full" x-text="pendingCount + ' en attente'"></span>
                                    </template>
                                </p>
                                <p class="text-slate-400 text-[11px] mt-0.5 hidden sm:block">Vos posts sont mis en file d'attente locale.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href=\"/dashboard\" wire:navigate
                               class="bg-white/10 hover:bg-white/20 text-white text-[11px] font-bold px-4 py-2 rounded-lg transition-colors border border-white/5">
                                Naviguer
                            </a>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Back Online Toast (Long version) -->
            <div x-show="showToast && !isOffline" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full"
                 class="fixed bottom-0 left-0 right-0 z-[100]">
                <div class="bg-green-600 text-white px-4 py-3 flex items-center justify-between shadow-2xl">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="font-bold text-sm">Connexion rétablie !</p>
                    </div>
                    <template x-if="pendingCount > 0">
                        <button @click=\"window.location.reload()\"
                                class="bg-white text-green-700 text-[11px] font-bold px-4 py-2 rounded-lg transition-colors shadow-sm">
                            Synchroniser les posts (<span x-text=\"pendingCount\"></span>)
                        </button>
                    </template>
                </div>
            </div>
        </div>
        HTML;
    }
}
