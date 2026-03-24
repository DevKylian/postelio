<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Gestion des Gabarits
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Activez ou désactivez les modèles disponibles pour vos utilisateurs.
                </p>
            </div>
            <a href="{{ route('admin.dashboard') }}" wire:navigate class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Retour au dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-12">
            
            {{-- Gabarits Natifs --}}
            <section>
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="font-bold text-lg text-midnight">Gabarits Natifs</h2>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($nativeTemplates as $nt)
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <h3 class="font-bold text-midnight text-lg">{{ $nt['name'] }}</h3>
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $nt['description'] }}</p>
                            </div>
                            <div class="shrink-0">
                                <span class="px-2 py-1 bg-gray-100 text-gray-400 text-[10px] font-bold uppercase rounded-md">Système</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                            <span class="text-sm font-medium {{ $nt['is_active'] ? 'text-green-600' : 'text-red-500' }}">
                                {{ $nt['is_active'] ? 'Activé' : 'Désactivé' }}
                            </span>
                            
                            <button wire:click="toggleNative('{{ $nt['slug'] }}')" 
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $nt['is_active'] ? 'bg-copper' : 'bg-gray-200' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $nt['is_active'] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- Gabarits Personnalisés --}}
            <section>
                <div class="flex items-center gap-4 mb-6">
                    <h2 class="font-bold text-lg text-midnight">Gabarits Personnalisés (IA)</h2>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-400 font-medium border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Modèle</th>
                                <th class="px-6 py-4">Utilisateur</th>
                                <th class="px-6 py-4">Créé le</th>
                                <th class="px-6 py-4 text-right">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($customTemplates as $ct)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-midnight">{{ $ct->name }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $ct->description ?: 'Aucune description.' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($ct->user)
                                        <div class="font-medium text-midnight">{{ $ct->user->name }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $ct->user->email }}</div>
                                    @else
                                        <span class="text-gray-300">Inconnu</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $ct->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <span class="text-xs font-semibold {{ $ct->is_active ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $ct->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                        <button wire:click="toggleCustom({{ $ct->id }})" 
                                                class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none {{ $ct->is_active ? 'bg-copper' : 'bg-gray-200' }}">
                                            <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $ct->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                    Aucun gabarit personnalisé n'a encore été créé.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </div>
</div>
