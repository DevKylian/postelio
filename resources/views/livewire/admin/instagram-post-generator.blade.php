<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight">
                    Générateur Instagram Postelio
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Crée des posts marketing convaincants pour les artisans
                </p>
            </div>
            @if($hasContent)
            <button
                wire:click="downloadPng"
                wire:loading.attr="disabled"
                wire:target="downloadPng"
                class="inline-flex items-center gap-2 bg-midnight text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-gray-800 transition-colors disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="downloadPng">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </span>
                <span wire:loading.flex wire:target="downloadPng" class="hidden">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>
                <span wire:loading.remove wire:target="downloadPng">Télécharger PNG</span>
                <span wire:loading wire:target="downloadPng" class="hidden">Export…</span>
            </button>
            @endif
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

                {{-- ══ LEFT SIDEBAR: Template Picker ═══════════════════════════════════════ --}}
                <div class="lg:col-span-2">
                    <div class="sticky top-6 space-y-4">
                        {{-- TOFU Section --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest px-2 mb-2">
                                📢 Notoriété (TOFU)
                            </h4>
                            <div class="space-y-2">
                                @foreach([
                                    ['stats',    '📊 Statistique'],
                                    ['question', '❓ Question'],
                                ] as [$val, $label])
                                <button 
                                    wire:click="$set('template', '{{ $val }}')"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-all {{ $template === $val ? 'bg-copper text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}"
                                >
                                    {{ $label }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- MOFU Section --}}
                        <div class="pt-2 border-t border-gray-200">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest px-2 mb-2">
                                💡 Considération (MOFU)
                            </h4>
                            <div class="space-y-2">
                                @foreach([
                                    ['conseil',    '🎯 Conseil'],
                                    ['avantages',  '✓ Avantages'],
                                    ['temoignage', '⭐ Témoignage'],
                                    ['annonce',    '📣 Annonce'],
                                ] as [$val, $label])
                                <button 
                                    wire:click="$set('template', '{{ $val }}')"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-all {{ $template === $val ? 'bg-copper text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}"
                                >
                                    {{ $label }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- BOFU Section --}}
                        <div class="pt-2 border-t border-gray-200">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest px-2 mb-2">
                                🎬 Conversion (BOFU)
                            </h4>
                            <div class="space-y-2">
                                @foreach([
                                    ['comparaison', '⚖️ Comparaison'],
                                    ['cta',         '🔥 Appel CTA'],
                                ] as [$val, $label])
                                <button 
                                    wire:click="$set('template', '{{ $val }}')"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-all {{ $template === $val ? 'bg-copper text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}"
                                >
                                    {{ $label }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- With Images Section --}}
                        <div class="pt-2 border-t border-gray-200">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest px-2 mb-2">
                                🖼️ Avec Screenshots
                            </h4>
                            <div class="space-y-2">
                                @foreach([
                                    ['avantapres', '↔️ Avant/Après'],
                                    ['appmockup',  '💻 App Mockup'],
                                ] as [$val, $label])
                                <button 
                                    wire:click="$set('template', '{{ $val }}')"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-all {{ $template === $val ? 'bg-copper text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}"
                                >
                                    {{ $label }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tips Card --}}
                        <div class="pt-4 mt-4 border-t border-gray-200">
                            <div class="bg-copper/5 border border-copper/20 rounded-lg p-3">
                                <h4 class="text-xs font-bold text-copper uppercase tracking-widest mb-2">💪 Pro Tips</h4>
                                <ul class="space-y-1 text-[11px] text-gray-600">
                                    <li class="flex gap-2">
                                        <span class="shrink-0">•</span>
                                        <span>Varie les types pour un feed cohérent</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="shrink-0">•</span>
                                        <span>Export en 1080×1080px</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="shrink-0">•</span>
                                        <span>Tone "Dynamique" pour les CTA</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ CENTER PANEL: Form ═════════════════════════════════════════════════ --}}
                <div class="lg:col-span-3 space-y-5">

                    {{-- Topic & Tone --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <h3 class="font-bold text-sm text-midnight mb-4">Contenu & Ton</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">📝 Sujet du post</label>
                                <textarea
                                    wire:model="topic"
                                    rows="3"
                                    placeholder="Ex: Les plombiers qui publient gagnent plus de clients…"
                                    class="w-full rounded-lg border border-gray-200 text-sm text-midnight focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">🎭 Ton</label>
                                <select
                                    wire:model="tone"
                                    class="w-full rounded-lg border border-gray-200 text-sm text-midnight focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all"
                                >
                                    <option value="professionnel">Professionnel & Crédible</option>
                                    <option value="accessible">Accessible & Chaleureux</option>
                                    <option value="dynamique">Dynamique & Percutant</option>
                                    <option value="inspirant">Inspirant & Ambitieux</option>
                                </select>
                            </div>

                            <button
                                wire:click="generate"
                                wire:loading.attr="disabled"
                                wire:target="generate"
                                class="w-full flex items-center justify-center gap-2 bg-copper text-midnight font-bold py-2.5 rounded-lg hover:bg-amber-500 transition-colors disabled:opacity-50 text-sm"
                            >
                                <span wire:loading.remove wire:target="generate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </span>
                                <svg wire:loading wire:target="generate" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span wire:loading.remove wire:target="generate">Générer le contenu</span>
                                <span wire:loading wire:target="generate">Génération…</span>
                            </button>

                            @if($errorMessage)
                            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg p-3">
                                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $errorMessage }}
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Edit Fields --}}
                    @if($hasContent)
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <h3 class="font-bold text-sm text-midnight mb-4">✏️ Édition</h3>

                        <div class="space-y-4">
                            @if($template === 'annonce')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="tag" type="text" maxlength="20" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="headline" type="text" maxlength="55" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sous-titre</label>
                                    <textarea wire:model.live="subheadline" rows="2" maxlength="100" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Texte CTA</label>
                                    <input wire:model.live="cta" type="text" maxlength="25" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'conseil')
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">N°</label>
                                        <input wire:model.live="number" type="text" maxlength="2" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre du conseil</label>
                                    <input wire:model.live="tip_title" type="text" maxlength="40" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Corps</label>
                                    <textarea wire:model.live="tip_body" rows="2" maxlength="150" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Hashtags</label>
                                    <input wire:model.live="hashtags" type="text" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'stats')
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Stat</label>
                                        <input wire:model.live="stat" type="text" maxlength="8" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sujet</label>
                                        <input wire:model.live="stat_label" type="text" maxlength="20" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                                    <textarea wire:model.live="stat_description" rows="2" maxlength="110" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Insight</label>
                                    <input wire:model.live="insight" type="text" maxlength="45" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'cta')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Accroche</label>
                                    <textarea wire:model.live="hook" rows="2" maxlength="65" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                                    <textarea wire:model.live="description" rows="2" maxlength="120" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Bouton</label>
                                    <input wire:model.live="cta" type="text" maxlength="30" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">URL</label>
                                    <input wire:model.live="url" type="text" maxlength="40" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'temoignage')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Témoignage</label>
                                    <textarea wire:model.live="quote" rows="3" maxlength="160" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Auteur</label>
                                        <input wire:model.live="author" type="text" maxlength="20" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Étoiles</label>
                                        <select wire:model.live="stars" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                            @foreach([5,4,3] as $s)
                                            <option value="{{ $s }}">{{ str_repeat('★', $s) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Profession</label>
                                    <input wire:model.live="profession" type="text" maxlength="45" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>

                            @elseif($template === 'avantages')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="avantages_tag" type="text" maxlength="25" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="avantages_headline" type="text" maxlength="45" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                @foreach($items as $i => $item)
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Point {{ $i + 1 }}</label>
                                    <input wire:model.live="items.{{ $i }}" type="text" maxlength="45" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                @endforeach

                            @elseif($template === 'question')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Badge</label>
                                    <input wire:model.live="question_tag" type="text" maxlength="20" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Question</label>
                                    <textarea wire:model.live="question" rows="2" maxlength="90" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Amorce réponse</label>
                                    <textarea wire:model.live="question_teaser" rows="2" maxlength="110" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>

                            @elseif($template === 'comparaison')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="comparaison_headline" type="text" maxlength="35" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <p class="text-xs font-bold text-red-600">Sans Postelio</p>
                                        @foreach($col_sans as $i => $item)
                                        <input wire:model.live="col_sans.{{ $i }}" type="text" maxlength="38" class="w-full rounded-lg border border-gray-200 text-xs focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                        @endforeach
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-xs font-bold text-copper">Avec Postelio</p>
                                        @foreach($col_avec as $i => $item)
                                        <input wire:model.live="col_avec.{{ $i }}" type="text" maxlength="38" class="w-full rounded-lg border border-gray-200 text-xs focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
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
                                    <input wire:model.live="avantapres_tag" type="text" maxlength="20" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="avantapres_headline" type="text" maxlength="45" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
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
                                    <input wire:model.live="appmockup_tag" type="text" maxlength="18" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Titre</label>
                                    <input wire:model.live="appmockup_headline" type="text" maxlength="40" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sous-titre</label>
                                    <textarea wire:model.live="appmockup_subheadline" rows="2" maxlength="95" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- History --}}
                    @if(count($history) > 0)
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <h3 class="font-bold text-sm text-midnight mb-3">📚 Historique</h3>
                        <div class="space-y-2">
                            @foreach($history as $i => $item)
                            <button
                                wire:click="restoreHistory({{ $i }})"
                                class="w-full flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 border border-gray-100 transition-all text-left group"
                            >
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $item['template'] === 'annonce' ? 'bg-midnight text-white' : '' }}
                                    {{ $item['template'] === 'conseil' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $item['template'] === 'stats' ? 'bg-blue-50 text-blue-700' : '' }}
                                    {{ $item['template'] === 'cta' ? 'bg-copper text-midnight' : '' }}
                                ">{{ $item['template'] }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-midnight truncate group-hover:text-copper transition-colors">{{ $item['topic'] }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $item['time'] }}</p>
                                </div>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                {{-- ══ RIGHT PANEL: Preview ═══════════════════════════════════════════════ --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-6 space-y-4">
                        {{-- Live Preview --}}
                        <div class="bg-white rounded-lg border border-gray-200 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-sm text-midnight">📱 Aperçu</h3>
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 rounded-full bg-copper animate-pulse"></div>
                                    <span class="text-[11px] text-gray-400">Live</span>
                                </div>
                            </div>

                            {{-- Preview frame --}}
                            <div class="flex justify-center">
                                <div class="relative" style="width: 280px; height: 280px;">
                                    <div style="width: 360px; height: 360px; transform: scale(0.777); transform-origin: top left; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.12);">

                                        @if(!$hasContent)
                                            <div style="width: 360px; height: 360px; background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                                <svg style="width: 40px; height: 40px; color: #D1D5DB;" fill="none" stroke="#D1D5DB" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <p style="color: #9CA3AF; font-size: 12px; font-weight: 600; text-align: center;">Remplis et génère</p>
                                            </div>

                                        @elseif($template === 'annonce')
                                            <x-postelio-instagram.annonce :tag="$tag" :headline="$headline" :subheadline="$subheadline" :cta="$cta" />
                                        @elseif($template === 'conseil')
                                            <x-postelio-instagram.conseil :number="$number" :tip_title="$tip_title" :tip_body="$tip_body" :hashtags="$hashtags" />
                                        @elseif($template === 'stats')
                                            <x-postelio-instagram.stats :stat="$stat" :stat_label="$stat_label" :stat_description="$stat_description" :insight="$insight" />
                                        @elseif($template === 'cta')
                                            <x-postelio-instagram.cta :hook="$hook" :description="$description" :cta="$cta" :url="$url" />
                                        @elseif($template === 'temoignage')
                                            <x-postelio-instagram.temoignage :quote="$quote" :author="$author" :profession="$profession" :stars="$stars" />
                                        @elseif($template === 'avantages')
                                            <x-postelio-instagram.avantages :headline="$avantages_headline" :tag="$avantages_tag" :items="$items" />
                                        @elseif($template === 'question')
                                            <x-postelio-instagram.question :question="$question" :teaser="$question_teaser" :tag="$question_tag" />
                                        @elseif($template === 'comparaison')
                                            <x-postelio-instagram.comparaison :headline="$comparaison_headline" :col_sans="$col_sans" :col_avec="$col_avec" />
                                        @elseif($template === 'avantapres')
                                            <x-postelio-instagram.avantapres :image_before_url="$uploadImage1 ? $uploadImage1->temporaryUrl() : null" :image_after_url="$uploadImage2 ? $uploadImage2->temporaryUrl() : null" :headline="$avantapres_headline" :tag="$avantapres_tag" />
                                        @elseif($template === 'appmockup')
                                            <x-postelio-instagram.appmockup :image_url="$uploadImage1 ? $uploadImage1->temporaryUrl() : null" :tag="$appmockup_tag" :headline="$appmockup_headline" :subheadline="$appmockup_subheadline" />
                                        @endif

                                    </div>
                                </div>
                            </div>

                            @if($hasContent)
                            <button
                                wire:click="downloadPng"
                                wire:loading.attr="disabled"
                                wire:target="downloadPng"
                                class="w-full mt-4 flex items-center justify-center gap-2 bg-midnight text-white font-semibold py-2 rounded-lg hover:bg-gray-800 transition-colors disabled:opacity-50 text-sm"
                            >
                                <span wire:loading.remove wire:target="downloadPng">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </span>
                                <svg wire:loading wire:target="downloadPng" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span wire:loading.remove wire:target="downloadPng">PNG 1080×1080</span>
                                <span wire:loading wire:target="downloadPng">Export…</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
