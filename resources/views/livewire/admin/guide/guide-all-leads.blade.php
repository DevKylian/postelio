<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.guides.index') }}" wire:navigate class="p-2 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-midnight hover:border-slate-300 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h1 class="font-display font-bold text-2xl text-midnight tracking-tight leading-tight">
                        Tous les Leads
                    </h1>
                    <p class="text-slate-400 text-sm mt-1 font-medium">
                        Centralisation de tous les emails collectés sur l'ensemble des guides.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un email..."
                           class="pl-11 pr-4 py-2.5 bg-white border-slate-200 rounded-2xl text-sm font-medium text-midnight focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none min-w-[300px] shadow-sm">
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Email</th>
                            <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Guide Source</th>
                            <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">Date de collecte</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($leads as $lead)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-500 font-black text-sm group-hover:from-orange-500 group-hover:to-amber-500 group-hover:text-white transition-all shadow-inner">
                                        {{ strtoupper(substr($lead->email, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-bold text-midnight tracking-tight">{{ $lead->email }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @if($lead->guide)
                                    <a href="{{ route('admin.guides.leads', $lead->guide->id) }}" wire:navigate class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500 hover:bg-orange-50 hover:text-orange-600 transition-all text-xs font-bold ring-1 ring-slate-200 group-hover:ring-orange-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        {{ $lead->guide->title }}
                                    </a>
                                @else
                                    <span class="text-slate-300 text-xs italic">Guide supprimé</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-medium text-slate-500">{{ $lead->created_at->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-bold text-slate-300 ml-2 uppercase tracking-widest">{{ $lead->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-200 mx-auto mb-6">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <p class="text-slate-400 font-bold tracking-tight">Aucun email collecté pour l'instant.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leads->hasPages())
                <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                    {{ $leads->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
