<div>
    @once
    <script>
    window.flowGraph = function(flowData, scriptId) {
        return {
            flow: flowData,
            scriptId: scriptId,
            edges: [],
            pathData: { reply: '', no_reply: '', positive: '', negative: '' },
            canvasW: 8000,
            canvasH: 8000,
            panning: false,
            startX: 0,
            startY: 0,
            scrollLeft: 0,
            scrollTop: 0,
            zoom: 1,
            
            meta: {
                reply:    { label: 'Si réponse',       icon: '💬', btn: 'bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-100', short: 'Réponse', bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200' },
                no_reply: { label: 'Pas de réponse',   icon: '⏳', btn: 'bg-amber-50 text-amber-600 border-amber-200 hover:bg-amber-100', short: 'Relance', bg: 'bg-amber-50',   text: 'text-amber-700',   border: 'border-amber-200' },
                positive: { label: 'Réponse positive', icon: '✅', btn: 'bg-blue-50 text-blue-600 border-blue-200 hover:bg-blue-100', short: 'Positif', bg: 'bg-blue-50',    text: 'text-blue-700',    border: 'border-blue-200' },
                negative: { label: 'Réponse négative', icon: '❌', btn: 'bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100', short: 'Négatif', bg: 'bg-rose-50',    text: 'text-rose-700',    border: 'border-rose-200' },
            },
            
            initCanvas() {
                this.$nextTick(() => {
                    const el = this.$refs.canvasContainer;
                    if(el) {
                        el.scrollLeft = (this.canvasW / 2) - (el.clientWidth / 2);
                        el.scrollTop = 150;
                    }
                    this.computeLayout();
                });
            },

            computeLayout() {
                if(!this.flow || !this.flow.root) return;
                let nodes = this.flow.nodes;
                let edges = [];
                let paths = { reply: '', no_reply: '', positive: '', negative: '' };
                let visited = new Set();
                let subtreeWidths = {};

                const calcWidth = (id) => {
                    if (visited.has(id)) return 0;
                    visited.add(id);
                    let node = nodes[id];
                    if (!node || !node.branches || node.branches.length === 0) {
                        subtreeWidths[id] = 350;
                        return 350;
                    }
                    let w = 0;
                    node.branches.forEach(b => { w += calcWidth(b.next); });
                    subtreeWidths[id] = Math.max(w, 350);
                    return subtreeWidths[id];
                };

                calcWidth(this.flow.root);
                visited.clear();

                const positionNode = (id, xStart, y) => {
                    if (visited.has(id)) return;
                    visited.add(id);
                    let node = nodes[id];
                    if(!node) return;
                    node.x = xStart + (subtreeWidths[id] / 2) - 140; 
                    node.y = y;

                    if (node.branches) {
                        let currentX = xStart;
                        node.branches.forEach((b, i) => {
                            let childId = b.next;
                            if(!nodes[childId]) return;
                            positionNode(childId, currentX, y + 450);
                            let target = nodes[childId];
                            let x1 = node.x + 140 + ((i - (node.branches.length - 1) / 2) * 60); 
                            let y1 = node.y + 240;
                            let x2 = target.x + 140; 
                            let y2 = target.y;
                            let pathStr = `M ${x1} ${y1} C ${x1} ${y1+100}, ${x2} ${y2-100}, ${x2} ${y2}`;
                            if (paths[b.condition] !== undefined) paths[b.condition] += ' ' + pathStr;
                            edges.push({
                                id: 'e-' + id + '-' + childId + '-' + i,
                                nodeId: id, branchIndex: i, condition: b.condition,
                                ...this.meta[b.condition],
                                lx: x1, ly: node.y + 280
                            });
                            currentX += subtreeWidths[childId] || 350;
                        });
                    }
                };

                positionNode(this.flow.root, (this.canvasW / 2) - (subtreeWidths[this.flow.root] / 2), 200);
                this.edges = edges;
                this.pathData = paths;
            },

            doZoom(e) {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    const el = e.currentTarget;
                    const rect = el.getBoundingClientRect();
                    const mouseX = e.clientX - rect.left;
                    const mouseY = e.clientY - rect.top;
                    const canvasMouseX = (el.scrollLeft + mouseX) / this.zoom;
                    const canvasMouseY = (el.scrollTop + mouseY) / this.zoom;
                    this.zoom = Math.min(Math.max(0.2, this.zoom * (e.deltaY > 0 ? 0.9 : 1.1)), 2.5);
                    this.$nextTick(() => {
                        el.scrollLeft = (canvasMouseX * this.zoom) - mouseX;
                        el.scrollTop = (canvasMouseY * this.zoom) - mouseY;
                    });
                }
            },

            startPanning(e) {
                if (e.target.closest('.node-wrap') || e.target.closest('button') || e.target.closest('textarea') || e.target.closest('.tag-label')) return;
                this.panning = true;
                this.startX = e.clientX;
                this.startY = e.clientY;
                this.scrollLeft = e.currentTarget.scrollLeft;
                this.scrollTop = e.currentTarget.scrollTop;
            },
            stopPanning() { this.panning = false; },
            movePanning(e) {
                if (!this.panning) return;
                e.preventDefault();
                const walkX = (e.clientX - this.startX) / this.zoom;
                const walkY = (e.clientY - this.startY) / this.zoom;
                e.currentTarget.scrollLeft = this.scrollLeft - walkX;
                e.currentTarget.scrollTop = this.scrollTop - walkY;
            },

            style(id) { 
                let n = this.flow.nodes[id]; 
                if(!n || typeof n.x === 'undefined') return 'opacity:0; pointer-events:none; position:absolute;'; 
                return `left:${n.x}px; top:${n.y}px; width:280px; position:absolute; z-index:10; opacity:1; transition:all 0.4s ease;`; 
            },

            doSave(id, val) { 
                this.flow.nodes[id].content = val;
                this.$wire.updateNodeContent(this.scriptId, id, val); 
            },
            doAdd(id, cond) { this.$wire.addBranch(this.scriptId, id, cond); },
            doDel(id, idx) { this.$wire.deleteBranch(this.scriptId, id, idx); },
            doDelNode(id) { this.$wire.deleteNode(this.scriptId, id); }
        };
    };
    
    // Convertit le flow plat en arbre imbriqué propre sans IDs
    function nodeToTree(flow, nodeId, depth = 0) {
        if (depth > 25) return { content: "[Limite]", branches: [] };
        const n = flow.nodes[nodeId];
        if (!n) return null;
        const res = { content: n.content || "", branches: [] };
        if (n.branches) {
            n.branches.forEach(b => {
                const child = nodeToTree(flow, b.next, depth + 1);
                if (child) res.branches.push({ condition: b.condition, message: child });
            });
        }
        return res;
    }

    // Fonction globale de copie avec format propre
    window.copyScriptJson = function(name, flow) {
        if(!flow || !flow.root) return;
        const tree = nodeToTree(flow, flow.root);
        const text = JSON.stringify({ name, ...tree }, null, 2);
        navigator.clipboard.writeText(text);
    };
    </script>
    @endonce

    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-6 shadow-sm overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 mb-8">
                <div>
                    <h2 class="font-black text-sm text-midnight flex items-center gap-2 uppercase tracking-tighter mb-1">
                        <svg class="w-5 h-5 text-copper" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Flux de script instagram
                    </h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest pl-7">Créez ou importez vos scripts de prospection</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                    <div x-data="{ showImport: false }" class="relative w-full sm:w-auto">
                        <button @click="showImport = !showImport" class="w-full sm:w-auto bg-slate-100 text-slate-600 px-5 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all flex items-center justify-center gap-2">
                            <span x-text="showImport ? 'Fermer' : 'Importer JSON'"></span>
                        </button>
                        <div x-show="showImport" x-cloak class="absolute top-full right-0 mt-2 w-full sm:w-80 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-4">
                            <textarea wire:model="importJson" class="w-full h-32 text-[10px] font-mono border-slate-100 rounded-xl focus:ring-copper p-3 bg-slate-50 mb-3" placeholder='Collez le JSON ici (format Arbre ou Flow)'></textarea>
                            <button wire:click="handleImportAction" @click="showImport = false" class="w-full bg-copper text-white py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:brightness-110">Confirmer l'import</button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input type="text" wire:model="newName" wire:keydown.enter="addScript" class="flex-1 sm:w-64 text-[11px] font-bold rounded-xl border-gray-200 focus:border-copper focus:ring-copper px-4 h-9" placeholder="NOM DU NOUVEAU SCRIPT...">
                        <button type="button" wire:click="addScript" class="bg-midnight text-white px-5 h-9 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-black transition-all">Créer</button>
                    </div>
                </div>
            </div>

            @foreach($scripts as $script)
                <div wire:key="script-row-v8-{{ $script->id }}" x-data="{ open: false }" class="mb-4 border border-slate-200 rounded-3xl bg-white shadow-sm overflow-hidden">
                    <div @click="open = !open" class="flex items-center gap-3 px-6 py-4 cursor-pointer hover:bg-slate-50 transition-colors group">
                        <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center transition-transform" :class="open ? 'rotate-90' : ''"><svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg></div>
                        <input type="text" value="{{ $script->name }}" @click.stop onblur="@this.updateName({{ $script->id }}, this.value)" class="bg-transparent border-none p-0 font-black text-midnight focus:ring-0 text-xs flex-1 uppercase tracking-tight">
                        
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" @click.stop="window.copyScriptJson('{{ addslashes($script->name) }}', @js($script->flow))" title="Copier le code propre" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                            <button type="button" @click.stop wire:click="duplicateScript({{ $script->id }})" title="Dupliquer" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-blue-500 hover:bg-blue-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                            </button>
                            <button type="button" @click.stop wire:click="deleteScript({{ $script->id }})" wire:confirm="Supprimer ce script ?" title="Supprimer" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    <div x-show="open" x-cloak class="border-t border-slate-100 p-4" wire:key="script-wrap-v8-{{ $script->id }}">
                        @if(!empty($script->flow))
                            <div x-data="flowGraph(@js($script->flow), {{ $script->id }})" 
                                 x-init="initCanvas(); $watch('open', val => val && initCanvas())" 
                                 x-on:scripts-updated.window="if($event.detail.scriptId == scriptId && $event.detail.flow) { flow = $event.detail.flow; computeLayout(); }"
                                 class="relative h-[720px] border border-slate-200 rounded-[2rem] bg-[#f8fafc] overflow-hidden cursor-grab"
                                 @mousedown="startPanning" @mouseup="stopPanning" @mouseleave="stopPanning" @mousemove="movePanning" @wheel="doZoom"
                                 x-ref="canvasContainer">
                                
                                <div class="relative origin-top-left" :style="`width:${canvasW}px; height:${canvasH}px; transform: scale(${zoom})`">
                                    <div class="absolute inset-0 pointer-events-none opacity-20" style="background:radial-gradient(circle, #cbd5e1 1px, transparent 1px); background-size:32px 32px;"></div>
                                    <div wire:ignore class="absolute inset-0 pointer-events-none">
                                        <svg :width="canvasW" :height="canvasH" class="overflow-visible">
                                            <path x-show="pathData.reply" :d="pathData.reply" stroke="#10b981" stroke-width="3" fill="none" stroke-dasharray="8,6" stroke-opacity="0.3" stroke-linecap="round"/>
                                            <path x-show="pathData.no_reply" :d="pathData.no_reply" stroke="#f59e0b" stroke-width="3" fill="none" stroke-dasharray="8,6" stroke-opacity="0.3" stroke-linecap="round"/>
                                            <path x-show="pathData.positive" :d="pathData.positive" stroke="#3b82f6" stroke-width="3" fill="none" stroke-dasharray="8,6" stroke-opacity="0.3" stroke-linecap="round"/>
                                            <path x-show="pathData.negative" :d="pathData.negative" stroke="#f43f5e" stroke-width="3" fill="none" stroke-dasharray="8,6" stroke-opacity="0.3" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <template x-for="edge in edges" :key="edge.id">
                                        <div class="absolute flex items-center gap-1 tag-label" :style="`left:${edge.lx}px; top:${edge.ly}px; z-index:50; transform: translate(-50%, -50%)`" @mousedown.stop>
                                            <div class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border backdrop-blur-md shadow-md" :class="[edge.bg, edge.text, edge.border]">
                                                <span x-text="edge.icon"></span> <span x-text="edge.short"></span>
                                            </div>
                                            <button @click.stop="doDel(edge.nodeId, edge.branchIndex)" class="w-5 h-5 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-300 hover:text-rose-400 text-[11px]">&times;</button>
                                        </div>
                                    </template>
                                    @foreach($script->flow['nodes'] as $nodeId => $nodeData)
                                        <div wire:key="node-v8-{{ $script->id }}-{{ $nodeId }}" :style="style('{{ $nodeId }}')" class="node-wrap" @mousedown.stop>
                                            <div class="bg-white rounded-3xl border-2 overflow-hidden shadow-sm min-h-[240px]" :class="'{{ $nodeId }}' === flow.root ? 'border-midnight/20' : 'border-slate-100'">
                                                <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-50" :class="'{{ $nodeId }}' === flow.root ? 'bg-midnight/5' : 'bg-slate-50/50'">
                                                    <div class="w-5 h-5 rounded-lg flex items-center justify-center text-[10px]" :class="'{{ $nodeId }}' === flow.root ? 'bg-midnight text-white' : 'bg-white text-slate-300 border border-slate-100'"><span x-text="'{{ $nodeId }}' === flow.root ? '🚀' : '✉️'"></span></div>
                                                    <span class="text-[9px] font-black uppercase tracking-widest truncate flex-1 opacity-40" x-text="'{{ $nodeId }}' === flow.root ? 'Source' : 'Réponse'"></span>
                                                    <button x-show="'{{ $nodeId }}' !== flow.root" @click.stop="doDelNode('{{ $nodeId }}')" class="hover:text-rose-400 text-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                                </div>
                                                <div class="p-4">
                                                    <textarea @blur.stop="doSave('{{ $nodeId }}', $event.target.value)" rows="4" class="w-full text-xs font-bold text-slate-800 bg-transparent border-0 focus:ring-0 p-0 resize-none placeholder-slate-200" placeholder="Ton message... ">{{ $nodeData['content'] ?? '' }}</textarea>
                                                </div>
                                                <div class="px-4 pb-4 pt-1 flex flex-wrap gap-1.5 border-t border-slate-50 mt-2">
                                                    @foreach(App\Livewire\Admin\Prospect\ScriptFlowManager::BRANCH_CONDITIONS as $cond => $meta)
                                                        @php
                                                            $isUsed = false;
                                                            foreach($nodeData['branches'] ?? [] as $b) { if($b['condition'] === $cond) $isUsed = true; }
                                                        @endphp
                                                        @if(!$isUsed)
                                                            <button @click.stop="doAdd('{{ $nodeId }}', '{{ $cond }}')" class="flex items-center gap-1.5 text-[9px] font-black border rounded-xl px-3 py-1.5 transition-all shadow-sm hover:translate-y-[-1px] {{ $cond == 'reply' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : '' }} {{ $cond == 'no_reply' ? 'bg-amber-50 text-amber-600 border-amber-100' : '' }} {{ $cond == 'positive' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }} {{ $cond == 'negative' ? 'bg-rose-50 text-rose-600 border-rose-100' : '' }}">
                                                                {{ $meta['label'] }}
                                                            </button>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-10"><button wire:click="initFlow({{ $script->id }})" class="bg-midnight text-white px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl">Initier le Flow</button></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
