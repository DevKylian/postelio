<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Membres
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Gestion des utilisateurs enregistrés sur la plateforme
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Search Bar -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="relative w-full max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Chercher par nom, par email..." class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-copper/30 focus:border-copper transition sm:text-sm">
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-6 py-4 font-medium">Utilisateur</th>
                                <th class="px-6 py-4 font-medium">Entité / Métier</th>
                                <th class="px-6 py-4 font-medium">Plan & Quotas</th>
                                <th class="px-6 py-4 font-medium">Inscription</th>
                                <th class="px-6 py-4 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $u)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-copper text-white flex items-center justify-center font-bold text-lg shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $u) }}" wire:navigate class="font-bold text-midnight hover:text-copper transition-colors">{{ $u->name }}</a>
                                            <div class="text-[11px] text-gray-400">{{ $u->email }}</div>
                                            @if($u->is_admin)
                                                <span class="inline-block mt-0.5 px-2 py-0.5 bg-purple-100 text-purple-700 text-[9px] font-bold uppercase rounded-md">Admin</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-midnight">{{ optional($u->company)->name ?? 'Non défini' }}</div>
                                    <div class="text-xs text-gray-400">{{ optional($u->company)->trade ?? 'Non défini' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($u->currentPlanKey())
                                        <span class="px-2.5 py-1 bg-copper/10 text-copper font-bold text-[11px] rounded-md capitalize">
                                            {{ $u->currentPlanKey() }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-500 font-bold text-[11px] rounded-md uppercase">
                                            Gratuit
                                        </span>
                                    @endif
                                    <div class="text-[11px] text-gray-500 mt-1 font-medium">{{ $u->remaining_credits }} posts restants</div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-medium">
                                    {{ $u->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.users.show', $u) }}" wire:navigate class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-midnight hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    </div>
                                    <p class="text-sm text-gray-400 font-medium">Aucun utilisateur trouvé.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
