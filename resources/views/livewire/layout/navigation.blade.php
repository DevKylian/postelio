<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<div class="lg:w-64 lg:flex-shrink-0 z-50">
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-midnight/50 backdrop-blur-sm z-40 lg:hidden"
         style="display: none;"></div>

    <aside
        id="sidebar"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 w-64 z-50 flex flex-col transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen"
        style="background: #0F172A;">

        <!-- Mobile Close -->
        <button @click="sidebarOpen = false" class="lg:hidden absolute top-4 right-4 text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Logo -->
        <div class="px-5 pt-6 pb-4 border-b border-white/[0.06]">
            <a href="{{ route('dashboard') }}" wire:navigate>
                <x-logo size="md" textClass="text-[1.4rem] text-white" />
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">

            <!-- Dashboard -->
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Tableau de bord</span>
            </x-sidebar-link>

            <!-- Section: Outils -->
            <div class="pt-5 pb-2 px-3">
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.12em]">Créations</p>
            </div>

            <x-sidebar-link :href="route('generate-post')" :active="request()->routeIs('generate-post')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <span>Générer un post</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('posts.index')" :active="request()->routeIs('posts.index')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Mes créations</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('calendar')" :active="request()->routeIs('calendar')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Calendrier</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('templates-preview')" :active="request()->routeIs('templates-preview')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                </svg>
                <span>Gabarits</span>
            </x-sidebar-link>


            <x-sidebar-link :href="route('statistics')" :active="request()->routeIs('statistics')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Statistiques</span>
            </x-sidebar-link>
            
            <x-sidebar-link :href="route('changelog')" :active="request()->routeIs('changelog')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span>Nouveautés</span>
            </x-sidebar-link>

            @if(auth()->user() && auth()->user()->is_admin)
            <!-- Section: Administration -->
            <div class="pt-5 pb-2 px-3">
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.12em]">Administration</p>
            </div>

            <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Dashboard Admin</span>
            </x-sidebar-link>
            
            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Utilisateurs</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.templates.index')" :active="request()->routeIs('admin.templates.*')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                </svg>
                <span>Gabarits</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.changelog.index')" :active="request()->routeIs('admin.changelog.*')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Changelog</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('admin.blog.index')" :active="request()->routeIs('admin.blog.*')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <span>Blog</span>
            </x-sidebar-link>
            @endif

            <!-- Section: Compte -->
            <div class="pt-5 pb-2 px-3">
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.12em]">Compte</p>
            </div>

            <x-sidebar-link :href="route('post-settings')" :active="request()->routeIs('post-settings')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Paramètres</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('subscription.plans')" :active="request()->routeIs('subscription.*')" wire:navigate>
                <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Abonnement</span>
            </x-sidebar-link>

        </nav>

        <!-- Quota Progress Bar -->
        <div class="px-3 py-4 border-t border-white/[0.04]">
            <div class="bg-white/[0.03] border border-white/[0.06] rounded-2xl p-4 shadow-sm relative overflow-hidden group">
                <!-- Background decoration -->
                <div class="absolute -right-4 -top-8 w-16 h-16 bg-copper/10 blur-2xl rounded-full"></div>
                
                <div class="flex items-center justify-between mb-3 relative z-10">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full bg-copper shadow-[0_0_8px_rgba(184,115,51,0.4)]"></div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Quota Mensuel</span>
                    </div>
                    <span class="text-[12px] font-bold text-slate-200 tabular-nums">
                        @if(auth()->user()->is_admin || auth()->user()->is_premium)
                            <span class="text-lg leading-none">∞</span>
                        @else
                            {{ auth()->user()->remaining_credits }} <span class="text-slate-500 font-medium">/ {{ auth()->user()->total_credits }}</span>
                        @endif
                    </span>
                </div>

                <div class="h-1.5 w-full bg-white/[0.05] rounded-full overflow-hidden shadow-inner relative z-10">
                    @php
                        $remaining = auth()->user()->remaining_credits;
                        $total = max(1, auth()->user()->total_credits);
                        $isUnlimited = auth()->user()->is_admin || auth()->user()->is_premium;
                        $percentage = $isUnlimited ? 100 : min(100, ($remaining / $total) * 100);
                        $colorClass = $isUnlimited ? 'from-amber-400 to-copper' : ($percentage < 15 ? 'from-rose-500 to-red-600' : ($percentage < 40 ? 'from-orange-500 to-amber-500' : 'from-amber-400 to-copper'));
                    @endphp
                    <div class="h-full bg-gradient-to-r {{ $colorClass }} transition-all duration-1000 rounded-full relative"
                         style="width: {{ $percentage }}%">
                        <div class="absolute inset-0 bg-white/20 opacity-50 group-hover:translate-x-full transition-transform duration-1000 ease-in-out"></div>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between relative z-10">
                    @if(auth()->user()->hasActiveSubscription())
                        <p class="text-[10px] text-slate-500 font-medium">
                            Plan {{ ucfirst(auth()->user()->currentPlanKey() ?? 'gratuit') }}
                        </p>
                    @else
                        <a href="{{ route('subscription.plans') }}" wire:navigate
                           class="text-[10px] text-copper font-semibold hover:text-amber-400 transition-colors">
                            Augmenter
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer / Profile -->
        <div class="shrink-0 p-3 border-t border-white/[0.06]">
            <x-dropdown align="top" width="w-full">
                <x-slot name="trigger">
                    <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/[0.06] transition-all duration-150 group">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-copper to-amber-600 flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-lg">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 text-left min-w-0 hidden lg:block">
                            <p class="text-[13px] font-semibold text-slate-200 truncate leading-tight"
                               x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                               x-text="name"
                               x-on:profile-updated.window="name = $event.detail.name"></p>
                            <p class="text-[11px] text-slate-500 truncate leading-tight mt-0.5">{{ auth()->user()->email }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-slate-300 transition-colors shrink-0 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile')" wire:navigate>
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Mon profil
                    </x-dropdown-link>
                    <div class="border-t border-gray-100 my-1"></div>
                    <button wire:click="logout" class="w-full text-start">
                        <x-dropdown-link class="text-red-600 hover:text-red-700 hover:bg-red-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Se déconnecter
                        </x-dropdown-link>
                    </button>
                </x-slot>
            </x-dropdown>
        </div>
    </aside>
</div>
