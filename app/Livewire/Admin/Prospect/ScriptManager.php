<?php

namespace App\Livewire\Admin\Prospect;

use App\Models\SalesScript;
use Livewire\Component;

class ScriptManager extends Component
{
    public $scripts;
    public $newName = '';

    const BRANCH_CONDITIONS = [
        'reply'    => ['label' => 'Si réponse',       'color' => 'emerald'],
        'no_reply' => ['label' => 'Pas de réponse',   'color' => 'amber'],
        'positive' => ['label' => 'Réponse positive', 'color' => 'blue'],
        'negative' => ['label' => 'Réponse négative', 'color' => 'red'],
    ];

    public function mount(): void
    {
        $this->loadScripts();
    }

    public function loadScripts(): void
    {
        $this->scripts = SalesScript::latest()->get();
    }

    public function addScript(): void
    {
        $this->validate(['newName' => 'required|string|max:255']);

        $rootId = 'node_' . uniqid();

        SalesScript::create([
            'name'      => $this->newName,
            'sequences' => [],
            'flow'      => [
                'root'  => $rootId,
                'nodes' => [
                    $rootId => [
                        'id'       => $rootId,
                        'content'  => '',
                        'branches' => [],
                    ],
                ],
            ],
        ]);

        $this->newName = '';
        $this->loadScripts();
        $this->dispatch('scripts-updated');
        session()->flash('script_message', 'Script créé avec succès.');
    }

    public function updateName(int $scriptId, string $name): void
    {
        $script = SalesScript::findOrFail($scriptId);
        $script->update(['name' => trim($name)]);
        $this->loadScripts();
        $this->dispatch('scripts-updated');
    }

    /**
     * Initialize flow from old flat sequences (backward compat migration).
     */
    public function initFlow(int $scriptId): void
    {
        $script = SalesScript::findOrFail($scriptId);
        if ($script->flow) {
            return;
        }

        $sequences = $script->sequences ?? [''];
        if (empty($sequences)) {
            $sequences = [''];
        }

        $nodeIds = [];
        foreach ($sequences as $i => $_) {
            $nodeIds[] = 'node_' . uniqid();
        }

        $nodes = [];
        foreach ($sequences as $i => $content) {
            $nodeId   = $nodeIds[$i];
            $branches = [];

            if (isset($nodeIds[$i + 1])) {
                $branches[] = [
                    'condition' => 'reply',
                    'label'     => 'Si réponse',
                    'color'     => 'emerald',
                    'next'      => $nodeIds[$i + 1],
                ];
            }

            $nodes[$nodeId] = [
                'id'       => $nodeId,
                'content'  => $content,
                'branches' => $branches,
            ];
        }

        $script->update([
            'flow' => [
                'root'  => $nodeIds[0],
                'nodes' => $nodes,
            ],
        ]);

        $this->loadScripts();
    }

    public function updateNodeContent(int $scriptId, string $nodeId, string $content): void
    {
        $script = SalesScript::findOrFail($scriptId);
        $flow   = $script->flow;

        if (isset($flow['nodes'][$nodeId])) {
            $flow['nodes'][$nodeId]['content'] = $content;
            $script->update(['flow' => $flow]);
        }

        $this->loadScripts();
    }

    public function addBranch(int $scriptId, string $nodeId, string $condition): void
    {
        if (!array_key_exists($condition, self::BRANCH_CONDITIONS)) {
            return;
        }

        $script = SalesScript::findOrFail($scriptId);
        $flow   = $script->flow;

        if (!isset($flow['nodes'][$nodeId])) {
            return;
        }

        // Prevent duplicate conditions on the same node
        $usedConditions = array_column($flow['nodes'][$nodeId]['branches'], 'condition');
        if (in_array($condition, $usedConditions, true)) {
            return;
        }

        $newNodeId    = 'node_' . uniqid();
        $conditionMeta = self::BRANCH_CONDITIONS[$condition];

        $flow['nodes'][$newNodeId] = [
            'id'       => $newNodeId,
            'content'  => '',
            'branches' => [],
        ];

        $flow['nodes'][$nodeId]['branches'][] = [
            'condition' => $condition,
            'label'     => $conditionMeta['label'],
            'color'     => $conditionMeta['color'],
            'next'      => $newNodeId,
        ];

        $script->update(['flow' => $flow]);
        $this->loadScripts();
    }

    public function deleteNode(int $scriptId, string $nodeId): void
    {
        $script = SalesScript::findOrFail($scriptId);
        $flow   = $script->flow;

        // Find the parent node that has a branch pointing to this node
        foreach ($flow['nodes'] as $parentId => $parentNode) {
            foreach ($parentNode['branches'] as $branchIndex => $branch) {
                if ($branch['next'] === $nodeId) {
                    $this->deleteNodeTree($flow, $nodeId);
                    array_splice($flow['nodes'][$parentId]['branches'], $branchIndex, 1);
                    $script->update(['flow' => $flow]);
                    $this->loadScripts();
                    return;
                }
            }
        }
    }

    public function deleteBranch(int $scriptId, string $nodeId, int $branchIndex): void
    {
        $script   = SalesScript::findOrFail($scriptId);
        $flow     = $script->flow;
        $branches = $flow['nodes'][$nodeId]['branches'] ?? [];
        $branch   = $branches[$branchIndex] ?? null;

        if ($branch) {
            $this->deleteNodeTree($flow, $branch['next']);
        }

        array_splice($flow['nodes'][$nodeId]['branches'], $branchIndex, 1);

        $script->update(['flow' => $flow]);
        $this->loadScripts();
    }

    public function deleteScript(int $scriptId): void
    {
        SalesScript::findOrFail($scriptId)->delete();
        $this->loadScripts();
        $this->dispatch('scripts-updated');
    }

    private function deleteNodeTree(array &$flow, string $nodeId): void
    {
        $node = $flow['nodes'][$nodeId] ?? null;
        if (!$node) {
            return;
        }

        foreach ($node['branches'] as $branch) {
            $this->deleteNodeTree($flow, $branch['next']);
        }

        unset($flow['nodes'][$nodeId]);
    }

    public function render()
    {
        return view('livewire.admin.prospect.script-manager');
    }
}
