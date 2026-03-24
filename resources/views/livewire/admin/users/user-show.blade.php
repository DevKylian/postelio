<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" wire:navigate class="p-2 -ml-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h1 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3">
                        {{ $user->name }}
                        @if($user->is_admin)
                            <span class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-semibold uppercase tracking-wider rounded-md">Admin</span>
                        @endif
                        @if($user->is_banned)
                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-semibold uppercase tracking-wider rounded-md">Banni</span>
                        @endif
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($user->currentPlanKey())
                    <span class="px-4 py-2 bg-copper/10 text-copper font-semibold text-sm rounded-lg capitalize">
                        Plan {{ $user->currentPlanKey() }}
                    </span>
                @else
                    <span class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-sm rounded-lg uppercase">
                        Gratuit
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">

            @if (session()->has('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 text-sm font-medium flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Company Details -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-6">Entreprise</h3>
                        @if($user->company)
                            <div class="flex items-center gap-4 mb-4">
                                @if($user->company->logo)
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 shrink-0">
                                        <img src="{{ Storage::url($user->company->logo) }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 shrink-0">
                                        <span class="text-xl font-semibold">{{ strtoupper(substr($user->company->name ?? $user->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="w-full overflow-hidden">
                                    <p class="font-semibold text-lg text-gray-900 truncate" title="{{ $user->company->name ?? 'Enregistrée' }}">{{ $user->company->name ?? 'Enregistrée' }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $user->company->trade ?? 'Secteur non renseigné' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 text-gray-400 mt-2">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <p class="text-sm">Onboarding non complété</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quota & Subscription -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-4">Quotas & Facturation</h3>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-3xl font-bold text-gray-900">{{ $user->remaining_credits }}</span>
                            <span class="text-sm text-gray-500">posts restants</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                            <div class="bg-copper h-2 rounded-full" style="width: {{ $user->total_credits > 0 ? min(100, ($user->used_credits / $user->total_credits) * 100) : 0 }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>{{ $user->used_credits }} consommés / {{ $user->total_credits }}</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Abonnements actifs</span>
                        <span class="font-semibold text-gray-900">{{ $user->subscriptions()->active()->count() }}</span>
                    </div>
                </div>

                <!-- Social Details -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 mb-5">Réseaux Sociaux</h3>
                        <div class="space-y-4">
                            <!-- Facebook -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $user->company?->facebook_connected ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">Facebook</span>
                                </div>
                                <span class="text-xs font-semibold {{ $user->company?->facebook_connected ? 'text-green-600' : 'text-gray-400' }}">{{ $user->company?->facebook_connected ? 'Connecté' : 'Non lié' }}</span>
                            </div>

                            <!-- Instagram -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $user->company?->instagram_connected ? 'bg-pink-100 text-pink-600' : 'bg-gray-100 text-gray-400' }}">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">Instagram</span>
                                </div>
                                <span class="text-xs font-semibold {{ $user->company?->instagram_connected ? 'text-green-600' : 'text-gray-400' }}">{{ $user->company?->instagram_connected ? 'Connecté' : 'Non lié' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Zone -->
                <div class="bg-white rounded-xl border-t-4 border-t-red-500 shadow-sm p-6 flex flex-col justify-between relative overflow-hidden">
                    <h3 class="text-sm font-semibold text-gray-900 mb-5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        Zone Admin
                    </h3>

                    <div class="space-y-4">
                        <!-- Quota Input -->
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1.5 block">Modifier Quota Mensuel</label>
                            <div class="flex items-center gap-2">
                                <input type="number" wire:model="newTotalQuota" class="block w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-1 focus:ring-copper focus:border-copper transition-shadow" placeholder="{{ $user->total_credits }}">
                                <button wire:click="updateQuota" class="px-4 py-2 bg-copper hover:bg-copper-dark text-white text-sm font-semibold rounded-lg transition-colors">MAJ</button>
                            </div>
                            @error('newTotalQuota') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <!-- Role Toggle -->
                            <button wire:click="toggleAdmin" class="flex flex-col items-center justify-center gap-1.5 p-2 rounded-lg border {{ $user->is_admin ? 'border-purple-200 bg-purple-50 text-purple-700 hover:bg-purple-100' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }} transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                <span class="text-[10px] font-semibold text-center">{{ $user->is_admin ? 'Rétrograder Admin' : 'Promouvoir Admin' }}</span>
                            </button>

                            <!-- Premium Toggle -->
                            <button wire:click="togglePremium" class="flex flex-col items-center justify-center gap-1.5 p-2 rounded-lg border {{ $user->is_premium ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50' }} transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                                <span class="text-[10px] font-semibold text-center">{{ $user->is_premium ? 'Retirer Premium' : 'Rendre Premium' }}</span>
                            </button>

                            <!-- Ban Toggle -->
                            <button wire:click="toggleBan" wire:confirm="Êtes-vous sûr de vouloir {{ $user->is_banned ? 'débannir' : 'bannir' }} cet utilisateur ?" class="flex flex-col items-center justify-center gap-1.5 p-2 col-span-2 rounded-lg border {{ $user->is_banned ? 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100' : 'border-gray-200 bg-white text-red-600 hover:bg-red-50' }} transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                <span class="text-[10px] font-semibold text-center">{{ $user->is_banned ? 'Débannir Compte' : 'Bannir Compte' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- User Posts Grid -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-lg text-gray-900">Historique des Générations ({{ $posts->total() }})</h2>
                </div>

                @if($posts->isEmpty())
                    <div class="bg-gray-50 rounded-xl border border-dashed border-gray-200 p-12 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <p class="text-gray-500 font-medium">Cet utilisateur n'a généré aucun contenu.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
                        @foreach($posts as $post)
                            <div wire:click="selectPost({{ $post->id }})" class="bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col hover:shadow-md transition-shadow cursor-pointer">
                                <div class="aspect-square bg-gray-100 relative">
                                    @if($post->image_path)
                                        <img src="{{ Storage::url($post->image_path) }}" class="w-full h-full object-cover">
                                    @elseif($post->slides)
                                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 gap-2">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Carrousel</p>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif

                                    <!-- Status indicator -->
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2 py-0.5 {{ $post->status_color_class }} text-[10px] font-semibold border rounded-md shadow-sm">
                                            {{ $post->status_label }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-3 flex flex-col flex-1">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[10px] font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">{{ $post->template ?? 'Standard' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-800 font-medium line-clamp-2 mt-1">{{ $post->caption ?? 'Génération sans légende.' }}</p>
                                    <div class="mt-auto pt-2 flex items-center justify-between text-[10px] text-gray-400">
                                        <span>{{ $post->created_at->format('d/m/Y') }}</span>
                                        <span>{{ $post->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($posts->hasPages())
                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>

    <!-- Modal Détail Post -->
    @if($selectedPost)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" wire:click.self="closeModal">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col md:flex-row transform transition-all">
                <!-- Image Side -->
                <div class="w-full md:w-1/2 bg-gray-50 relative flex items-center justify-center p-6 border-r border-gray-100 min-h-[300px]">
                    @if($selectedPost->image_path)
                        <img src="{{ Storage::url($selectedPost->image_path) }}" class="max-w-full max-h-full object-contain rounded-xl shadow-sm">
                    @elseif($selectedPost->slides)
                        <div class="w-full h-full flex flex-col items-center justify-center">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 inline-block">Carrousel ({{ is_array($selectedPost->slides) ? count($selectedPost->slides) : 0 }} slides)</span>
                            <div class="grid grid-cols-2 gap-3 w-full max-w-sm">
                                @if(is_array($selectedPost->slides))
                                    @foreach(array_slice($selectedPost->slides, 0, 4) as $slide)
                                        <div class="aspect-square rounded-xl overflow-hidden shadow-sm border border-gray-200 bg-white">
                                            <img src="{{ Storage::url($slide) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-gray-400 font-medium flex flex-col items-center gap-3">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Aucun visuel disponible
                        </div>
                    @endif
                </div>
                
                <!-- Content Side -->
                <div class="w-full md:w-1/2 flex flex-col h-full max-h-[50vh] md:max-h-none overflow-y-auto bg-white">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-md uppercase tracking-wider">{{ $selectedPost->template ?? 'Standard' }}</span>
                            <span class="text-xs font-medium text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ $selectedPost->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 bg-gray-50 hover:bg-gray-200 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="p-6 flex-1 flex flex-col gap-6">
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                                Légende générée
                            </h4>
                            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-800 whitespace-pre-wrap border border-gray-100 leading-relaxed shadow-inner font-medium">{{ $selectedPost->caption ?? 'Aucune légende n\'a été générée pour ce post.' }}</div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Statut
                            </h4>
                            <div class="inline-flex items-center gap-2 px-3.5 py-2 {{ $selectedPost->status_color_class }} border text-sm font-semibold rounded-xl">
                                {{ $selectedPost->status_label }}
                            </div>

                            @if($selectedPost->status === 'failed' && $selectedPost->error_message)
                                <div class="mt-2 p-3 bg-red-50 border border-red-100 text-red-700 text-xs rounded-lg break-all">
                                    {{ $selectedPost->error_message }}
                                </div>
                            @endif
                        </div>

                         @if($selectedPost->audio_path)
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                                    Audio source
                                </h4>
                                <audio controls class="w-full h-12 rounded-lg bg-gray-50 border border-gray-100">
                                    <source src="{{ Storage::url($selectedPost->audio_path) }}" type="audio/mpeg">
                                 </audio>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
