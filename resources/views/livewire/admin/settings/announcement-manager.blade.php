<div class="py-12">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-megaphone"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8 a3 3 0 1 1-5.8-1.6"/></svg>
            </div>
            <h2 class="font-display font-semibold text-2xl text-midnight leading-tight">
                Gestion du bandeau d'annonce
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-slate-200/60 transition-all duration-300">
            <div class="p-8">
                <!-- Success Message -->
                @if (session()->has('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                <form wire:submit="save" class="space-y-8">
                    <!-- Text -->
                    <div>
                        <label for="text" class="block text-sm font-bold text-midnight mb-2">Texte de l'annonce</label>
                        <input type="text" id="text" wire:model="text" 
                               class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 transition-all shadow-sm"
                               placeholder="Ex: Nouveau : Postez 2x plus vite avec notre nouvelle IA artisan !">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Link Text -->
                        <div>
                            <label for="link_text" class="block text-sm font-bold text-midnight mb-2">Texte du lien / bouton</label>
                            <input type="text" id="link_text" wire:model="link_text" 
                                   class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 transition-all shadow-sm"
                                   placeholder="Ex: Essayer gratuitement →">
                        </div>

                        <!-- Route Select -->
                        <div>
                            <label for="route" class="block text-sm font-bold text-midnight mb-2">Destination du lien</label>
                            <select id="route" wire:model="route" 
                                    class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 transition-all shadow-sm">
                                @foreach($availableRoutes as $name => $label)
                                    <option value="{{ $name }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Visibility Toggle -->
                    <div class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <div>
                            <h4 class="text-sm font-bold text-midnight">Visibilité</h4>
                            <p class="text-xs text-slate-500 mt-1">Activer ou désactiver l'affichage du bandeau sur la landing page.</p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-600 shadow-inner"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Footer / Preview -->
                    <div class="pt-6 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-xs text-slate-500 italic flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            Les modifications sont appliquées instantanément.
                        </div>
                        <button type="submit" class="w-full md:w-auto px-10 py-3.5 bg-midnight text-white text-sm font-bold rounded-2xl hover:bg-slate-800 transition-all shadow-lg hover:shadow-xl active:scale-95 flex items-center justify-center gap-2">
                             Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
