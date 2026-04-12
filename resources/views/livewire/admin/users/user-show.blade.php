<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" wire:navigate class="group p-2 -ml-2 rounded-xl text-gray-400 hover:text-midnight hover:bg-gray-100 transition-all">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-copper transition-colors">Admin</a>
                        <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                        <a href="{{ route('admin.users.index') }}" wire:navigate class="hover:text-copper transition-colors">Membres</a>
                    </nav>
                    <div class="flex items-center gap-3">
                        <h1 class="font-bold text-2xl text-midnight tracking-tight">{{ $user->name }}</h1>
                        <div class="flex items-center gap-1.5">
                            @if($user->is_admin)
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-indigo-100">Admin</span>
                            @endif
                            @if($user->is_tester)
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-amber-100">Testeur</span>
                            @endif
                            @if($user->is_banned)
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[9px] font-black uppercase tracking-widest rounded-md border border-red-100">Banni</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @php $role = $user->getRoleKey(); @endphp
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Statut Abonnement</p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-100 rounded-xl shadow-sm">
                        @if($role != 'free' && $role != 'tester')
                            <div class="w-2 h-2 rounded-full bg-copper animate-pulse"></div>
                            <span class="font-black text-xs text-midnight uppercase tracking-widest">Plan {{ $role }}</span>
                        @elseif($role == 'tester')
                            <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                            <span class="font-black text-xs text-midnight uppercase tracking-widest">Accès Testeur</span>
                        @else
                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                            <span class="font-black text-xs text-gray-400 uppercase tracking-widest">Gratuit</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8 bg-gray-50/30 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-10">

            @if (session()->has('success'))
                <div class="bg-midnight text-white p-4 rounded-2xl shadow-xl flex items-center gap-4 animate-in slide-in-from-top duration-300" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                    <div class="w-8 h-8 rounded-full bg-copper flex items-center justify-center text-white shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-sm font-bold uppercase tracking-wide">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Profile Card -->
                <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Profil Utilisateur</h3>
                    <div class="flex items-center gap-5 mb-8">
                        <div class="w-20 h-20 rounded-2xl bg-copper text-white flex items-center justify-center shadow-lg shadow-copper/20 shrink-0">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                <span class="text-3xl font-black">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-black text-xl text-midnight truncate">{{ $user->name }}</h4>
                            <p class="text-sm text-gray-400 font-medium truncate">{{ $user->email }}</p>
                            <div class="mt-2 text-[10px] font-bold text-gray-400 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Inscrit en {{ $user->created_at->translatedFormat('F Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3 pt-6 border-t border-gray-50">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-400 font-bold uppercase tracking-tight">Vérifié</span>
                            @if($user->email_verified_at)
                                <span class="text-green-600 font-black tracking-tighter uppercase">OUI</span>
                            @else
                                <span class="text-red-500 font-black tracking-tighter uppercase">NON</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-400 font-bold uppercase tracking-tight">Onboarding</span>
                            <span class="{{ $user->onboarding_completed ? 'text-green-600' : 'text-amber-500' }} font-black tracking-tighter uppercase">
                                {{ $user->onboarding_completed ? 'COMPLÉTÉ' : 'EN COURS' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Company Info -->
                <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Établissement</h3>
                    @if($user->company)
                        <div class="flex items-start gap-4 mb-8">
                             <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 shrink-0">
                                @if($user->company->logo_path)
                                    <img src="{{ Storage::url($user->company->logo_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-base text-midnight truncate">{{ $user->company->name ?? 'Nom non défini' }}</h4>
                                <p class="text-xs text-copper font-black uppercase tracking-widest mt-0.5">{{ $user->company->trade ?? 'Métier non défini' }}</p>
                                <p class="text-xs text-gray-400 mt-1 font-medium">{{ $user->company->city ?? 'Ville non définie' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-2xl border {{ $user->company->facebook_connected ? 'border-blue-100 bg-blue-50/30' : 'border-gray-50 bg-gray-50/50' }} transition-all text-center">
                                <p class="text-[9px] font-black uppercase tracking-tighter mb-1 {{ $user->company->facebook_connected ? 'text-blue-600' : 'text-gray-400' }}">Facebook</p>
                                <p class="text-[10px] font-black text-midnight">{{ $user->company->facebook_connected ? 'Connecté' : 'Offline' }}</p>
                            </div>
                            <div class="p-3 rounded-2xl border {{ $user->company->instagram_connected ? 'border-pink-100 bg-pink-50/30' : 'border-gray-50 bg-gray-50/50' }} transition-all text-center">
                                <p class="text-[9px] font-black uppercase tracking-tighter mb-1 {{ $user->company->instagram_connected ? 'text-pink-600' : 'text-gray-400' }}">Instagram</p>
                                <p class="text-[10px] font-black text-midnight">{{ $user->company->instagram_connected ? 'Connecté' : 'Offline' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="h-full flex flex-col items-center justify-center py-6 text-gray-300">
                            <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-xs font-bold uppercase tracking-widest text-center">En attente de setup</p>
                        </div>
                    @endif
                </div>

                <!-- Consumption Card -->
                <div class="bg-midnight rounded-3xl p-8 shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Utilisation des crédits</h3>
                        <div class="flex items-end gap-3 mb-6">
                            <span class="text-5xl font-black text-white leading-none">{{ $user->remaining_credits }}</span>
                            <span class="text-xs font-bold text-gray-400 pb-1.5 uppercase tracking-widest">Restants</span>
                        </div>
                        <div class="space-y-3">
                            <div class="h-3 w-full bg-white/10 rounded-full overflow-hidden border border-white/5">
                                @php $percent = $user->total_credits > 0 ? min(100, ($user->used_credits / $user->total_credits) * 100) : 0; @endphp
                                <div class="h-full bg-gradient-to-r from-copper to-amber-500 rounded-full transition-all duration-700" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <span>{{ $user->used_credits }} Consommés</span>
                                <span>{{ $user->total_credits }} Total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Section -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-50">
                    <!-- Credits Control -->
                    <div class="p-8 lg:p-10">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-copper/10 text-copper flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-sm font-black text-midnight uppercase tracking-widest">Ajuster Quota</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="relative">
                                <input type="number" wire:model="newTotalQuota" class="block w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-lg font-black text-midnight focus:ring-2 focus:ring-copper transition-shadow" placeholder="{{ $user->total_credits }}">
                                <button wire:click="updateQuota" wire:loading.attr="disabled" class="absolute right-2 top-2 bottom-2 px-6 bg-midnight text-white text-[10px] font-black rounded-xl hover:bg-black transition-all active:scale-95 uppercase tracking-widest shadow-lg inline-flex items-center justify-center gap-2">
                                    <svg wire:loading wire:target="updateQuota" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Mettre à jour
                                </button>
                            </div>
                            <p class="text-[10px] text-gray-400 font-medium leading-relaxed">Définit le quota total mensuel. Les crédits s'ajustent automatiquement.</p>
                            @error('newTotalQuota') <p class="text-red-500 text-[10px] font-bold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Plan Override -->
                    <div class="p-8 lg:p-10">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                            </div>
                            <h3 class="text-sm font-black text-midnight uppercase tracking-widest">Forcer le Plan</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button wire:click="setManualPlan('basic')" class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all {{ $user->manual_plan == 'basic' ? 'border-midnight bg-midnight text-white' : 'border-gray-50 bg-gray-50 text-gray-400 hover:border-gray-200' }}">
                                <span class="text-[10px] font-black uppercase tracking-widest">Starter</span>
                            </button>
                            <button wire:click="setManualPlan('pro')" class="flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all {{ $user->manual_plan == 'pro' ? 'border-copper bg-copper text-white' : 'border-gray-50 bg-gray-50 text-gray-400 hover:border-gray-200' }}">
                                <span class="text-[10px] font-black uppercase tracking-widest">Pro</span>
                            </button>
                            <button wire:click="setManualPlan(null)" class="col-span-2 flex items-center justify-center gap-2 p-4 rounded-2xl border-2 border-dashed border-gray-100 text-gray-400 hover:border-red-200 hover:text-red-500 transition-all font-bold text-[10px] uppercase tracking-widest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Réinitialiser (Auto)
                            </button>
                        </div>
                    </div>

                    <!-- Safety & Account -->
                    <div class="p-8 lg:p-10">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <h3 class="text-sm font-black text-midnight uppercase tracking-widest">Privilèges & Accès</h3>
                        </div>
                        <div class="space-y-3">
                            <button wire:click="toggleAdmin" wire:loading.attr="disabled" class="w-full flex items-center justify-between px-5 py-4 rounded-2xl transition-all {{ $user->is_admin ? 'bg-indigo-600 text-white' : 'bg-gray-50 text-gray-500' }}">
                                <div class="flex items-center gap-3">
                                    <svg wire:loading wire:target="toggleAdmin" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Accès Administrateur</span>
                                </div>
                                <div class="w-4 h-4 rounded-full border-2 {{ $user->is_admin ? 'bg-white' : 'border-gray-200' }}"></div>
                            </button>
                            <button wire:click="toggleTester" wire:loading.attr="disabled" class="w-full flex items-center justify-between px-5 py-4 rounded-2xl transition-all {{ $user->is_tester ? 'bg-amber-500 text-white' : 'bg-gray-50 text-gray-500' }}">
                                <div class="flex items-center gap-3">
                                    <svg wire:loading wire:target="toggleTester" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Mode Testeur</span>
                                </div>
                                <div class="w-4 h-4 rounded-full border-2 {{ $user->is_tester ? 'bg-white' : 'border-gray-200' }}"></div>
                            </button>
                            <button wire:click="toggleBan" wire:loading.attr="disabled" wire:confirm="Bannir l'utilisateur ? Il ne pourra plus se connecter." class="w-full flex items-center justify-center gap-2 px-5 py-4 rounded-2xl border-2 transition-all {{ $user->is_banned ? 'bg-red-600 text-white border-red-600' : 'border-red-100 text-red-600 hover:bg-red-50' }}">
                                <svg wire:loading wire:target="toggleBan" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $user->is_banned ? 'DÉBANNIR LE COMPTE' : 'BANNIR L\'UTILISATEUR' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Activity Journey Section (Compact) -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col max-h-[500px]">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-midnight text-white flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-[10px] font-black text-midnight uppercase tracking-widest">Activité Récente</h3>
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto p-2 sm:p-4 bg-gray-50/20">
                    <div class="space-y-1">
                        @forelse($activityLogs as $log)
                            @php
                                $dotColor = match($log->tag) {
                                    'admin' => 'bg-indigo-500',
                                    'auth'  => 'bg-rose-500',
                                    'posts' => 'bg-emerald-500',
                                    default => 'bg-gray-300'
                                };
                            @endphp
                            <div class="flex items-center gap-3 px-3 py-2 rounded-xl border border-transparent hover:border-gray-100 hover:bg-white transition-all group">
                                <div class="w-2 h-2 rounded-full {{ $dotColor }} shrink-0"></div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-[9px] font-black uppercase text-gray-400 shrink-0">
                                                {{ $log->tag }}
                                            </span>
                                            <span class="text-xs font-bold text-midnight truncate font-mono">
                                                {{ Str::after($log->url, request()->getSchemeAndHttpHost()) ?: '/' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="text-[9px] font-bold text-gray-300">{{ $log->created_at->format('H:i') }}</span>
                                            <span class="text-[9px] font-mono text-gray-400 bg-gray-100 px-1 rounded">{{ $log->response_time }}ms</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center">
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Aucune donnée</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Posts History -->
            <div>
                <div class="flex items-center justify-between mb-8">
                    <h2 class="font-black text-2xl text-midnight tracking-tight">Historique des créations</h2>
                    <span class="px-4 py-1.5 bg-white border border-gray-100 rounded-full text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $posts->total() }} Posts générés</span>
                </div>

                @if($posts->isEmpty())
                    <div class="bg-white rounded-[32px] border border-gray-100 p-20 text-center shadow-sm">
                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-gray-200">
                             <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Aucune activité pour le moment</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                        @foreach($posts as $post)
                            <div wire:click="selectPost({{ $post->id }})" class="group relative aspect-square bg-white rounded-2xl border border-gray-100 overflow-hidden cursor-pointer shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all">
                                @if($post->image_path)
                                    <img src="{{ Storage::url($post->image_path) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @elseif($post->slides)
                                     <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-400 gap-2">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                                        <span class="text-[9px] font-black uppercase tracking-widest">Carrousel</span>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-200">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                                
                                <!-- Overlay Info -->
                                <div class="absolute inset-0 bg-gradient-to-t from-midnight/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4">
                                    <span class="text-[9px] font-black text-copper uppercase tracking-widest mb-1">{{ $post->template ?? 'Standard' }}</span>
                                    <p class="text-white text-[10px] font-bold line-clamp-2">{{ $post->caption }}</p>
                                </div>

                                <div class="absolute top-2 right-2">
                                    <div class="w-2.5 h-2.5 rounded-full border-2 border-white {{ $post->status === 'published' ? 'bg-emerald-500' : ($post->status === 'failed' ? 'bg-red-500' : 'bg-amber-500') }} shadow-sm"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Post Detail Modal -->
    @if($selectedPost)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-midnight/90 backdrop-blur-xl" x-on:keydown.escape.window="$wire.closeModal()">
            <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col md:flex-row transform transition-all animate-in zoom-in-95 duration-200">
                <!-- Visual Side -->
                <div class="w-full md:w-3/5 bg-gray-50 flex items-center justify-center p-8 lg:p-12 border-r border-gray-100 overflow-y-auto">
                    @if($selectedPost->image_path)
                        <img src="{{ Storage::url($selectedPost->image_path) }}" class="max-w-full h-auto rounded-3xl shadow-2xl">
                    @elseif($selectedPost->slides)
                        <div class="grid grid-cols-2 gap-4 w-full">
                            @foreach($selectedPost->slides as $slide)
                                <img src="{{ Storage::url($slide) }}" class="w-full aspect-square object-cover rounded-2xl shadow-lg border border-white">
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Info Side -->
                <div class="w-full md:w-2/5 flex flex-col bg-white overflow-y-auto">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-start sticky top-0 bg-white/80 backdrop-blur-md z-10">
                        <div>
                            <span class="text-[10px] font-black text-copper bg-copper/5 px-3 py-1 rounded-full uppercase tracking-widest mb-2 inline-block">Modèle {{ $selectedPost->template ?? 'Standard' }}</span>
                            <h2 class="text-sm font-black text-midnight uppercase tracking-tight">{{ $selectedPost->created_at->translatedFormat('d F Y \à H:i') }}</h2>
                        </div>
                        <button wire:click="closeModal" class="p-2 bg-gray-50 text-gray-400 hover:text-midnight hover:bg-gray-100 rounded-2xl transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-10">
                        <!-- Caption -->
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Légende générée</p>
                            <div class="bg-gray-50 rounded-3xl p-6 text-sm text-midnight font-bold leading-relaxed border border-gray-100 shadow-inner select-all">
                                {{ $selectedPost->caption }}
                            </div>
                        </div>

                        <!-- Status & Feedback -->
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Statut de la génération</p>
                            <div class="flex items-center gap-4">
                                <span class="px-4 py-2 {{ $selectedPost->status_color_class }} text-[10px] font-black rounded-xl border uppercase tracking-widest text-center">
                                    {{ $selectedPost->status_label }}
                                </span>
                                @if($selectedPost->error_message)
                                    <p class="text-[10px] text-red-500 font-bold max-w-xs leading-tight italic">{{ $selectedPost->error_message }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Technical Info -->
                        <div class="pt-10 border-t border-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-3 text-[10px] font-bold text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Généré en {{ $selectedPost->created_at->diffForHumans($selectedPost->updated_at, true) }}
                            </div>
                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">ID: #{{ $selectedPost->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
