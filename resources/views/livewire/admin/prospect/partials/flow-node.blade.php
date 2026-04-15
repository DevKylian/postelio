@php
    $node   = $nodes[$nodeId] ?? null;
    $depth  = $depth ?? 0;
    $isRoot = $depth === 0;

    $branchMeta = [
        'reply'    => [
            'cardBg'   => 'bg-emerald-50/60',
            'cardBorder'=> 'border-emerald-200',
            'headBg'   => 'bg-emerald-100/80',
            'headText' => 'text-emerald-800',
            'headBorder'=> 'border-emerald-200',
            'dot'      => 'bg-emerald-400',
            'addBg'    => 'bg-emerald-50',
            'addText'  => 'text-emerald-700',
            'addBorder'=> 'border-emerald-200',
            'addHover' => 'hover:bg-emerald-100',
            'icon'     => '💬',
        ],
        'no_reply' => [
            'cardBg'   => 'bg-amber-50/60',
            'cardBorder'=> 'border-amber-200',
            'headBg'   => 'bg-amber-100/80',
            'headText' => 'text-amber-800',
            'headBorder'=> 'border-amber-200',
            'dot'      => 'bg-amber-400',
            'addBg'    => 'bg-amber-50',
            'addText'  => 'text-amber-700',
            'addBorder'=> 'border-amber-200',
            'addHover' => 'hover:bg-amber-100',
            'icon'     => '⏰',
        ],
        'positive' => [
            'cardBg'   => 'bg-blue-50/60',
            'cardBorder'=> 'border-blue-200',
            'headBg'   => 'bg-blue-100/80',
            'headText' => 'text-blue-800',
            'headBorder'=> 'border-blue-200',
            'dot'      => 'bg-blue-400',
            'addBg'    => 'bg-blue-50',
            'addText'  => 'text-blue-700',
            'addBorder'=> 'border-blue-200',
            'addHover' => 'hover:bg-blue-100',
            'icon'     => '👍',
        ],
        'negative' => [
            'cardBg'   => 'bg-rose-50/60',
            'cardBorder'=> 'border-rose-200',
            'headBg'   => 'bg-rose-100/80',
            'headText' => 'text-rose-800',
            'headBorder'=> 'border-rose-200',
            'dot'      => 'bg-rose-400',
            'addBg'    => 'bg-rose-50',
            'addText'  => 'text-rose-700',
            'addBorder'=> 'border-rose-200',
            'addHover' => 'hover:bg-rose-100',
            'icon'     => '👎',
        ],
    ];

    $conditionLabels = [
        'reply'    => 'Si réponse',
        'no_reply' => 'Pas de réponse',
        'positive' => 'Réponse positive',
        'negative' => 'Réponse négative',
    ];

    $allConditions   = ['reply', 'no_reply', 'positive', 'negative'];
    $usedConditions  = !empty($node['branches']) ? array_column($node['branches'], 'condition') : [];
    $availConditions = array_values(array_diff($allConditions, $usedConditions));

    // Unique Alpine key per node (safe for JS identifier)
    $alpineKey = 'cp' . preg_replace('/[^a-z0-9]/i', '_', $nodeId);
@endphp

@if($node && $depth < 12)
<div class="flow-node-wrap">

    {{-- ╔══════════════════════════════════════╗
         ║  NODE CARD                           ║
         ╚══════════════════════════════════════╝ --}}
    <div class="bg-white rounded-2xl border-2 overflow-hidden shadow-sm
                {{ $isRoot ? 'border-midnight/20' : 'border-slate-200' }}">

        {{-- Header bar --}}
        <div class="flex items-center gap-2 px-4 py-2.5 border-b border-slate-100
                    {{ $isRoot ? 'bg-midnight/[0.05]' : 'bg-slate-50' }}">

            {{-- Icon + label --}}
            <div class="w-6 h-6 rounded-lg flex-shrink-0 flex items-center justify-center
                        {{ $isRoot ? 'bg-midnight text-white' : 'bg-slate-200 text-slate-500' }}">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                </svg>
            </div>

            <span class="text-[10px] font-black uppercase tracking-widest
                         {{ $isRoot ? 'text-midnight/60' : 'text-slate-400' }}">
                {{ $isRoot ? '🚀 Message initial' : '✉️ Message' }}
            </span>

            {{-- Step badge --}}
            <span class="text-[9px] font-bold text-slate-300 bg-slate-100
                         px-2 py-0.5 rounded-full tabular-nums">
                Étape {{ $depth + 1 }}
            </span>

            <div class="ml-auto flex items-center gap-1">
                {{-- Delete node (non-root only) --}}
                @if(!$isRoot)
                <button
                    wire:click="deleteNode({{ $scriptId }}, '{{ $nodeId }}')"
                    wire:confirm="Supprimer ce message et tous ses enfants ?"
                    class="w-7 h-7 flex items-center justify-center rounded-lg
                           text-slate-300 hover:text-rose-400 hover:bg-rose-50 transition-all"
                    title="Supprimer ce message"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                @endif

                {{-- Copy --}}
                <div x-data="{ {{ $alpineKey }}: false }">
                    <button
                        @click="
                            const ta = $el.closest('.flow-node-wrap').querySelector('textarea');
                            navigator.clipboard.writeText(ta ? ta.value : '');
                            {{ $alpineKey }} = true;
                            setTimeout(() => {{ $alpineKey }} = false, 2000);
                        "
                        :class="{{ $alpineKey }} ? 'bg-green-500 text-white' : 'text-slate-300 hover:text-copper hover:bg-amber-50'"
                        class="w-7 h-7 flex items-center justify-center rounded-lg transition-all"
                        title="Copier le message"
                    >
                        <svg x-show="!{{ $alpineKey }}" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <svg x-show="{{ $alpineKey }}" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Textarea --}}
        <div class="p-4">
            <textarea
                onblur="@this.updateNodeContent({{ $scriptId }}, '{{ $nodeId }}', this.value)"
                rows="4"
                class="w-full text-xs text-slate-700 bg-transparent border-0 focus:ring-0 p-0
                       resize-none placeholder-slate-300 leading-relaxed"
                placeholder="{{ $isRoot ? 'Ton message d\'accroche initial...' : 'Ton message ici...' }}"
            >{{ $node['content'] }}</textarea>
        </div>
    </div>

    {{-- ╔══════════════════════════════════════╗
         ║  BRANCHES                            ║
         ╚══════════════════════════════════════╝ --}}
    @if(!empty($node['branches']) || !empty($availConditions))
    <div class="mt-2">

        {{-- ── "Selon la réponse" divider ───────── --}}
        @if(!empty($node['branches']))
        <div class="flex items-center gap-3 my-4">
            <div class="flex-1 h-px bg-slate-200"></div>
            <div class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest
                        text-slate-400 bg-slate-100 px-3 py-1.5 rounded-full flex-shrink-0">
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Selon la réponse
            </div>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>
        @endif

        {{-- ── Branch cards ─────────────────────── --}}
        <div class="space-y-3">
            @foreach($node['branches'] as $branchIndex => $branch)
                @php $bm = $branchMeta[$branch['condition']] ?? $branchMeta['reply']; @endphp

                <div class="{{ $bm['cardBg'] }} {{ $bm['cardBorder'] }} border-2 rounded-2xl overflow-hidden">

                    {{-- Branch header --}}
                    <div class="{{ $bm['headBg'] }} {{ $bm['headBorder'] }} border-b
                                flex items-center gap-2 px-4 py-2.5">
                        <div class="w-2.5 h-2.5 rounded-full {{ $bm['dot'] }} flex-shrink-0"></div>
                        <span class="{{ $bm['headText'] }} text-[10px] font-black uppercase tracking-wider">
                            {{ $bm['icon'] }} {{ $branch['label'] }}
                        </span>
                        <button
                            wire:click="deleteBranch({{ $scriptId }}, '{{ $nodeId }}', {{ $branchIndex }})"
                            wire:confirm="Supprimer cette branche et tous ses messages ?"
                            class="ml-auto w-5 h-5 flex items-center justify-center rounded-full
                                   {{ $bm['headText'] }} opacity-40 hover:opacity-100
                                   hover:bg-rose-100 hover:text-rose-500 transition-all"
                            title="Supprimer cette branche"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Child node (recursive) --}}
                    <div class="p-3">
                        @include('livewire.admin.prospect.partials.flow-node', [
                            'nodeId'   => $branch['next'],
                            'nodes'    => $nodes,
                            'scriptId' => $scriptId,
                            'depth'    => $depth + 1,
                        ])
                    </div>

                </div>
            @endforeach
        </div>

        {{-- ── Add branch buttons ────────────────── --}}
        @if(!empty($availConditions))
            <div class="flex flex-wrap gap-2 mt-3">
                @foreach($availConditions as $condition)
                    @php $bm = $branchMeta[$condition]; @endphp
                    <button
                        wire:click="addBranch({{ $scriptId }}, '{{ $nodeId }}', '{{ $condition }}')"
                        class="{{ $bm['addBg'] }} {{ $bm['addText'] }} {{ $bm['addBorder'] }} {{ $bm['addHover'] }}
                               border px-3 py-1.5 rounded-full text-[10px] font-bold
                               flex items-center gap-1.5 transition-all"
                    >
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ $conditionLabels[$condition] }}
                    </button>
                @endforeach
            </div>
        @endif

    </div>
    @endif

</div>
@endif
