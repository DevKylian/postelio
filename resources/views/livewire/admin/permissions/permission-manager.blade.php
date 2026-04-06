<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Gestion des Roles & Permissions
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Configurez les quotas et les accès par type de compte.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-600 font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    Retour Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center gap-3 animate-fade-in shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($roles as $key => $perms)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col hover:border-gray-200 transition-all">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-midnight text-white flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr($key, 0, 1)) }}
                                </div>
                                <div>
                                    <h2 class="font-bold text-base text-midnight">{{ $roleLabels[$key] ?? ucfirst($key) }}</h2>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $key }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-6 flex-1">
                            <!-- Quota Section -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-copper/10 text-copper flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-midnight">Quota mensuel</p>
                                        <p class="text-[10px] text-gray-400">Nombre de posts autorisés par mois.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($editingQuota === $key)
                                        <input 
                                            type="number" 
                                            wire:model.defer="newQuota"
                                            class="w-20 px-3 py-1.5 border border-copper rounded-xl text-sm font-bold focus:ring-copper focus:border-copper"
                                            wire:keydown.enter="saveQuota"
                                        >
                                        <button wire:click="saveQuota" class="p-1.5 bg-copper text-white rounded-lg hover:bg-amber-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    @else
                                        <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm cursor-pointer hover:border-copper transition-all" wire:click="startEditingQuota('{{ $key }}')">
                                            <span class="text-lg font-black text-midnight">{{ $perms['monthly_quota'] >= 999999 ? '∞' : $perms['monthly_quota'] }}</span>
                                            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Toggles List -->
                            <div class="space-y-4">
                                @php
                                    $toggles = [
                                        'can_generate_posts' => ['Génération de posts', 'Autoriser la création via IA.'],
                                        'can_connect_social' => ['Réseaux Sociaux', 'Relier Facebook & Instagram.'],
                                        'has_premium_templates' => ['Modèles Premiums', 'Accès aux gabarits de haute qualité.'],
                                        'can_view_calendar' => ['Calendrier', 'Voir la vue mensuelle de planification.'],
                                        'can_view_template_previews' => ['Aperçu Templates', 'Tester les designs graphiques.'],
                                        'can_view_statistics' => ['Statistiques', 'Accès au dashboard Umami.'],
                                        'can_customize_template' => ['Personnalisation Gabarit', 'Auto-analyse ou édition manuelle.'],
                                    ];
                                @endphp

                                @foreach($toggles as $field => $data)
                                    <div class="flex items-center justify-between group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 flex items-center justify-center group-hover:bg-midnight group-hover:text-white transition-all">
                                                @if($field == 'can_generate_posts') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                                @elseif($field == 'can_connect_social') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                                @elseif($field == 'has_premium_templates') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" /></svg>
                                                @elseif($field == 'can_view_calendar') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                @elseif($field == 'can_view_template_previews') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                @elseif($field == 'can_view_statistics') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                                @elseif($field == 'can_customize_template') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-midnight">{{ $data[0] }}</p>
                                                <p class="text-[10px] text-gray-400 font-medium">{{ $data[1] }}</p>
                                            </div>
                                        </div>
                                        <button 
                                            wire:click="togglePermission('{{ $key }}', '{{ $field }}')"
                                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $perms[$field] ? 'bg-copper' : 'bg-gray-200' }}"
                                        >
                                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $perms[$field] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
