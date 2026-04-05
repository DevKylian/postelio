<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between py-2">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="p-2 bg-copper/10 rounded-xl">
                        <svg class="w-6 h-6 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h1 class="font-bold text-2xl lg:text-3xl text-midnight tracking-tight">
                        Générateur Instagram
                    </h1>
                </div>
                <p class="text-gray-500 text-sm font-medium">
                    Créez des visuels marketing <span class="text-copper">impactants</span> pour vos clients
                </p>
            </div>
            @if($hasContent)
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <button
                    wire:click="downloadPng"
                    wire:loading.attr="disabled"
                    wire:target="downloadPng"
                    class="group relative inline-flex items-center gap-2 bg-midnight text-white px-7 py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-all shadow-lg hover:shadow-midnight/20 active:scale-95 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="downloadPng" class="flex items-center gap-2">
                        <svg class="w-4 h-4 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Télécharger PNG
                    </span>
                    <span wire:loading.flex wire:target="downloadPng" class="hidden items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Exportation…
                    </span>
                </button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-10 px-4 sm:px-6 lg:px-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-[1600px] mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

                {{-- ══ LEFT SIDEBAR: Template Picker ═══════════════════════════════════════ --}}
                <div class="lg:col-span-3">
                    <div class="sticky top-6 space-y-6">
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                                <h3 class="font-bold text-sm text-midnight flex items-center gap-2">
                                    <svg class="w-4 h-4 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                                    Templates
                                </h3>
                            </div>
                            
                            <div class="p-4 space-y-6">
                                {{-- TOFU Section --}}
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-2">
                                        Notoriété (TOFU)
                                    </h4>
                                    <div class="grid grid-cols-1 gap-1.5">
                                        @foreach([
                                            ['stats',    '📊', 'Statistique'],
                                            ['question', '❓', 'Question de fond'],
                                        ] as [$val, $icon, $label])
                                        <button 
                                            wire:click="$set('template', '{{ $val }}')"
                                            class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $template === $val ? 'bg-copper text-midnight shadow-md shadow-copper/10' : 'text-gray-600 hover:bg-gray-50' }}"
                                        >
                                            <span class="text-lg transition-transform group-hover:scale-110">{{ $icon }}</span>
                                            {{ $label }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- MOFU Section --}}
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-2">
                                        Considération (MOFU)
                                    </h4>
                                    <div class="grid grid-cols-1 gap-1.5">
                                        @foreach([
                                            ['conseil',    '🎯', 'Conseil'],
                                            ['avantages',  '✓', 'Points clés'],
                                            ['temoignage', '⭐', 'Témoignage'],
                                            ['annonce',    '📣', 'Nouveauté'],
                                            ['educatif',   '🎓', 'Éducatif'],
                                            ['hero',       '💎', 'Branding'],
                                        ] as [$val, $icon, $label])
                                        <button 
                                            wire:click="$set('template', '{{ $val }}')"
                                            class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $template === $val ? 'bg-copper text-midnight shadow-md shadow-copper/10' : 'text-gray-600 hover:bg-gray-50' }}"
                                        >
                                            <span class="text-lg transition-transform group-hover:scale-110">{{ $icon }}</span>
                                            {{ $label }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- BOFU Section --}}
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-2">
                                        Conversion (BOFU)
                                    </h4>
                                    <div class="grid grid-cols-1 gap-1.5">
                                        @foreach([
                                            ['comparaison', '⚖️', 'Comparaison'],
                                            ['cta',         '🔥', 'Appel à l\'action'],
                                        ] as [$val, $icon, $label])
                                        <button 
                                            wire:click="$set('template', '{{ $val }}')"
                                            class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $template === $val ? 'bg-copper text-midnight shadow-md shadow-copper/10' : 'text-gray-600 hover:bg-gray-50' }}"
                                        >
                                            <span class="text-lg transition-transform group-hover:scale-110">{{ $icon }}</span>
                                            {{ $label }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Blog Section --}}
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-2">
                                        Blog
                                    </h4>
                                    <div class="grid grid-cols-1 gap-1.5">
                                        <button
                                            wire:click="$set('template', 'article-blog')"
                                            class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $template === 'article-blog' ? 'bg-copper text-midnight shadow-md shadow-copper/10' : 'text-gray-600 hover:bg-gray-50' }}"
                                        >
                                            <span class="text-lg transition-transform group-hover:scale-110">&#x1F4F0;</span>
                                            Article de blog
                                        </button>
                                    </div>
                                </div>

                                {{-- Visual Section --}}
                                <div>
                                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-3 px-2">
                                        Visuels & Screens
                                    </h4>
                                    <div class="grid grid-cols-1 gap-1.5">
                                        @foreach([
                                            ['avantapres', '↔️', 'Avant / Après'],
                                            ['appmockup',  '💻', 'Interface App'],
                                        ] as [$val, $icon, $label])
                                        <button 
                                            wire:click="$set('template', '{{ $val }}')"
                                            class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $template === $val ? 'bg-copper text-midnight shadow-md shadow-copper/10' : 'text-gray-600 hover:bg-gray-50' }}"
                                        >
                                            <span class="text-lg transition-transform group-hover:scale-110">{{ $icon }}</span>
                                            {{ $label }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tips Card --}}
                        <div class="bg-midnight rounded-2xl p-6 text-white shadow-xl shadow-midnight/10 relative overflow-hidden group">
                            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                            <h4 class="text-xs font-bold text-copper uppercase tracking-widest mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-copper"></span>
                                Pro Tips
                            </h4>
                            <ul class="space-y-3">
                                <li class="flex gap-3 items-start">
                                    <svg class="w-4 h-4 text-copper shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-xs text-gray-300 leading-relaxed">Variez les types pour un feed cohérent et dynamique.</span>
                                </li>
                                <li class="flex gap-3 items-start">
                                    <svg class="w-4 h-4 text-copper shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-xs text-gray-300 leading-relaxed">Utilisez le ton "Dynamique" pour booster vos CTA.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- ══ CENTER PANEL: Form ═════════════════════════════════════════════════ --}}
                <div class="lg:col-span-5 space-y-8">
                    
                    {{-- STEP 1: Ideation --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                            <h3 class="font-bold text-sm text-midnight flex items-center gap-2">
                                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-midnight text-white text-[10px]">1</span>
                                Configuration du post
                            </h3>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">IA Engine v4</span>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 px-1">📝 Quel est le sujet ?</label>
                                <textarea
                                    wire:model="topic"
                                    rows="3"
                                    placeholder="Ex: Pourquoi les artisans du bâtiment doivent être sur Instagram en 2024..."
                                    class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm text-midnight placeholder:text-gray-400 focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none p-4"
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 px-1">🎭 Ton de voix</label>
                                    <div class="relative">
                                        <select
                                            wire:model="tone"
                                            class="w-full appearance-none rounded-xl border-gray-100 bg-gray-50/50 text-sm text-midnight focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all p-3.5 pr-10"
                                        >
                                            <option value="professionnel">💼 Professionnel</option>
                                            <option value="accessible">🤝 Accessible</option>
                                            <option value="dynamique">⚡ Dynamique</option>
                                            <option value="inspirant">✨ Inspirant</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex items-end">
                                    <button
                                        wire:click="generate"
                                        wire:loading.attr="disabled"
                                        wire:target="generate"
                                        class="w-full group flex items-center justify-center gap-2 bg-copper text-midnight font-bold py-3.5 rounded-xl hover:bg-amber-500 transition-all shadow-lg shadow-copper/20 active:scale-95 disabled:opacity-50 text-sm"
                                    >
                                        <div wire:loading.remove wire:target="generate" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            Générer le contenu
                                        </div>
                                        <div wire:loading wire:target="generate" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            Magie en cours…
                                        </div>
                                    </button>
                                </div>
                            </div>

                            @if($errorMessage)
                            <div class="flex items-start gap-4 bg-red-50/50 border border-red-100 text-red-700 text-xs rounded-xl p-4 animate-shake">
                                <div class="p-1.5 bg-red-100 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold mb-0.5">Oups ! Quelque chose coince</p>
                                    <p class="opacity-80">{{ $errorMessage }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 2: Refinement --}}
                    @if($hasContent)
                    <div wire:key="step-2-{{ $template }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                            <h3 class="font-bold text-sm text-midnight flex items-center gap-2">
                                <span class="flex items-center justify-center w-5 h-5 rounded-full bg-midnight text-white text-[10px]">2</span>
                                Personnalisation
                            </h3>
                            <div class="flex gap-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500 opacity-50"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500 opacity-25"></div>
                            </div>
                        </div>

                        <div class="p-6">
                            @if($template === 'annonce')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="tag" type="text" maxlength="20" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="headline" type="text" maxlength="55" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sous-titre</label>
                                    <textarea wire:model.live="subheadline" rows="2" maxlength="100" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Texte CTA</label>
                                    <input wire:model.live="cta" type="text" maxlength="25" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'conseil')
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">N°</label>
                                        <input wire:model.live="number" type="text" maxlength="2" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre du conseil</label>
                                    <input wire:model.live="tip_title" type="text" maxlength="40" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Corps</label>
                                    <textarea wire:model.live="tip_body" rows="2" maxlength="150" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Hashtags</label>
                                    <input wire:model.live="hashtags" type="text" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'stats')
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Stat</label>
                                        <input wire:model.live="stat" type="text" maxlength="8" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sujet</label>
                                        <input wire:model.live="stat_label" type="text" maxlength="20" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                                    <textarea wire:model.live="stat_description" rows="2" maxlength="110" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Insight</label>
                                    <input wire:model.live="insight" type="text" maxlength="45" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'cta')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Accroche</label>
                                    <textarea wire:model.live="cta_hook" rows="2" maxlength="65" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                                    <textarea wire:model.live="description" rows="2" maxlength="120" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Bouton</label>
                                    <input wire:model.live="cta" type="text" maxlength="30" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">URL</label>
                                    <input wire:model.live="url" type="text" maxlength="40" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'temoignage')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Témoignage</label>
                                    <textarea wire:model.live="quote" rows="3" maxlength="160" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Auteur</label>
                                        <input wire:model.live="author" type="text" maxlength="20" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Étoiles</label>
                                        <select wire:model.live="stars" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                            @foreach([5,4,3] as $s)
                                            <option value="{{ $s }}">{{ str_repeat('★', $s) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Profession</label>
                                    <input wire:model.live="profession" type="text" maxlength="45" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'avantages')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="avantages_tag" type="text" maxlength="25" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="avantages_headline" type="text" maxlength="45" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description (Optionnelle)</label>
                                    <textarea wire:model.live="avantages_body" rows="2" maxlength="100" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                @foreach($items as $i => $item)
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Point {{ $i + 1 }}</label>
                                    <input wire:model.live="items.{{ $i }}" type="text" maxlength="45" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                @endforeach

                            @elseif($template === 'question')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="question_tag" type="text" maxlength="20" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Question</label>
                                    <textarea wire:model.live="question" rows="2" maxlength="90" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Amorce réponse</label>
                                    <textarea wire:model.live="question_teaser" rows="2" maxlength="110" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>

                            @elseif($template === 'comparaison')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="comparaison_headline" type="text" maxlength="35" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <p class="text-xs font-bold text-red-600 px-1">Sans Postelio</p>
                                        @foreach($col_sans as $i => $item)
                                        <input wire:model.live="col_sans.{{ $i }}" type="text" maxlength="38" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-xs focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                        @endforeach
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-xs font-bold text-copper px-1">Avec Postelio</p>
                                        @foreach($col_avec as $i => $item)
                                        <input wire:model.live="col_avec.{{ $i }}" type="text" maxlength="38" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-xs focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                        @endforeach
                                    </div>
                                </div>

                            @elseif($template === 'avantapres')
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Photo AVANT</label>
                                        <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed rounded-lg cursor-pointer transition-all h-24
                                            {{ $uploadImage1 ? 'border-copper bg-copper/5' : 'border-gray-200 hover:border-copper/50 bg-gray-50' }}">
                                            @if($uploadImage1)
                                                <img src="{{ $uploadImage1->temporaryUrl() }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span class="text-[10px] text-gray-400">Avant</span>
                                            @endif
                                            <input wire:model="uploadImage1" type="file" accept="image/*" class="hidden">
                                        </label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Photo APRÈS</label>
                                        <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed rounded-lg cursor-pointer transition-all h-24
                                            {{ $uploadImage2 ? 'border-copper bg-copper/5' : 'border-gray-200 hover:border-copper/50 bg-gray-50' }}">
                                            @if($uploadImage2)
                                                <img src="{{ $uploadImage2->temporaryUrl() }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span class="text-[10px] text-gray-400">Après</span>
                                            @endif
                                            <input wire:model="uploadImage2" type="file" accept="image/*" class="hidden">
                                        </label>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400">Max 8 Mo • JPG, PNG, WEBP</p>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="avantapres_tag" type="text" maxlength="20" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="avantapres_headline" type="text" maxlength="45" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'appmockup')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Screenshot Postelio</label>
                                    <p class="text-[10px] text-gray-400 mb-2">Interface de l'app ou exemple de post</p>
                                    <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed rounded-lg cursor-pointer transition-all h-32
                                        {{ $uploadImage1 ? 'border-copper bg-copper/5' : 'border-gray-200 hover:border-copper/50 bg-gray-50' }}">
                                        @if($uploadImage1)
                                            <img src="{{ $uploadImage1->temporaryUrl() }}" class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <span class="text-xs text-gray-400">Uploader un screenshot</span>
                                        @endif
                                        <input wire:model="uploadImage1" type="file" accept="image/*" class="hidden">
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="appmockup_tag" type="text" maxlength="18" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="appmockup_headline" type="text" maxlength="40" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sous-titre</label>
                                    <textarea wire:model.live="appmockup_subheadline" rows="2" maxlength="95" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>

                            @elseif($template === 'educatif')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="educatif_tag" type="text" maxlength="20" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="educatif_title" type="text" maxlength="45" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Contenu</label>
                                    <textarea wire:model.live="educatif_body" rows="3" maxlength="160" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Point Expert (Insight)</label>
                                    <input wire:model.live="educatif_insight" type="text" maxlength="60" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                            @elseif($template === 'hero')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="hero_tag" type="text" maxlength="20" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <textarea wire:model.live="hero_headline" rows="2" maxlength="110" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Message</label>
                                    <textarea wire:model.live="hero_body" rows="3" maxlength="160" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>

                            @elseif($template === 'article-blog')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="article_blog_tag" type="text" maxlength="25" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre de l'article</label>
                                    <textarea wire:model.live="article_blog_title" rows="2" maxlength="100" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Extrait / Accroche</label>
                                    <textarea wire:model.live="article_blog_excerpt" rows="3" maxlength="160" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">CTA</label>
                                        <input wire:model.live="article_blog_cta" type="text" maxlength="25" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">URL</label>
                                        <input wire:model.live="article_blog_url" type="text" maxlength="40" class="w-full rounded-xl border-gray-100 bg-gray-50/50 text-sm focus:ring-4 focus:ring-copper/10 focus:border-copper transition-all">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif


                    {{-- History --}}
                    @if(count($history) > 0)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-bold text-sm text-midnight flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Historique récent
                            </h3>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($history as $i => $item)
                                <button
                                    wire:click="restoreHistory({{ $i }})"
                                    class="w-full flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all text-left group"
                                >
                                    <div class="shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-lg
                                        {{ $item['template'] === 'annonce' ? 'bg-midnight/5 text-midnight' : '' }}
                                        {{ $item['template'] === 'conseil' ? 'bg-amber-50 text-amber-600' : '' }}
                                        {{ $item['template'] === 'stats' ? 'bg-blue-50 text-blue-600' : '' }}
                                        {{ $item['template'] === 'cta' ? 'bg-copper/10 text-copper' : '' }}
                                        {{ !in_array($item['template'], ['annonce', 'conseil', 'stats', 'cta']) ? 'bg-gray-100 text-gray-500' : '' }}
                                    ">
                                        {{ match($item['template']) {
                                            'annonce' => '📣',
                                            'conseil' => '🎯',
                                            'stats' => '📊',
                                            'cta' => '🔥',
                                            'temoignage' => '⭐',
                                            'avantages' => '✓',
                                            'question' => '❓',
                                            'comparaison' => '⚖️',
                                            'avantapres' => '↔️',
                                            'appmockup' => '💻',
                                            'educatif' => '🎓',
                                            'hero' => '💎',
                                            default => '📄'
                                        } }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-0.5">
                                            <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400">{{ $item['template'] }}</span>
                                            <span class="text-[9px] text-gray-400 font-bold px-1.5 py-0.5 bg-gray-50 rounded">{{ $item['date'] }} · {{ $item['time'] }}</span>
                                        </div>
                                        <p class="text-xs font-semibold text-midnight truncate group-hover:text-copper transition-colors">{{ $item['topic'] }}</p>
                                    </div>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- ══ RIGHT PANEL: Preview ═══════════════════════════════════════════════ --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-6 space-y-6">
                        {{-- Live Preview --}}
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
                            <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                                <h3 class="font-bold text-sm text-midnight flex items-center gap-2">
                                    <svg class="w-4 h-4 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Aperçu en direct
                                </h3>
                                <div class="flex items-center gap-2 px-2.5 py-1 bg-copper/10 rounded-full">
                                    <div class="w-1.5 h-1.5 rounded-full bg-copper animate-pulse"></div>
                                    <span class="text-[10px] font-bold text-copper uppercase tracking-wider">Live</span>
                                </div>
                            </div>

                            <div class="p-8 flex flex-col items-center justify-center bg-gray-50/30">
                                {{-- Preview frame with shadow and glass effect --}}
                                <div class="relative group">
                                    {{-- Background glow --}}
                                    <div class="absolute -inset-4 bg-gradient-to-tr from-copper/20 to-midnight/5 rounded-[2rem] blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                                    
                                    <div class="relative z-10 p-4 bg-white rounded-[2rem] shadow-2xl border border-gray-100">
                                        <div class="relative" style="width: 280px; height: 350px;">
                                            <div style="width: 360px; height: 450px; transform: scale(0.777); transform-origin: top left; border-radius: 12px; overflow: hidden; background: white;">

                                                @if(!$hasContent)
                                                    <div style="width: 360px; height: 360px; background: #F9FAFB; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; border: 2px dashed #E5E7EB; border-radius: 12px;">
                                                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-300">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        </div>
                                                        <p style="color: #9CA3AF; font-size: 11px; font-weight: 700; text-transform: uppercase; tracking-wider;">En attente de génération</p>
                                                    </div>

                                                @elseif($template === 'annonce')
                                                    <x-postelio-instagram.annonce :tag="$tag" :headline="$headline" :subheadline="$subheadline" :cta="$cta" />
                                                @elseif($template === 'conseil')
                                                    <x-postelio-instagram.conseil :number="$number" :tip_title="$tip_title" :tip_body="$tip_body" :hashtags="$hashtags" :show_swipe="$show_swipe" />
                                                @elseif($template === 'stats')
                                                    <x-postelio-instagram.stats :stat="$stat" :stat_label="$stat_label" :stat_description="$stat_description" :insight="$insight" />
                                                @elseif($template === 'cta')
                                                    <x-postelio-instagram.cta :hook="$cta_hook" :description="$description" :cta="$cta" :url="$url" />
                                                @elseif($template === 'temoignage')
                                                    <x-postelio-instagram.temoignage :quote="$quote" :author="$author" :profession="$profession" :stars="$stars" />
                                                @elseif($template === 'avantages')
                                                    <x-postelio-instagram.avantages :headline="$avantages_headline" :tag="$avantages_tag" :items="$items" :body="$avantages_body" />
                                                @elseif($template === 'question')
                                                    <x-postelio-instagram.question :question="$question" :teaser="$question_teaser" :tag="$question_tag" />
                                                @elseif($template === 'comparaison')
                                                    <x-postelio-instagram.comparaison :headline="$comparaison_headline" :col_sans="$col_sans" :col_avec="$col_avec" />
                                                @elseif($template === 'avantapres')
                                                    <x-postelio-instagram.avantapres :image_before_url="$uploadImage1 ? $uploadImage1->temporaryUrl() : null" :image_after_url="$uploadImage2 ? $uploadImage2->temporaryUrl() : null" :headline="$avantapres_headline" :tag="$avantapres_tag" />
                                                @elseif($template === 'appmockup')
                                                    <x-postelio-instagram.appmockup :image_url="$uploadImage1 ? $uploadImage1->temporaryUrl() : null" :tag="$appmockup_tag" :headline="$appmockup_headline" :subheadline="$appmockup_subheadline" />
                                                @elseif($template === 'educatif')
                                                    <x-postelio-instagram.educatif :tag="$educatif_tag" :title="$educatif_title" :body="$educatif_body" :insight="$educatif_insight" />
                                                @elseif($template === 'hero')
                                                    <x-postelio-instagram.hero :tag="$hero_tag" :headline="$hero_headline" :body="$hero_body" />
                                                @elseif($template === 'article-blog')
                                                    <x-postelio-instagram.article-blog :tag="$article_blog_tag" :title="$article_blog_title" :excerpt="$article_blog_excerpt" :cta="$article_blog_cta" :url="$article_blog_url" />
                                                @elseif($template === 'chantier')
                                                    <x-postelio-instagram.chantier 
                                                        :tag="$photo_tag" 
                                                        :headline="$photo_headline" 
                                                        :caption="$photo_caption" 
                                                        :image_url="$uploadImage1 ? $uploadImage1->temporaryUrl() : null" 
                                                    />
                                                @endif

                                            </div>

                                            {{-- Carousel Navigation Overlay --}}
                                            @if(count($slides) > 1)
                                            <div class="absolute inset-0 flex items-center justify-between px-2 pointer-events-none">
                                                <button 
                                                    wire:click.stop="prevSlide" 
                                                    class="pointer-events-auto p-2 rounded-full bg-white/90 shadow-lg text-midnight hover:bg-white transition-all active:scale-95 disabled:opacity-30"
                                                    {{ $currentSlideIndex === 0 ? 'disabled' : '' }}
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                </button>
                                                <button 
                                                    wire:click.stop="nextSlide" 
                                                    class="pointer-events-auto p-2 rounded-full bg-white/90 shadow-lg text-midnight hover:bg-white transition-all active:scale-95 disabled:opacity-30"
                                                    {{ $currentSlideIndex === count($slides) - 1 ? 'disabled' : '' }}
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </button>
                                            </div>

                                            <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-1.5 pointer-events-none">
                                                @foreach($slides as $index => $s)
                                                <div class="w-1.5 h-1.5 rounded-full {{ $index === $currentSlideIndex ? 'bg-copper scale-125' : 'bg-gray-300' }} transition-all"></div>
                                                @endforeach
                                            </div>

                                            <div class="absolute top-6 right-6 bg-midnight/80 backdrop-blur-sm text-white text-[9px] font-bold px-2.5 py-1 rounded-full shadow-lg">
                                                {{ $currentSlideIndex + 1 }} / {{ count($slides) }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($hasContent)
                                <div class="w-full mt-10 space-y-6">
                                    {{-- Caption Section --}}
                                    <div wire:key="caption-right-{{ $template }}" class="bg-white rounded-2xl border border-gray-200 shadow-xl shadow-midnight/5 overflow-hidden animate-in fade-in zoom-in-95 duration-500">
                                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                                <span class="text-[10px] font-bold text-midnight uppercase tracking-widest">Légende Instagram</span>
                                            </div>
                                            <button
                                                x-data="{ 
                                                    copied: false,
                                                    copy() {
                                                        navigator.clipboard.writeText($refs.captionTextRight.value);
                                                        this.copied = true;
                                                        setTimeout(() => this.copied = false, 2000);
                                                    }
                                                }"
                                                x-on:click="copy()"
                                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-midnight text-white text-[10px] font-bold hover:bg-gray-800 transition-all active:scale-95 shadow-sm shadow-midnight/10 disabled:opacity-50"
                                                @if(!$caption) disabled @endif
                                            >
                                                <span x-show="!copied">Copier</span>
                                                <span x-show="copied" x-cloak class="text-green-400">Copié !</span>
                                            </button>
                                        </div>
                                        <div class="p-5">
                                            <textarea
                                                x-ref="captionTextRight"
                                                wire:model.live="caption"
                                                rows="5"
                                                class="w-full bg-transparent border-none p-0 text-[13px] text-midnight focus:ring-0 resize-none font-medium leading-relaxed"
                                                placeholder="La légende optimisée par l'IA apparaîtra ici..."
                                            ></textarea>
                                            <div class="flex items-center justify-between mt-4 border-t border-gray-50 pt-3">
                                                <span class="text-[9px] font-bold text-gray-300 uppercase tracking-tighter">Optimisé par Postelio AI</span>
                                                <div class="text-[9px] text-gray-400 font-bold bg-gray-50 px-2 py-0.5 rounded-full">{{ strlen($caption) }} CAR.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <button
                                            wire:click="saveToHistory"
                                            wire:loading.attr="disabled"
                                            class="w-full flex items-center justify-center gap-3 bg-white border-2 border-midnight text-midnight font-bold py-3.5 rounded-xl hover:bg-gray-50 transition-all active:scale-95 disabled:opacity-50 group"
                                        >
                                            <span wire:loading.remove wire:target="saveToHistory" class="flex items-center gap-3">
                                                <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Valider & Historiser
                                            </span>
                                            <span wire:loading wire:target="saveToHistory" class="flex items-center gap-3">
                                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                Enregistrement...
                                            </span>
                                        </button>

                                        <div x-data="{ show: false }" x-on:post-saved.window="show = true; setTimeout(() => show = false, 3000)" x-show="show" x-transition x-cloak class="flex items-center justify-center gap-2 text-green-600 font-bold text-[10px] uppercase tracking-widest animate-bounce">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Post sauvegardé dans l'historique !
                                        </div>

                                        <button
                                            wire:click="downloadPng"
                                            wire:loading.attr="disabled"
                                            wire:target="downloadPng"
                                            class="w-full relative flex items-center justify-center gap-3 bg-midnight text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition-all shadow-xl shadow-midnight/10 active:scale-95 disabled:opacity-50 group"
                                        >
                                            <span wire:loading.remove wire:target="downloadPng" class="flex items-center gap-3">
                                                <svg class="w-5 h-5 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Télécharger en HD (PNG)
                                            </span>
                                            <div wire:loading wire:target="downloadPng" class="flex items-center gap-3">
                                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                Exportation haute qualité…
                                            </div>
                                        </button>
                                        <p class="text-[10px] text-center text-gray-400 font-medium">Format optimal : 1080 × 1080 px (Carré Instagram)</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }
        .animate-shake {
            animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
        }
    </style>
</div>
