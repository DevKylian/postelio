<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Administration Globale
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Vue d'ensemble et monitoring de la plateforme
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Users -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Utilisateurs inscrits</p>
                        <p class="text-2xl font-bold text-midnight">{{ number_format($totalUsers) }}</p>
                    </div>
                </div>

                <!-- Total Posts -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Créations générées</p>
                        <p class="text-2xl font-bold text-midnight">{{ number_format($totalPosts) }}</p>
                    </div>
                </div>

                <!-- Active Subscriptions -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Abonnements Actifs</p>
                        <p class="text-2xl font-bold text-midnight">{{ number_format($activeSubscriptions) }}</p>
                    </div>
                </div>

                <!-- Failed Posts -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 font-medium">Créations Échouées</p>
                        <p class="text-2xl font-bold text-midnight">{{ number_format($failedPosts) }}</p>
                    </div>
                </div>
            </div>

            <!-- Configuration Système -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-copper/10 text-copper flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-midnight">Génération de posts</h3>
                            <p class="text-sm text-gray-400">Activer ou désactiver la création de nouveaux posts pour tous les utilisateurs.</p>
                        </div>
                    </div>
                    <div>
                        <button 
                            wire:click="togglePostGeneration"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $postGenerationEnabled ? 'bg-copper' : 'bg-gray-200' }}"
                        >
                            <span class="sr-only">Toggle Post Generation</span>
                            <span 
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $postGenerationEnabled ? 'translate-x-5' : 'translate-x-0' }}"
                            ></span>
                        </button>
                    </div>
                </div>
                @if (session()->has('success'))
                    <div class="mt-4 p-3 bg-green-50 text-green-700 text-sm font-bold rounded-xl border border-green-100 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Users -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-[500px]">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between shrink-0">
                        <h2 class="font-bold text-base text-midnight">Derniers Utilisateurs</h2>
                    </div>
                    <div class="overflow-y-auto flex-1 p-2">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="text-gray-400 sticky top-0 bg-white shadow-[0_1px_0_0_#f3f4f6]">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Utilisateur</th>
                                    <th class="px-4 py-3 font-medium">Plan</th>
                                    <th class="px-4 py-3 font-medium text-right">Inscription</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentUsers as $u)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.users.show', $u) }}" wire:navigate class="font-semibold text-midnight truncate max-w-[150px] block hover:text-copper transition-colors">{{ $u->name }}</a>
                                        <div class="text-[11px] text-gray-400 truncate max-w-[150px]">{{ $u->email }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($u->currentPlanKey())
                                            <span class="px-2.5 py-1 bg-copper/10 text-copper font-bold text-[10px] rounded-md capitalize">
                                                {{ $u->currentPlanKey() }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-gray-100 text-gray-500 font-bold text-[10px] rounded-md uppercase">
                                                Gratuit
                                            </span>
                                        @endif
                                        <div class="text-[10px] text-gray-400 mt-1 font-medium">{{ $u->remaining_credits }} posts left</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs text-right font-medium">
                                        {{ $u->created_at->format('d M H:i') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400 text-sm">Aucun utilisateur.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-[500px]">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between shrink-0">
                        <h2 class="font-bold text-base text-midnight">Activité des Générations</h2>
                    </div>
                    <div class="overflow-y-auto flex-1 p-2">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="text-gray-400 sticky top-0 bg-white shadow-[0_1px_0_0_#f3f4f6]">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Visuel</th>
                                    <th class="px-4 py-3 font-medium">Auteur</th>
                                    <th class="px-4 py-3 font-medium text-right">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentPosts as $p)
                                <tr wire:click="selectPost({{ $p->id }})" class="hover:bg-gray-50/50 transition-colors cursor-pointer">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($p->image_path)
                                                <img src="{{ Storage::url($p->image_path) }}" class="w-9 h-9 rounded-md object-cover bg-gray-100 border border-gray-200">
                                            @elseif($p->slides)
                                                <div class="w-9 h-9 rounded-md bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                </div>
                                            @else
                                                <div class="w-9 h-9 rounded-md bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-midnight truncate max-w-[120px]">{{ $p->template ?? 'Standard' }}</div>
                                                <div class="text-[10px] text-gray-400 capitalize">{{ $p->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($p->user)
                                            <a href="{{ route('admin.users.show', $p->user) }}" wire:navigate class="font-medium text-midnight truncate max-w-[100px] block hover:text-copper transition-colors">{{ $p->user->name }}</a>
                                        @else
                                            <div class="font-medium text-midnight truncate max-w-[100px]">Inconnu</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="px-2 py-0.5 {{ $p->status_color_class }} border text-[10px] font-bold uppercase rounded-md">
                                            {{ $p->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400 text-sm">Aucun post généré.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
                        @if($selectedPost->user)
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">Auteur</h4>
                                <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-3 rounded-xl">
                                    <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center text-gray-500 font-bold shrink-0">
                                        {{ strtoupper(substr($selectedPost->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-midnight">{{ $selectedPost->user->name }}</p>
                                        <p class="text-[10px] font-medium text-gray-400">{{ $selectedPost->user->email }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

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
