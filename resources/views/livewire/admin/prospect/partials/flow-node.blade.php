@php
    $node  = $nodes[$nodeId] ?? null;
    $depth = $depth ?? 0;
    $isRoot = $depth === 0;

    $branchMeta = [
        'reply'    => [
            'bg'     => 'bg-emerald-50',
            'border' => 'border-emerald-200',
            'text'   => 'text-emerald-700',
            'dot'    => 'bg-emerald-400',
            'line'   => 'border-emerald-200',
            'btn'    => 'hover:bg-emerald-100',
            'icon'   => '💬',
        ],
        'no_reply' => [
            'bg'     => 'bg-amber-50',
            'border' => 'border-amber-200',
            'text'   => 'text-amber-700',
            'dot'    => 'bg-amber-400',
            'line'   => 'border-amber-200',
            'btn'    => 'hover:bg-amber-100',
            'icon'   => '⏰',
        ],
        'positive' => [
            'bg'     => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text'   => 'text-blue-700',
            'dot'    => 'bg-blue-400',
            'line'   => 'border-blue-200',
            'btn'    => 'hover:bg-blue-100',
            'icon'   => '👍',
        ],
        'negative' => [
            'bg'     => 'bg-rose-50',
            'border' => 'border-rose-200',
            'text'   => 'text-rose-700',
            'dot'    => 'bg-rose-400',
            'line'   => 'border-rose-200',
            'btn'    => 'hover:bg-rose-100',
            'icon'   => '👎',
        ],
    ];

    $conditionLabels = [
        'reply'    => 'Si réponse',
        'no_reply' => 'Pas de réponse',
        'positive' => 'Réponse positive',
        'negative' => 'Réponse négative',
    ];

    $allConditions    = ['reply', 'no_reply', 'positive', 'negative'];
    $usedConditions   = !empty($node['branches']) ? array_column($node['branches'], 'condition') : [];
    $availConditions  = array_values(array_diff($allConditions, $usedConditions));
@endphp

@if($node && $depth < 12)
<div class="flow-node-wrap">

    {{-- ─── NODE CARD ─────────────────────────────────── --}}
    <div class="relative bg-white rounded-2xl shadow-sm border-2 overflow-hidden
                {{ $isRoot ? 'border-midnight/20 shadow-card' : 'border-slate-200' }}
                transition-shadow hover:shadow-card-hover">

        {{-- Card header bar --}}
        <div class="flex items-center gap-2 px-4 py-2.5 border-b border-slate-100
                    {{ $isRoot ? 'bg-midnight/[0.04]' : 'bg-slate-50/60' }}">

            <div class="w-6 h-6 rounded-lg flex-shrink-0 flex items-center justify-center
                        {{ $isRoot ? 'bg-midnight text-white' : 'bg-slate-200 text-slate-500' }}">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
                </svg>
            </div>

            <span class="text-[10px] font-black uppercase tracking-widest
                         {{ $isRoot ? 'text-midnight/50' : 'text-slate-400' }}">
                {{ $isRoot ? '🚀 Message initial' : '✉️ Message' }}
            </span>

            {{-- Delete node button (non-root only) --}}
            @if(!$isRoot)
            <button
                wire:click="deleteNode({{ $scriptId }}, '{{ $nodeId }}')"
                wire:confirm="Supprimer ce message et tous ses enfants ?"
                class="w-7 h-7 flex items-center justify-center rounded-lg ml-auto
                       text-slate-300 hover:text-rose-400 hover:bg-rose-50 transition-all"
                title="Supprimer ce message"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
            @endif

            {{-- Copy button --}}
            <div x-data="{ cp{{ str_replace('-','_',$nodeId) }}: false }" class="{{ $isRoot ? 'ml-auto' : '' }}">
                <button
                    @click="
                        const ta = $el.closest('.flow-node-wrap').querySelector('textarea');
                        navigator.clipboard.writeText(ta ? ta.value : '');
                        cp{{ str_replace('-','_',$nodeId) }} = true;
                        setTimeout(() => cp{{ str_replace('-','_',$nodeId) }} = false, 2000);
                    "
                    :class="cp{{ str_replace('-','_',$nodeId) }}
                        ? 'bg-green-500 text-white'
                        : 'text-slate-300 hover:text-copper hover:bg-amber-50'"
                    class="w-7 h-7 flex items-center justify-center rounded-lg transition-all"
                    title="Copier le message"
                >
                    <svg x-show="!cp{{ str_replace('-','_',$nodeId) }}" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <svg x-show="cp{{ str_replace('-','_',$nodeId) }}" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Textarea --}}
        <div class="p-3.5">
            <textarea
                onblur="@this.updateNodeContent({{ $scriptId }}, '{{ $nodeId }}', this.value)"
                rows="4"
                class="w-full text-xs text-slate-700 bg-transparent border-0 focus:ring-0 p-0
                       resize-none placeholder-slate-300 leading-relaxed"
                placeholder="{{ $isRoot ? 'Ton message d\'accroche initial...' : 'Ton message ici...' }}"
            >{{ $node['content'] }}</textarea>
        </div>
    </div>

    {{-- ─── BRANCHES & ADD BUTTONS ────────────────────── --}}
    @if(!empty($node['branches']) || !empty($availConditions))
    <div class="mt-1 pl-4">

        {{-- Vertical connector line --}}
        <div class="h-5 w-px bg-slate-200 ml-1 mb-0"></div>

        {{-- Existing branches --}}
        @foreach($node['branches'] as $branchIndex => $branch)
            @php $bm = $branchMeta[$branch['condition']] ?? $branchMeta['reply']; @endphp
            <div class="mb-6">
                {{-- Branch label row --}}
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-3 h-3 rounded-full {{ $bm['dot'] }} ring-2 ring-white shadow-sm flex-shrink-0"></div>
                    <span class="{{ $bm['bg'] }} {{ $bm['text'] }} {{ $bm['border'] }} border
                                 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                        {{ $bm['icon'] }} {{ $branch['label'] }}
                    </span>
                    <button
                        wire:click="deleteBranch({{ $scriptId }}, '{{ $nodeId }}', {{ $branchIndex }})"
                        wire:confirm="Supprimer cette branche et tous ses messages ?"
                        class="w-5 h-5 flex items-center justify-center rounded-full ml-auto flex-shrink-0
                               text-slate-200 hover:text-rose-400 hover:bg-rose-50 transition-all"
                        title="Supprimer cette branche"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Child node (recursive) --}}
                <div class="pl-4 border-l-2 {{ $bm['line'] }} ml-1">
                    @include('livewire.admin.prospect.partials.flow-node', [
                        'nodeId'   => $branch['next'],
                        'nodes'    => $nodes,
                        'scriptId' => $scriptId,
                        'depth'    => $depth + 1,
                    ])
                </div>
            </div>
        @endforeach

        {{-- Add branch buttons --}}
        @if(!empty($availConditions))
            <div class="flex flex-wrap gap-2 mt-1 pb-1">
                @foreach($availConditions as $condition)
                    @php $bm = $branchMeta[$condition]; @endphp
                    <button
                        wire:click="addBranch({{ $scriptId }}, '{{ $nodeId }}', '{{ $condition }}')"
                        class="{{ $bm['bg'] }} {{ $bm['text'] }} {{ $bm['border'] }} {{ $bm['btn'] }}
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
