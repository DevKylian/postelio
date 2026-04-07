<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display font-bold text-2xl text-midnight tracking-tight leading-tight">
                    Newsletter
                </h1>
                <p class="text-slate-400 text-sm mt-1 font-medium">
                    Rédigez et envoyez des emails à vos membres.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-gray-600 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    {{-- CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-10">

        {{-- ─── Composer ─── --}}
        <section>
            <div class="glass-panel p-8 sm:p-10 rounded-[2.5rem] border-white/80 shadow-2xl shadow-slate-200/40 relative overflow-hidden bg-white/60"
                 x-data="{
                    content: @entangle('content'),
                    htmlMode: false,
                    editor: null,
                    init() {
                        if (typeof ClassicEditor === 'undefined') return;
                        ClassicEditor
                            .create(this.$refs.editorArea, {
                                toolbar: [
                                    'heading', '|',
                                    'bold', 'italic', 'link',
                                    'bulletedList', 'numberedList',
                                    'blockQuote', 'insertTable',
                                    '|', 'undo', 'redo'
                                ],
                                heading: {
                                    options: [
                                        { model: 'paragraph',  title: 'Paragraphe',         class: 'ck-heading_paragraph' },
                                        { model: 'heading1',   view: 'h2', title: 'Titre (H2)',     class: 'ck-heading_heading1' },
                                        { model: 'heading2',   view: 'h3', title: 'Sous-titre (H3)', class: 'ck-heading_heading2' }
                                    ]
                                }
                            })
                            .then(editor => {
                                this.editor = editor;
                                editor.setData(this.content || '');
                                editor.model.document.on('change:data', () => {
                                    this.content = editor.getData();
                                });
                                this.$watch('content', value => {
                                    if (!this.htmlMode && value !== editor.getData()) {
                                        editor.setData(value || '');
                                    }
                                });
                            })
                            .catch(error => console.error(error));
                    }
                 }">

                {{-- Decoration blob --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-copper/5 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>

                <div class="flex items-center gap-3 mb-8 relative z-10">
                    <div class="w-9 h-9 rounded-xl bg-copper/10 flex items-center justify-center text-copper shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-xl text-midnight">Nouvelle newsletter</h2>
                        <p class="text-xs text-slate-400 font-medium">Rédigez votre email et choisissez votre audience</p>
                    </div>
                </div>

                <div class="space-y-8 relative z-10">

                    {{-- Subject --}}
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Objet de l'email</label>
                        <input
                            type="text"
                            wire:model="subject"
                            placeholder="Ex : Nouveauté Postelio — Découvrez notre dernière fonctionnalité ✨"
                            class="w-full bg-white/50 border-slate-100 rounded-2xl p-4 text-sm font-bold text-midnight focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none"
                        >
                        @error('subject') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Audience selector --}}
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest px-1">Audience</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                            @foreach([
                                ['value' => 'all',   'label' => 'Tous les abonnés', 'icon' => '👥', 'color' => 'copper'],
                                ['value' => 'free',  'label' => 'Plan Gratuit',      'icon' => '🌱', 'color' => 'emerald'],
                                ['value' => 'basic', 'label' => 'Plan Basic',        'icon' => '⚡', 'color' => 'blue'],
                                ['value' => 'pro',   'label' => 'Plan Pro',          'icon' => '🚀', 'color' => 'purple'],
                            ] as $seg)

                            <label
                                wire:click="$set('audience', '{{ $seg['value'] }}')"
                                class="relative cursor-pointer rounded-2xl p-4 border-2 transition-all select-none
                                    {{ $audience === $seg['value']
                                        ? 'border-copper bg-copper/5 shadow-lg shadow-copper/10'
                                        : 'border-slate-100 bg-white/50 hover:border-slate-200' }}"
                            >
                                <div class="flex flex-col gap-2">
                                    <span class="text-xl">{{ $seg['icon'] }}</span>
                                    <div>
                                        <p class="text-xs font-black text-midnight">{{ $seg['label'] }}</p>
                                        <p class="text-[11px] font-bold text-slate-400 mt-0.5">{{ $counts[$seg['value']] ?? 0 }} destinataire(s)</p>
                                    </div>
                                </div>
                                @if($audience === $seg['value'])
                                <div class="absolute top-3 right-3 w-5 h-5 rounded-full bg-copper flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                @endif
                            </label>

                            @endforeach
                        </div>
                        @error('audience') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Content editor --}}
                    <div class="space-y-4" wire:ignore>
                        <div class="flex items-center justify-between px-1">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Contenu de la newsletter</label>
                            <button
                                type="button"
                                @click="
                                    htmlMode = !htmlMode;
                                    if (!htmlMode && editor) { editor.setData(content); }
                                "
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all"
                                :class="htmlMode ? 'bg-copper/10 text-copper' : 'bg-slate-100 text-slate-400 hover:text-slate-600'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span x-text="htmlMode ? 'Mode visuel' : 'Mode HTML'"></span>
                            </button>
                        </div>

                        <div x-show="!htmlMode" class="ck-editor-container">
                            <div x-ref="editorArea" class="prose prose-slate max-w-none"></div>
                        </div>

                        <div x-show="htmlMode" x-cloak>
                            <textarea
                                x-model="content"
                                placeholder="Collez votre HTML ici..."
                                class="w-full bg-slate-900 text-green-400 border-slate-700 rounded-[2rem] p-8 text-sm font-mono focus:ring-2 focus:ring-copper/20 focus:border-copper transition-all outline-none leading-relaxed min-h-[400px] resize-y shadow-2xl shadow-slate-900/20"
                            ></textarea>
                        </div>
                        @error('content') <span class="text-rose-500 text-xs font-bold px-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-2">
                        <div class="flex items-center gap-3 px-4 py-3 bg-amber-50 rounded-2xl border border-amber-100">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-bold text-amber-700">
                                <span class="font-black">{{ $this->selectedCount }}</span> destinataire(s) sélectionné(s)
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                wire:click="openPreview"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Aperçu
                            </button>

                            <button
                                type="button"
                                wire:click="openConfirmModal"
                                @disabled($isSending || $this->selectedCount === 0)
                                class="inline-flex items-center gap-2 px-8 py-3 rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-xl shadow-copper/20 bg-gradient-to-r from-copper to-amber-500 text-white hover:shadow-copper/30 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                @if($isSending)
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Envoi en cours...
                                @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Envoyer
                                @endif
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- ─── Send history ─── --}}
        <section class="space-y-6">
            <div class="flex items-center gap-4 px-4">
                <h2 class="font-display font-bold text-midnight text-lg tracking-tight shrink-0">Historique d'envoi</h2>
                <div class="h-px bg-slate-100 flex-1"></div>
                <span class="text-xs font-bold text-slate-400">{{ $logs->total() }} campagne(s)</span>
            </div>

            <div class="glass-panel overflow-hidden rounded-[2.5rem] border-white/60 shadow-xl shadow-slate-200/30 bg-white/40">
                @if($logs->isEmpty())
                <div class="py-20 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-50 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-400">Aucune newsletter envoyée pour l'instant</p>
                </div>
                @else
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5">Objet</th>
                            <th class="px-6 py-5">Audience</th>
                            <th class="px-6 py-5">Destinataires</th>
                            <th class="px-6 py-5">Envoyé par</th>
                            <th class="px-6 py-5">Date</th>
                            <th class="px-6 py-5">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($logs as $log)
                        <tr class="group hover:bg-white/60 transition-colors">
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-midnight line-clamp-1">{{ $log->subject }}</p>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                                    {{ $log->audience_label }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-bold text-slate-600">{{ number_format($log->recipients_count) }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-medium text-slate-500">{{ $log->sender?->name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs font-medium text-slate-400">{{ $log->created_at->format('d/m/Y à H:i') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                    {{ $log->status === 'sent' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $log->status === 'sent' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $log->status === 'sent' ? 'Envoyée' : 'Échec' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($logs->hasPages())
                <div class="px-8 py-5 border-t border-slate-50">
                    {{ $logs->links() }}
                </div>
                @endif
                @endif
            </div>
        </section>

    </div>

    {{-- ─── Confirm Send Modal ─── --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-init="$el.querySelector('[data-dialog]').classList.add('animate-scale-in')">
        <div class="absolute inset-0 bg-midnight/40 backdrop-blur-sm" wire:click="$set('showConfirmModal', false)"></div>
        <div data-dialog class="relative bg-white rounded-[2rem] shadow-2xl shadow-midnight/20 p-8 max-w-md w-full z-10">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-2xl shrink-0">📨</div>
                <div>
                    <h3 class="font-display font-bold text-lg text-midnight">Confirmer l'envoi</h3>
                    <p class="text-sm text-slate-400 font-medium mt-0.5">Cette action est irréversible</p>
                </div>
            </div>

            <div class="space-y-3 mb-8">
                <div class="flex justify-between items-center py-3 border-b border-slate-50">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Objet</span>
                    <span class="text-sm font-bold text-midnight text-right max-w-[220px] truncate">{{ $subject }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-slate-50">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Audience</span>
                    <span class="text-sm font-bold text-midnight">
                        @php $audienceLabels = ['all' => 'Tous', 'free' => 'Gratuit', 'basic' => 'Basic', 'pro' => 'Pro']; @endphp
                        {{ $audienceLabels[$audience] ?? ucfirst($audience) }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Destinataires</span>
                    <span class="text-2xl font-black text-copper">{{ $this->selectedCount }}</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button wire:click="$set('showConfirmModal', false)"
                        class="flex-1 px-6 py-3.5 rounded-2xl font-bold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all">
                    Annuler
                </button>
                <button wire:click="send"
                        class="flex-1 px-6 py-3.5 rounded-2xl font-black text-sm text-white bg-gradient-to-r from-copper to-amber-500 hover:shadow-lg hover:shadow-copper/20 transition-all active:scale-95">
                    Envoyer maintenant
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── Preview Modal ─── --}}
    @if($showPreviewModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-data x-init="$el.querySelector('[data-dialog]').classList.add('animate-scale-in')">
        <div class="absolute inset-0 bg-midnight/40 backdrop-blur-sm" wire:click="$set('showPreviewModal', false)"></div>
        <div data-dialog class="relative bg-slate-100 rounded-[2rem] shadow-2xl max-w-2xl w-full z-10 max-h-[90vh] overflow-hidden flex flex-col">
            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-white rounded-t-[2rem] border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    </div>
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Aperçu email</span>
                </div>
                <button wire:click="$set('showPreviewModal', false)" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- Email preview iframe --}}
            <div class="overflow-y-auto flex-1 p-4">
                <div class="bg-[#F8FAFC] rounded-2xl overflow-hidden">
                    {{-- Render the email template inline for preview --}}
                    <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #F8FAFC; padding: 32px 16px;">
                        <div style="max-width: 560px; margin: 0 auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
                            {{-- Header --}}
                            <div style="background: linear-gradient(135deg, #F59E0B, #D97706); padding: 28px 32px; text-align: center;">
                                <div style="display:inline-flex; align-items:center; gap:10px; margin-bottom:14px;">
                                    <span style="font-size:22px; font-weight:700; color:#fff; letter-spacing:-0.5px;">✉ Postelio</span>
                                </div>
                                <p style="margin:0; font-size:11px; font-weight:600; color:rgba(255,255,255,0.7); letter-spacing:0.15em; text-transform:uppercase;">Newsletter — Aperçu</p>
                            </div>
                            {{-- Subject --}}
                            <div style="padding: 28px 32px 0;">
                                <h1 style="margin:0; font-size:22px; font-weight:700; color:#111827; line-height:1.3;">{{ $subject ?: 'Objet de la newsletter' }}</h1>
                                <p style="margin:8px 0 0; font-size:14px; color:#64748B;">Bonjour Prénom 👋</p>
                            </div>
                            <div style="padding: 12px 32px 0;"><div style="border-top:2px solid #FEF3C7;"></div></div>
                            {{-- Content --}}
                            <div style="padding: 20px 32px 12px; font-size:15px; color:#374151; line-height:1.75;">
                                <style>
                                    .nl-preview-content h2 { font-size:20px; font-weight:700; color:#111827; margin:20px 0 8px; }
                                    .nl-preview-content h3 { font-size:17px; font-weight:600; color:#1E293B; margin:16px 0 6px; }
                                    .nl-preview-content p  { margin:0 0 14px; }
                                    .nl-preview-content a  { color:#D97706; font-weight:600; }
                                    .nl-preview-content ul, .nl-preview-content ol { margin:0 0 14px; padding-left:22px; }
                                    .nl-preview-content li { margin-bottom:4px; }
                                    .nl-preview-content blockquote { border-left:4px solid #F59E0B; margin:16px 0; padding:10px 16px; background:#FFFBEB; border-radius:0 10px 10px 0; color:#92400E; font-style:italic; }
                                    .nl-preview-content strong { font-weight:700; color:#111827; }
                                </style>
                                <div class="nl-preview-content">
                                    @if($content)
                                        {!! $content !!}
                                    @else
                                        <p style="color:#94A3B8; font-style:italic;">Le contenu de votre newsletter apparaîtra ici…</p>
                                    @endif
                                </div>
                            </div>
                            {{-- CTA --}}
                            <div style="padding: 12px 32px 28px;">
                                <div style="background:linear-gradient(135deg,#FFFBEB,#FEF3C7); border-radius:14px; border:1px solid #FDE68A; padding:18px 24px; text-align:center;">
                                    <p style="margin:0 0 12px; font-size:14px; font-weight:600; color:#92400E;">Accédez à votre espace Postelio</p>
                                    <span style="display:inline-block; padding:11px 28px; background:#111827; color:#fff; font-size:14px; font-weight:700; border-radius:50px; text-decoration:none;">Ouvrir mon tableau de bord →</span>
                                </div>
                            </div>
                            {{-- Footer --}}
                            <div style="padding:20px 32px 28px; border-top:1px solid #F1F5F9; text-align:center;">
                                <p style="margin:0 0 4px; font-size:12px; color:#94A3B8;">Postelio — La vitrine digitale des artisans passionnés</p>
                                <p style="margin:0; font-size:11px; color:#CBD5E1;">Se désabonner</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- CKEditor styling (same as blog editor) --}}
<style>
    .ck-editor-container .ck-editor__editable {
        min-height: 380px;
        border-radius: 0 0 1.5rem 1.5rem !important;
        border-color: #f1f5f9 !important;
        padding: 1.5rem 2rem !important;
        font-size: 0.9375rem;
        line-height: 1.75;
        color: #1e293b;
        background: rgba(255,255,255,0.5) !important;
    }
    .ck-editor-container .ck-toolbar {
        border-radius: 1.5rem 1.5rem 0 0 !important;
        border-color: #f1f5f9 !important;
        background: rgba(249,250,251,0.8) !important;
        padding: 0.5rem 1rem !important;
    }
    .ck-editor-container .ck-editor__top .ck-sticky-panel .ck-toolbar {
        border-bottom: 1px solid #f1f5f9;
    }
    .ck-editor-container .ck.ck-editor {
        border-radius: 1.5rem !important;
        overflow: hidden;
        border: 1px solid #f1f5f9 !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .ck-editor-container .ck-editor__editable:focus {
        box-shadow: 0 0 0 2px rgba(245,158,11,0.15) !important;
        border-color: #F59E0B !important;
    }
</style>
