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
            showOnlineToast: false,
            isSyncing: false,
            pendingCount: 0,

            async updateCount() {
                if (window.PostelioQueue) {
                    try {
                        this.pendingCount = await window.PostelioQueue.getQueueCount();
                    } catch (e) {
                        console.warn('[PWA] Could not read queue count', e);
                    }
                }
            },

            async syncNow() {
                if (this.isSyncing || !window.PostelioSync) return;
                this.isSyncing = true;
                try {
                    await window.PostelioSync.processQueue();
                } finally {
                    this.isSyncing = false;
                    await this.updateCount();
                }
            },

            init() {
                this.updateCount();

                window.addEventListener('online', async () => {
                    this.isOffline = false;
                    await this.updateCount();
                    if (this.pendingCount > 0) {
                        this.showOnlineToast = true;
                        // Auto-sync after a brief delay
                        setTimeout(() => this.syncNow(), 1000);
                        setTimeout(() => { this.showOnlineToast = false; }, 7000);
                    } else {
                        this.showOnlineToast = true;
                        setTimeout(() => { this.showOnlineToast = false; }, 4000);
                    }
                });

                window.addEventListener('offline', () => {
                    this.isOffline = true;
                    this.showOnlineToast = false;
                    this.updateCount();
                });

                window.addEventListener('pwa:queued', async () => {
                    await this.updateCount();
                });

                window.addEventListener('pwa:sync-complete', async (e) => {
                    await this.updateCount();
                    if (e.detail.synced > 0) {
                        // Redirect to posts list so the user sees their generated posts
                        setTimeout(() => { window.location.href = '/posts'; }, 1200);
                    }
                });
            }
        }">
            <!-- ── Offline Banner ─────────────────────────────────────────────── -->
            <template x-if="isOffline">
                <div class="fixed bottom-0 left-0 right-0 z-[100]"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-slate-900 border-t border-white/10 px-4 py-3 flex items-center justify-between gap-3 shadow-[0_-8px_30px_rgb(0,0,0,0.18)]">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-white font-bold text-sm leading-tight flex items-center gap-2 flex-wrap">
                                    Mode Hors-ligne
                                    <template x-if="pendingCount > 0">
                                        <span class="bg-amber-500 text-slate-900 text-[10px] font-black px-2 py-0.5 rounded-full" x-text="pendingCount + ' post(s) en attente'"></span>
                                    </template>
                                </p>
                                <p class="text-slate-400 text-[11px] mt-0.5 hidden sm:block">Remplissez le formulaire — vos posts seront générés dès le retour en ligne.</p>
                            </div>
                        </div>
                        <a href="/generate-post"
                           class="shrink-0 bg-amber-500 hover:bg-amber-400 text-slate-900 text-[11px] font-black px-3 py-2 rounded-lg transition-colors">
                            Créer
                        </a>
                    </div>
                </div>
            </template>

            <!-- ── Back-Online Toast ──────────────────────────────────────────── -->
            <div x-show="showOnlineToast && !isOffline"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="fixed bottom-0 left-0 right-0 z-[100]">
                <div class="px-4 py-3 flex items-center justify-between gap-3 shadow-2xl"
                     :class="isSyncing ? 'bg-blue-600' : 'bg-green-600'">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                            <template x-if="isSyncing">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </template>
                            <template x-if="!isSyncing">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                        </div>
                        <div>
                            <p class="font-bold text-white text-sm" x-text="isSyncing ? 'Synchronisation en cours...' : 'Connexion rétablie !'"></p>
                            <template x-if="pendingCount > 0 && !isSyncing">
                                <p class="text-white/80 text-[11px]" x-text="pendingCount + ' post(s) en cours d\'envoi'"></p>
                            </template>
                        </div>
                    </div>
                    <template x-if="pendingCount > 0 && !isSyncing">
                        <button @click="syncNow()"
                                class="bg-white/20 hover:bg-white/30 text-white text-[11px] font-bold px-3 py-2 rounded-lg transition-colors">
                            Envoyer maintenant
                        </button>
                    </template>
                </div>
            </div>
        </div>
        HTML;
    }
}
