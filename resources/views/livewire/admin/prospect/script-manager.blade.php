{{-- ══════════════════════════════════════════════════════════
     Flow Graph — Alpine.js component (loaded once per page)
══════════════════════════════════════════════════════════ --}}
@once
<script>
window.flowGraph = function(flowData, scriptId) {
    return {
        wire:      null,
        flow:      flowData || { root: null, nodes: {} },
        scriptId:  scriptId,
        positions: {},
        canvasW:   600,
        canvasH:   300,

        /* ── Layout constants ── */
        NODE_W: 242,
        NODE_H: 218,   // estimated card height (header + textarea + buttons)
        H_GAP:  36,
        V_GAP:  110,

        /* ── Branch metadata ── */
        meta: {
            reply:    { icon:'💬', label:'Si réponse',       short:'Réponse',  stroke:'#059669', bg:'bg-emerald-100', text:'text-emerald-800', border:'border-emerald-200', btn:'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' },
            no_reply: { icon:'⏰', label:'Pas de réponse',   short:'Silence',  stroke:'#d97706', bg:'bg-amber-100',   text:'text-amber-800',   border:'border-amber-200',   btn:'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' },
            positive: { icon:'👍', label:'Réponse positive', short:'Positif',  stroke:'#2563eb', bg:'bg-blue-100',    text:'text-blue-800',    border:'border-blue-200',    btn:'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' },
            negative: { icon:'👎', label:'Réponse négative', short:'Négatif',  stroke:'#e11d48', bg:'bg-rose-100',    text:'text-rose-800',    border:'border-rose-200',    btn:'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' },
        },
        allConds: ['reply','no_reply','positive','negative'],

        /* ── Bootstrap ── */
        initWire(w) { this.wire = w; },

        /* ─────────────────────────────────────────────────────
           Tree layout algorithm
           - Recursively compute each node's "span" (how many
             leaf-columns its subtree occupies)
           - Place nodes so each is centered over its children
        ───────────────────────────────────────────────────── */
        computeLayout() {
            const nodes = this.flow?.nodes;
            const root  = this.flow?.root;
            if (!nodes || !root || !nodes[root]) { this.positions = {}; return; }

            /* 1 — span of each subtree */
            const span = {};
            const calcSpan = id => {
                const n = nodes[id];
                if (!n) return 1;
                const bs = n.branches || [];
                return (span[id] = bs.length ? bs.reduce((s,b) => s + calcSpan(b.next), 0) : 1);
            };
            calcSpan(root);

            /* 2 — assign (x,y) positions */
            const pos   = {};
            const UNIT  = this.NODE_W + this.H_GAP;
            const place = (id, depth, leftPx) => {
                const n = nodes[id]; if (!n) return;
                const s = span[id] || 1;
                const cx = leftPx + (s * UNIT - this.H_GAP) / 2;
                const y  = depth * (this.NODE_H + this.V_GAP);
                pos[id]  = { x: cx - this.NODE_W / 2, y, cx, by: y + this.NODE_H, ty: y };
                let cl = leftPx;
                (n.branches || []).forEach(b => {
                    const cs = span[b.next] || 1;
                    place(b.next, depth + 1, cl);
                    cl += cs * UNIT;
                });
            };
            place(root, 0, 0);

            /* 3 — normalise & compute canvas bounds */
            const vals = Object.values(pos);
            const minX = Math.min(...vals.map(p => p.x));
            const maxX = Math.max(...vals.map(p => p.x + this.NODE_W));
            const maxY = Math.max(...vals.map(p => p.y + this.NODE_H));
            const PAD  = 44, dx = -minX + PAD;

            Object.values(pos).forEach(p => { p.x += dx; p.cx += dx; });
            this.positions = pos;
            this.canvasW   = maxX - minX + PAD * 2;
            this.canvasH   = maxY + PAD;
        },

        /* ── Computed edge list (for SVG + labels) ── */
        get edges() {
            const nodes = this.flow?.nodes; if (!nodes) return [];
            const out = [];
            Object.entries(nodes).forEach(([nid, n]) => {
                (n.branches || []).forEach((b, i) => {
                    const f = this.positions[nid], t = this.positions[b.next];
                    if (!f || !t) return;
                    const ctrl = this.V_GAP * 0.52;
                    const m    = this.meta[b.condition] || this.meta.reply;
                    out.push({
                        id: `${nid}-${i}`, nodeId: nid, branchIndex: i, condition: b.condition,
                        path:   `M${f.cx},${f.by} C${f.cx},${f.by+ctrl} ${t.cx},${t.ty-ctrl} ${t.cx},${t.ty}`,
                        stroke: m.stroke,
                        lx: (f.cx + t.cx) / 2 - 58,
                        ly: (f.by + t.ty) / 2 - 13,
                        icon: m.icon, short: m.short, bg: m.bg, text: m.text, border: m.border,
                    });
                });
            });
            return out;
        },

        /* ── Helpers ── */
        get nodeIds() { return Object.keys(this.flow?.nodes || {}); },
        node(id)      { return this.flow?.nodes?.[id] || null; },
        isRoot(id)    { return id === this.flow?.root; },
        style(id)     {
            const p = this.positions[id];
            return p ? `position:absolute;left:${p.x}px;top:${p.y}px;width:${this.NODE_W}px` : 'display:none';
        },
        avail(id) {
            const used = (this.node(id)?.branches || []).map(b => b.condition);
            return this.allConds.filter(c => !used.includes(c));
        },

        /* ── Livewire actions ── */
        doAdd(nid, cond) { this.wire.addBranch(this.scriptId, nid, cond); },
        doDel(nid, idx)  {
            if (confirm('Supprimer cette branche et tous ses messages ?'))
                this.wire.deleteBranch(this.scriptId, nid, idx);
        },
        doDelNode(nid) {
            if (confirm('Supprimer ce message et tous ses enfants ?'))
                this.wire.deleteNode(this.scriptId, nid);
        },
        doSave(nid, val) { this.wire.updateNodeContent(this.scriptId, nid, val); },
    };
};
</script>
@endonce

{{-- ══════════════════════════════════════════════════════════
     MAIN COMPONENT VIEW
══════════════════════════════════════════════════════════ --}}
<div class="space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 shadow-sm">

    {{-- ── Header: title + new script form ──────────────── --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="font-bold text-base text-midnight flex items-center gap-2">
                <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Bibliothèque de Scripts &amp; Séquences
            </h2>
            <p class="text-[11px] text-slate-400 mt-1 ml-7">
                Construis tes scripts de prospection avec des branches conditionnelles
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
            <input type="text" wire:model="newName" wire:keydown.enter="addScript"
                   class="w-full sm:w-64 text-xs rounded-xl border-gray-200 focus:border-copper focus:ring-copper"
                   placeholder="Nom du nouveau script...">
            <button wire:click="addScript" wire:loading.attr="disabled"
                    class="bg-midnight text-white px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-800 transition-colors shadow-sm whitespace-nowrap flex items-center gap-2 justify-center">
                <span wire:loading.remove wire:target="addScript">
                    <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Nouveau Script
                </span>
                <span wire:loading wire:target="addScript" class="flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Création...
                </span>
            </button>
        </div>
    </div>

    @if(session()->has('script_message'))
        <div class="mb-4 text-xs text-green-700 bg-green-50 border border-green-100 p-3 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('script_message') }}
        </div>
    @endif

    {{-- ── Empty state ────────────────────────────────────── --}}
    @if($scripts->isEmpty())
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-500">Aucun script enregistré</p>
            <p class="text-xs text-slate-400 mt-1">Crée ton premier script de prospection ci-dessus.</p>
        </div>

    @else
    {{-- ── Scripts accordion list ──────────────────────────── --}}
    <div class="space-y-3">
        @foreach($scripts as $script)
            @php
                $flow        = $script->flow;
                $hasFlow     = !empty($flow);
                $nodeCount   = $hasFlow ? count($flow['nodes']) : 0;
                $branchCount = $hasFlow ? collect($flow['nodes'])->sum(fn($n) => count($n['branches'])) : 0;
            @endphp

            <div x-data="{ open: false }"
                 class="border border-slate-200 rounded-2xl overflow-hidden bg-white">

                {{-- ─── Script accordion header ──────────────── --}}
                <div @click="open = !open"
                     class="flex items-center gap-3 px-4 py-3.5 cursor-pointer select-none hover:bg-slate-50/60 transition-colors group">

                    <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-slate-200 flex items-center justify-center flex-shrink-0 transition-colors">
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-90' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                    <input type="text" value="{{ $script->name }}" @click.stop
                           onblur="@this.updateName({{ $script->id }}, this.value)"
                           class="bg-transparent border-none p-0 font-bold text-midnight focus:ring-0 text-sm flex-1 min-w-0 cursor-text uppercase tracking-tight">

                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($hasFlow)
                            <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/></svg>
                                {{ $nodeCount }}
                            </span>
                            <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                {{ $branchCount }}
                            </span>
                        @endif
                        <button @click.stop wire:click="deleteScript({{ $script->id }})"
                                wire:confirm="Supprimer ce script définitivement ?"
                                class="w-7 h-7 flex items-center justify-center rounded-xl text-slate-300 hover:text-rose-400 hover:bg-rose-50 transition-all opacity-0 group-hover:opacity-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ─── Flow graph (expanded) ────────────────── --}}
                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="border-t border-slate-100">

                        @if($hasFlow)
                        {{-- ════ Alpine.js graph component ════ --}}
                        <div
                            x-data="flowGraph(@js($flow), {{ $script->id }})"
                            x-init="initWire($wire); $nextTick(() => computeLayout())"
                            class="p-3 sm:p-4"
                        >
                            {{-- Legend bar --}}
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mb-3 text-[9px] font-black uppercase tracking-widest">
                                <span class="text-slate-300">Branches :</span>
                                <span class="flex items-center gap-1 text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>💬 Réponse</span>
                                <span class="flex items-center gap-1 text-amber-600"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>⏰ Silence</span>
                                <span class="flex items-center gap-1 text-blue-600"><span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>👍 Positif</span>
                                <span class="flex items-center gap-1 text-rose-600"><span class="w-2 h-2 rounded-full bg-rose-400 inline-block"></span>👎 Négatif</span>
                            </div>

                            {{-- ── Scrollable graph canvas ──────── --}}
                            <div class="overflow-auto rounded-2xl border border-slate-200 bg-[#f8fafc]" style="max-height:540px">
                                <div class="relative"
                                     :style="`width:${Math.max(canvasW,100)}px; height:${Math.max(canvasH,200)}px`">

                                    {{-- Dot grid background --}}
                                    <div class="absolute inset-0 pointer-events-none"
                                         style="background:radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size:24px 24px; opacity:.45;"></div>

                                    {{-- ── SVG: bezier arrows ──── --}}
                                    <svg class="absolute inset-0 overflow-visible pointer-events-none"
                                         :width="canvasW" :height="canvasH">
                                        <defs>
                                            <marker id="arr-{{ $script->id }}-reply"    markerWidth="8" markerHeight="6" refX="7" refY="3" orient="auto"><polygon points="0 0,8 3,0 6" fill="#059669"/></marker>
                                            <marker id="arr-{{ $script->id }}-no_reply" markerWidth="8" markerHeight="6" refX="7" refY="3" orient="auto"><polygon points="0 0,8 3,0 6" fill="#d97706"/></marker>
                                            <marker id="arr-{{ $script->id }}-positive" markerWidth="8" markerHeight="6" refX="7" refY="3" orient="auto"><polygon points="0 0,8 3,0 6" fill="#2563eb"/></marker>
                                            <marker id="arr-{{ $script->id }}-negative" markerWidth="8" markerHeight="6" refX="7" refY="3" orient="auto"><polygon points="0 0,8 3,0 6" fill="#e11d48"/></marker>
                                        </defs>
                                        <template x-for="e in edges" :key="e.id">
                                            <path :d="e.path" :stroke="e.stroke" stroke-width="2" fill="none"
                                                  :marker-end="`url(#arr-{{ $script->id }}-${e.condition})`"
                                                  stroke-opacity="0.65"/>
                                        </template>
                                    </svg>

                                    {{-- ── Edge labels (HTML) ──── --}}
                                    <template x-for="e in edges" :key="e.id+'-l'">
                                        <div class="absolute flex items-center gap-1 pointer-events-auto select-none"
                                             style="z-index:15"
                                             :style="`left:${e.lx}px; top:${e.ly}px`">
                                            <div class="flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider border shadow-sm bg-white/80 backdrop-blur-sm"
                                                 :class="[e.bg, e.text, e.border]">
                                                <span x-text="e.icon"></span>
                                                <span x-text="e.short"></span>
                                            </div>
                                            <button @click.stop="doDel(e.nodeId, e.branchIndex)"
                                                    class="w-4 h-4 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-300 hover:text-rose-400 hover:border-rose-300 shadow-sm text-[10px] font-bold leading-none transition-all">×</button>
                                        </div>
                                    </template>

                                    {{-- ── Node cards (HTML) ───── --}}
                                    <template x-for="nodeId in nodeIds" :key="nodeId">
                                        <div :style="style(nodeId)" class="absolute z-10 node-wrap">
                                            <div class="bg-white rounded-2xl border-2 overflow-hidden shadow-sm transition-shadow hover:shadow-md"
                                                 :class="isRoot(nodeId) ? 'border-midnight/20' : 'border-slate-200'">

                                                {{-- Card header --}}
                                                <div class="flex items-center gap-2 px-3 py-2 border-b border-slate-100"
                                                     :class="isRoot(nodeId) ? 'bg-midnight/[0.05]' : 'bg-slate-50'">
                                                    <div class="w-5 h-5 rounded-lg flex-shrink-0 flex items-center justify-center text-[11px]"
                                                         :class="isRoot(nodeId) ? 'bg-midnight text-white' : 'bg-slate-100 text-slate-400'">
                                                        <span x-text="isRoot(nodeId) ? '🚀' : '✉️'"></span>
                                                    </div>
                                                    <span class="text-[9px] font-black uppercase tracking-widest truncate flex-1"
                                                          :class="isRoot(nodeId) ? 'text-midnight/60' : 'text-slate-400'"
                                                          x-text="isRoot(nodeId) ? 'Message initial' : 'Message'">
                                                    </span>
                                                    <div class="flex items-center gap-0.5 flex-shrink-0">
                                                        {{-- Delete (non-root only) --}}
                                                        <button x-show="!isRoot(nodeId)"
                                                                @click.stop="doDelNode(nodeId)"
                                                                class="w-6 h-6 flex items-center justify-center rounded-md text-slate-300 hover:text-rose-400 hover:bg-rose-50 transition-all"
                                                                title="Supprimer ce message">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                        {{-- Copy --}}
                                                        <div x-data="{ cp: false }">
                                                            <button @click.stop="
                                                                    const ta = $el.closest('.node-wrap').querySelector('textarea');
                                                                    navigator.clipboard.writeText(ta ? ta.value : '');
                                                                    cp = true; setTimeout(() => cp = false, 2000);"
                                                                    :class="cp ? 'bg-green-500 text-white' : 'text-slate-300 hover:text-copper hover:bg-amber-50'"
                                                                    class="w-6 h-6 flex items-center justify-center rounded-md transition-all" title="Copier">
                                                                <svg x-show="!cp" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                                <svg x-show="cp" x-cloak class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Textarea --}}
                                                <div class="p-3">
                                                    <textarea
                                                        :value="node(nodeId)?.content || ''"
                                                        @blur.stop="doSave(nodeId, $event.target.value)"
                                                        rows="4"
                                                        class="w-full text-xs text-slate-700 bg-transparent border-0 focus:ring-0 p-0 resize-none placeholder-slate-300 leading-relaxed"
                                                        :placeholder="isRoot(nodeId) ? 'Message initial...' : 'Ton message...'"
                                                    ></textarea>
                                                </div>

                                                {{-- Add branch buttons --}}
                                                <template x-if="avail(nodeId).length > 0">
                                                    <div class="px-3 pb-3 pt-1.5 border-t border-slate-100 flex flex-wrap gap-1.5">
                                                        <template x-for="cond in avail(nodeId)" :key="cond">
                                                            <button @click.stop="doAdd(nodeId, cond)"
                                                                    class="flex items-center gap-1 text-[9px] font-bold border rounded-full px-2.5 py-1 transition-all"
                                                                    :class="meta[cond].btn">
                                                                <svg class="w-2 h-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                                                <span x-text="meta[cond].icon + ' ' + meta[cond].label"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>

                                            </div>
                                        </div>
                                    </template>

                                </div>{{-- /canvas inner --}}
                            </div>{{-- /overflow container --}}
                        </div>{{-- /x-data flowGraph --}}

                        @else
                        {{-- Old format: migration button --}}
                        <div class="text-center py-10 px-6">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-600 mb-1">Ce script utilise l'ancien format</p>
                            <p class="text-xs text-slate-400 mb-4">Convertis-le pour bénéficier du flow builder graphique.</p>
                            <button wire:click="initFlow({{ $script->id }})"
                                    class="bg-midnight text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-800 transition-colors shadow-sm inline-flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Convertir en Flow Builder
                            </button>
                        </div>
                        @endif

                    </div>
                </div>{{-- /accordion body --}}

            </div>{{-- /script card --}}
        @endforeach
    </div>
    @endif

</div>
</div>
