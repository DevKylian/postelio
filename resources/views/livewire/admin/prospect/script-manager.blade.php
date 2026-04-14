<div class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <h2 class="font-bold text-base text-midnight flex items-center gap-2">
                <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Bibliothèque de Scripts
            </h2>
            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <input type="text" wire:model="newName" class="w-full sm:w-64 text-xs rounded-lg border-gray-200 focus:border-copper focus:ring-copper" placeholder="Nom du nouveau script...">
                <button wire:click="addScript" class="bg-midnight text-white px-4 py-2.5 rounded-lg text-xs font-bold hover:bg-gray-800 transition-colors shadow-sm whitespace-nowrap">
                    + Nouveau Script
                </button>
            </div>
        </div>

        @if (session()->has('script_message'))
            <div class="mb-4 text-xs text-green-600 bg-green-50 p-2 rounded-lg">
                {{ session('script_message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($scripts as $script)
                <div class="bg-slate-100/50 rounded-2xl border border-slate-200/50 p-4 sm:p-5 relative group">
                    <button 
                        wire:click="deleteScript({{ $script->id }})" 
                        wire:confirm="Supprimer ce script ?"
                        class="absolute top-4 right-4 text-slate-300 hover:text-red-500 lg:opacity-0 lg:group-hover:opacity-100 transition-all"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>

                    <div class="mb-5">
                        <input 
                            type="text" 
                            value="{{ $script->name }}" 
                            onblur="@this.updateName({{ $script->id }}, this.value)"
                            class="bg-transparent border-none p-0 font-bold text-midnight focus:ring-0 w-4/5 text-sm uppercase tracking-tight"
                        >
                    </div>

                    <div class="space-y-4">
                        @foreach($script->sequences as $index => $content)
                            <div x-data="{ copied: false }" class="relative">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-slate-200 flex items-center justify-center text-[8px] text-slate-500">{{ $index + 1 }}</span>
                                    Séquence
                                </label>
                                <div class="relative group/seq">
                                    <textarea 
                                        x-ref="message"
                                        onblur="@this.updateSequence({{ $script->id }}, {{ $index }}, this.value)"
                                        class="w-full rounded-xl border-slate-200 text-xs text-slate-600 focus:border-copper focus:ring-copper p-3.5 pr-12 min-h-[90px] bg-white shadow-sm transition-all"
                                        placeholder="Entrez votre message ici..."
                                    >{{ $content }}</textarea>
                                    
                                    <div class="absolute right-2 top-2 flex flex-col gap-2">
                                        <button 
                                            @click="
                                                navigator.clipboard.writeText($refs.message.value);
                                                copied = true;
                                                setTimeout(() => copied = false, 2000);
                                            "
                                            class="w-8 h-8 flex items-center justify-center rounded-lg transition-all"
                                            :class="copied ? 'bg-green-500 text-white' : 'bg-slate-50 text-slate-400 hover:text-copper hover:bg-amber-50 border border-slate-100 shadow-sm'"
                                            title="Copier"
                                        >
                                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012-2m0 0h2a2 2 0 012 2m0 0h2a2 2 0 012 2" /></svg>
                                            <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                        
                                        @if(count($script->sequences) > 1)
                                            <button 
                                                wire:click="removeSequence({{ $script->id }}, {{ $index }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-300 hover:text-red-500 hover:bg-red-50 border border-slate-100 shadow-sm transition-all"
                                                title="Supprimer la séquence"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div x-show="copied" x-cloak x-transition class="absolute -top-6 right-0 text-[10px] font-bold text-green-500">Copié !</div>
                            </div>
                        @endforeach

                        <button 
                            wire:click="addSequence({{ $script->id }})"
                            class="w-full py-2 border-2 border-dashed border-slate-200 rounded-xl text-slate-400 text-[10px] font-bold uppercase tracking-widest hover:border-copper/30 hover:text-copper transition-all"
                        >
                            + Ajouter une séquence
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @if($scripts->isEmpty())
            <div class="py-12 text-center">
                <p class="text-sm text-slate-400 italic">Aucun script enregistré. Commencez par en créer un ci-dessus.</p>
            </div>
        @endif
    </div>
</div>
