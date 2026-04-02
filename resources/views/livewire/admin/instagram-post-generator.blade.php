<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-bold text-xl lg:text-2xl text-midnight tracking-tight leading-tight">
                    Générateur Instagram Postelio
                </h1>
                <p class="text-gray-400 text-sm mt-0.5">
                    Crée tes posts Instagram marketing pour convaincre les artisans.
                </p>
            </div>
            @if($hasContent)
            <button
                wire:click="downloadPng"
                wire:loading.attr="disabled"
                wire:target="downloadPng"
                class="inline-flex items-center gap-2 bg-midnight text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:bg-gray-800 transition-all shadow-sm disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="downloadPng">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </span>
                <span wire:loading.flex wire:target="downloadPng" class="hidden items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>
                <span wire:loading.remove wire:target="downloadPng">Télécharger PNG</span>
                <span wire:loading wire:target="downloadPng">Rendu en cours…</span>
            </button>
            @endif
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                {{-- ══ LEFT PANEL: Form ══════════════════════════════════════════════════ --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Template selector --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h3 class="font-bold text-sm text-midnight mb-4 uppercase tracking-widest">Type de post</h3>
                        {{-- TOFU --}}
                        <p class="col-span-2 text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">TOFU — Notoriété</p>
                        @foreach([
                            ['stats',    'Statistique', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                            ['question', 'Question',    'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ] as [$val, $label, $icon])
                        <button wire:click="$set('template', '{{ $val }}')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all {{ $template === $val ? 'border-copper bg-copper/5 text-copper' : 'border-gray-100 text-gray-400 hover:border-gray-200 hover:text-gray-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/></svg>
                            <span class="text-xs font-bold">{{ $label }}</span>
                        </button>
                        @endforeach

                        {{-- MOFU --}}
                        <p class="col-span-2 text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-2">MOFU — Considération</p>
                        @foreach([
                            ['conseil',    'Conseil',      'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                            ['avantages',  'Avantages',    'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                            ['temoignage', 'Témoignage',   'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                            ['annonce',    'Annonce',      'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.952 9.168-5 .303.477.424 1.053.424 1.636 0 4.332-2.66 7.364-9 8.364z'],
                        ] as [$val, $label, $icon])
                        <button wire:click="$set('template', '{{ $val }}')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all {{ $template === $val ? 'border-copper bg-copper/5 text-copper' : 'border-gray-100 text-gray-400 hover:border-gray-200 hover:text-gray-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/></svg>
                            <span class="text-xs font-bold">{{ $label }}</span>
                        </button>
                        @endforeach

                        {{-- BOFU --}}
                        <p class="col-span-2 text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-2">BOFU — Conversion</p>
                        @foreach([
                            ['comparaison', 'Comparaison', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                            ['cta',         'Appel CTA',   'M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122'],
                        ] as [$val, $label, $icon])
                        <button wire:click="$set('template', '{{ $val }}')" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all {{ $template === $val ? 'border-copper bg-copper/5 text-copper' : 'border-gray-100 text-gray-400 hover:border-gray-200 hover:text-gray-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/></svg>
                            <span class="text-xs font-bold">{{ $label }}</span>
                        </button>
                        @endforeach
                    </div>

                    {{-- Topic + Tone --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h3 class="font-bold text-sm text-midnight uppercase tracking-widest">Contenu</h3>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Sujet du post</label>
                            <textarea
                                wire:model="topic"
                                rows="4"
                                placeholder="Ex: Les plombiers qui publient sur Instagram gagnent plus de clients…"
                                class="w-full rounded-xl border-gray-200 text-sm text-midnight focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Ton</label>
                            <select
                                wire:model="tone"
                                class="w-full rounded-xl border-gray-200 text-sm text-midnight focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all"
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
                            class="w-full flex items-center justify-center gap-2.5 bg-copper text-midnight font-bold py-3 rounded-xl hover:bg-amber-500 transition-all shadow-sm disabled:opacity-60 text-sm"
                        >
                            <span wire:loading.remove wire:target="generate">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </span>
                            <svg wire:loading wire:target="generate" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span wire:loading.remove wire:target="generate">Générer le contenu</span>
                            <span wire:loading wire:target="generate">Génération en cours…</span>
                        </button>

                        @if($errorMessage)
                        <div class="flex items-start gap-3 bg-red-50 border border-red-100 text-red-700 text-xs rounded-xl p-3">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $errorMessage }}
                        </div>
                        @endif
                    </div>

                    {{-- Editable fields --}}
                    @if($hasContent)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <h3 class="font-bold text-sm text-midnight uppercase tracking-widest">Édition</h3>

                        @if($template === 'annonce')
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Badge / Tag</label>
                                <input wire:model.live="tag" type="text" maxlength="20" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Titre principal</label>
                                <input wire:model.live="headline" type="text" maxlength="55" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Sous-titre</label>
                                <textarea wire:model.live="subheadline" rows="2" maxlength="100" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Texte CTA</label>
                                <input wire:model.live="cta" type="text" maxlength="25" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>

                        @elseif($template === 'conseil')
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Numéro</label>
                                <input wire:model.live="number" type="text" maxlength="2" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Titre du conseil</label>
                                <input wire:model.live="tip_title" type="text" maxlength="40" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Corps du conseil</label>
                                <textarea wire:model.live="tip_body" rows="3" maxlength="150" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Hashtags</label>
                                <input wire:model.live="hashtags" type="text" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>

                        @elseif($template === 'stats')
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Statistique</label>
                                <input wire:model.live="stat" type="text" maxlength="8" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Sujet</label>
                                <input wire:model.live="stat_label" type="text" maxlength="20" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Description</label>
                                <textarea wire:model.live="stat_description" rows="2" maxlength="110" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Conclusion accrocheuse</label>
                                <input wire:model.live="insight" type="text" maxlength="45" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>

                        @elseif($template === 'cta')
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Accroche</label>
                                <textarea wire:model.live="hook" rows="2" maxlength="65" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Description</label>
                                <textarea wire:model.live="description" rows="2" maxlength="120" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Texte bouton</label>
                                <input wire:model.live="cta" type="text" maxlength="30" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">URL</label>
                                <input wire:model.live="url" type="text" maxlength="40" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>

                        @elseif($template === 'temoignage')
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Témoignage</label>
                                <textarea wire:model.live="quote" rows="4" maxlength="160" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Auteur</label>
                                    <input wire:model.live="author" type="text" maxlength="20" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Étoiles</label>
                                    <select wire:model.live="stars" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                        @foreach([5,4,3] as $s)
                                        <option value="{{ $s }}">{{ str_repeat('★', $s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Profession</label>
                                <input wire:model.live="profession" type="text" maxlength="45" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>

                        @elseif($template === 'avantages')
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Badge</label>
                                <input wire:model.live="avantages_tag" type="text" maxlength="25" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Titre</label>
                                <input wire:model.live="avantages_headline" type="text" maxlength="45" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            @foreach($items as $i => $item)
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Point {{ $i + 1 }}</label>
                                <input wire:model.live="items.{{ $i }}" type="text" maxlength="45" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            @endforeach

                        @elseif($template === 'question')
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Badge</label>
                                <input wire:model.live="question_tag" type="text" maxlength="20" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Question</label>
                                <textarea wire:model.live="question" rows="3" maxlength="90" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Amorce réponse</label>
                                <textarea wire:model.live="question_teaser" rows="2" maxlength="110" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all resize-none"></textarea>
                            </div>

                        @elseif($template === 'comparaison')
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Titre</label>
                                <input wire:model.live="comparaison_headline" type="text" maxlength="35" class="w-full rounded-xl border-gray-200 text-sm focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <p class="text-xs font-bold text-red-500">Sans Postelio</p>
                                    @foreach($col_sans as $i => $item)
                                    <input wire:model.live="col_sans.{{ $i }}" type="text" maxlength="38" class="w-full rounded-xl border-gray-200 text-xs focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                    @endforeach
                                </div>
                                <div class="space-y-2">
                                    <p class="text-xs font-bold text-copper">Avec Postelio</p>
                                    @foreach($col_avec as $i => $item)
                                    <input wire:model.live="col_avec.{{ $i }}" type="text" maxlength="38" class="w-full rounded-xl border-gray-200 text-xs focus:ring-2 focus:ring-copper/30 focus:border-copper transition-all">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif

                    {{-- History --}}
                    @if(count($history) > 0)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h3 class="font-bold text-sm text-midnight uppercase tracking-widest mb-4">Historique</h3>
                        <div class="space-y-2">
                            @foreach($history as $i => $item)
                            <button
                                wire:click="restoreHistory({{ $i }})"
                                class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 border border-gray-100 transition-all text-left group"
                            >
                                <div class="shrink-0">
                                    @php $colors = ['annonce'=>'bg-midnight','conseil'=>'bg-amber-100 text-amber-700','stats'=>'bg-blue-50 text-blue-700','cta'=>'bg-copper']; @endphp
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase
                                        {{ $item['template'] === 'annonce' ? 'bg-midnight text-white' : '' }}
                                        {{ $item['template'] === 'conseil' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $item['template'] === 'stats' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $item['template'] === 'cta' ? 'bg-copper text-midnight' : '' }}
                                    ">{{ $item['template'] }}</span>
                                </div>
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
                <div class="lg:col-span-3 space-y-6">

                    {{-- Live Preview --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-sm text-midnight uppercase tracking-widest">Aperçu live</h3>
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-copper animate-pulse"></div>
                                <span class="text-xs text-gray-400 font-medium">Mise à jour automatique</span>
                            </div>
                        </div>

                        {{-- Preview wrapper: 360×360 scaled to fit --}}
                        <div class="flex justify-center">
                            <div class="relative" style="width: 324px; height: 324px;">
                                <div style="width: 360px; height: 360px; transform: scale(0.9); transform-origin: top left; position: absolute; top: 0; left: 0; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">

                                    @if(!$hasContent)
                                        {{-- Empty state --}}
                                        <div style="width: 360px; height: 360px; background: #F9FAFB; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; border: 2px dashed #E5E7EB;">
                                            <svg style="width: 48px; height: 48px; color: #D1D5DB;" fill="none" stroke="#D1D5DB" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p style="color: #9CA3AF; font-size: 13px; font-weight: 500; text-align: center; max-width: 200px;">Remplis le formulaire et génère ton post</p>
                                        </div>

                                    @elseif($template === 'annonce')
                                        <x-postelio-instagram.annonce
                                            :tag="$tag"
                                            :headline="$headline"
                                            :subheadline="$subheadline"
                                            :cta="$cta"
                                        />

                                    @elseif($template === 'conseil')
                                        <x-postelio-instagram.conseil
                                            :number="$number"
                                            :tip_title="$tip_title"
                                            :tip_body="$tip_body"
                                            :hashtags="$hashtags"
                                        />

                                    @elseif($template === 'stats')
                                        <x-postelio-instagram.stats
                                            :stat="$stat"
                                            :stat_label="$stat_label"
                                            :stat_description="$stat_description"
                                            :insight="$insight"
                                        />

                                    @elseif($template === 'cta')
                                        <x-postelio-instagram.cta
                                            :hook="$hook"
                                            :description="$description"
                                            :cta="$cta"
                                            :url="$url"
                                        />

                                    @elseif($template === 'temoignage')
                                        <x-postelio-instagram.temoignage
                                            :quote="$quote"
                                            :author="$author"
                                            :profession="$profession"
                                            :stars="$stars"
                                        />

                                    @elseif($template === 'avantages')
                                        <x-postelio-instagram.avantages
                                            :headline="$avantages_headline"
                                            :tag="$avantages_tag"
                                            :items="$items"
                                        />

                                    @elseif($template === 'question')
                                        <x-postelio-instagram.question
                                            :question="$question"
                                            :teaser="$question_teaser"
                                            :tag="$question_tag"
                                        />

                                    @elseif($template === 'comparaison')
                                        <x-postelio-instagram.comparaison
                                            :headline="$comparaison_headline"
                                            :col_sans="$col_sans"
                                            :col_avec="$col_avec"
                                        />
                                    @endif

                                </div>
                            </div>
                        </div>

                        @if($hasContent)
                        <div class="flex justify-center mt-6">
                            <button
                                wire:click="downloadPng"
                                wire:loading.attr="disabled"
                                wire:target="downloadPng"
                                class="inline-flex items-center gap-2 bg-midnight text-white px-8 py-3 rounded-xl font-semibold text-sm hover:bg-gray-800 transition-all shadow-sm disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="downloadPng">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </span>
                                <svg wire:loading wire:target="downloadPng" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span wire:loading.remove wire:target="downloadPng">Télécharger en PNG (1080×1080)</span>
                                <span wire:loading wire:target="downloadPng">Rendu Puppeteer…</span>
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Template guide --}}
                    <div class="grid grid-cols-2 gap-4">

                        @foreach([
                            ['stats',       'Statistique',  'Chiffre choc sur fond sombre. Idéal pour créer de la prise de conscience. TOFU.',          'bg-midnight', 'text-copper'],
                            ['question',    'Question',     'Question directe pour déclencher l\'engagement et les commentaires. TOFU.',                 'bg-midnight', 'text-amber-400'],
                            ['conseil',     'Conseil',      'Tip concret pour artisans. Fond blanc épuré avec numérotation cuivrée. MOFU.',              'bg-chalk border border-gray-200', 'text-midnight'],
                            ['avantages',   'Avantages',    'Liste des 4 bénéfices clés de Postelio. Fond blanc, checkmarks cuivrés. MOFU.',            'bg-chalk border border-gray-200', 'text-midnight'],
                            ['temoignage',  'Témoignage',   'Avis client authentique pour rassurer. Fond sombre avec citation. MOFU.',                  'bg-midnight', 'text-copper'],
                            ['annonce',     'Annonce',      'Présente une feature ou actualité Postelio. Fond dark avec glow cuivré. MOFU.',             'bg-midnight', 'text-copper'],
                            ['comparaison', 'Comparaison',  'Sans vs Avec Postelio. Design split pour forcer la décision. BOFU.',                       'bg-midnight', 'text-amber-400'],
                            ['cta',         'Appel CTA',    'Converti avec un message direct. Dégradé cuivré avec bouton sombre. BOFU.',                'bg-amber-500', 'text-midnight'],
                        ] as [$key, $name, $desc, $bg, $text])
                        <div
                            wire:click="$set('template', '{{ $key }}')"
                            class="rounded-2xl border p-4 cursor-pointer transition-all {{ $template === $key ? 'border-copper ring-2 ring-copper/20' : 'border-gray-100 hover:border-gray-200' }}"
                        >
                            <div class="w-full h-12 rounded-xl mb-3 overflow-hidden flex items-center justify-center shadow-inner {{ $bg }}">
                                <span class="{{ $text }} text-[10px] font-black tracking-widest uppercase">{{ $name }}</span>
                            </div>
                            <p class="text-xs font-bold text-midnight mb-1">{{ $name }}</p>
                            <p class="text-[11px] text-gray-400 leading-relaxed">{{ $desc }}</p>
                        </div>
                        @endforeach

                    </div>

                    {{-- Tips --}}
                    <div class="bg-copper/5 border border-copper/20 rounded-2xl p-5">
                        <h4 class="text-xs font-bold text-copper uppercase tracking-widest mb-3">Conseils de contenu</h4>
                        <ul class="space-y-2">
                            @foreach([
                                'Cible les douleurs concrètes : manque de temps, clients insuffisants, compétition.',
                                'Utilise des chiffres réels ou crédibles pour les posts "Stats".',
                                'Le ton "Dynamique" fonctionne bien pour les CTA de conversion.',
                                'Varie les types de posts pour un feed cohérent : 40% conseil, 30% annonce, 30% CTA.',
                                'Toutes les images sont exportées en 1080×1080px format Instagram.',
                            ] as $tip)
                            <li class="flex items-start gap-2 text-xs text-gray-600">
                                <div class="w-1 h-1 rounded-full bg-copper mt-1.5 shrink-0"></div>
                                {{ $tip }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
