<?php

namespace App\Livewire\Admin\Prospect;

use App\Models\SalesScript;
use Livewire\Component;

class ScriptFlowManager extends Component
{
    public $newName = '';
    public $importJson = '';

    public const BRANCH_CONDITIONS = [
        'reply'    => ['label' => 'Si réponse',       'color' => 'emerald'],
        'no_reply' => ['label' => 'Pas de réponse',   'color' => 'amber'],
        'positive' => ['label' => 'Réponse positive', 'color' => 'blue'],
        'negative' => ['label' => 'Réponse négative', 'color' => 'rose'],
    ];

    public function addScript()
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
        $this->dispatch('scripts-updated', flow: $script->flow, scriptId: (int)$script->id);
        session()->flash('script_message', 'Script créé avec succès.');
    }

    public function updateName(int $scriptId, string $name)
    {
        $script = SalesScript::findOrFail($scriptId);
        $script->update(['name' => trim($name)]);
        $this->dispatch('scripts-updated', flow: $script->flow, scriptId: (int)$scriptId);
    }

    public function initFlow(int $scriptId)
    {
        $script = SalesScript::findOrFail($scriptId);
        if ($script->flow) return;
        $sequences = $script->sequences ?: [''];
        $nodeIds = array_map(fn() => 'node_' . uniqid(), $sequences);
        $nodes = [];
        foreach ($sequences as $i => $content) {
            $nodeId = $nodeIds[$i];
            $branches = [];
            if (isset($nodeIds[$i + 1])) {
                $branches[] = [
                    'condition' => 'reply',
                    'label'     => 'Si réponse',
                    'color'     => 'emerald',
                    'next'      => $nodeIds[$i + 1],
                ];
            }
            $nodes[$nodeId] = ['id' => $nodeId, 'content' => $content, 'branches' => $branches];
        }
        $script->update(['flow' => ['root' => $nodeIds[0], 'nodes' => $nodes]]);
        $this->dispatch('scripts-updated', flow: ['root' => $nodeIds[0], 'nodes' => $nodes], scriptId: $scriptId);
    }

    public function updateNodeContent($scriptId, string $nodeId, string $content)
    {
        $script = SalesScript::find($scriptId);
        if (!$script) {
            \Log::error("Script non trouvé pour l'ID: " . $scriptId);
            return;
        }

        $flow = $script->flow;
        if (isset($flow['nodes'][$nodeId])) {
            $flow['nodes'][$nodeId]['content'] = $content;
            $script->update(['flow' => $flow]);
            \Log::info("Script '{$script->name}' mis à jour. Node: {$nodeId}");
            $this->dispatch('scripts-updated', flow: $flow, scriptId: (int)$scriptId);
        } else {
            \Log::warning("Node non trouvé: {$nodeId} dans le script " . $scriptId);
        }
    }

    public function addBranch(int $scriptId, string $nodeId, string $condition)
    {
        if (!array_key_exists($condition, self::BRANCH_CONDITIONS)) return;
        $script = SalesScript::findOrFail($scriptId);
        $flow = $script->flow;
        if (!isset($flow['nodes'][$nodeId])) return;
        $used = array_column($flow['nodes'][$nodeId]['branches'], 'condition');
        if (in_array($condition, $used)) return;
        $newNodeId = 'node_' . uniqid();
        $flow['nodes'][$newNodeId] = ['id' => $newNodeId, 'content' => '', 'branches' => []];
        $flow['nodes'][$nodeId]['branches'][] = [
            'condition' => $condition,
            'label'     => self::BRANCH_CONDITIONS[$condition]['label'],
            'color'     => self::BRANCH_CONDITIONS[$condition]['color'],
            'next'      => $newNodeId,
        ];
        $script->update(['flow' => $flow]);
        $this->dispatch('scripts-updated', flow: $flow, scriptId: (int)$scriptId);
    }

    public function deleteNode(int $scriptId, string $nodeId)
    {
        $script = SalesScript::findOrFail($scriptId);
        $flow = $script->flow;
        foreach ($flow['nodes'] as $parentId => $parentNode) {
            foreach ($parentNode['branches'] as $idx => $branch) {
                if ($branch['next'] === $nodeId) {
                    $this->deleteNodeTree($flow, $nodeId);
                    array_splice($flow['nodes'][$parentId]['branches'], $idx, 1);
                    $script->update(['flow' => $flow]);
                    $this->dispatch('scripts-updated', flow: $flow, scriptId: $scriptId);
                    return;
                }
            }
        }
    }

    public function deleteBranch(int $scriptId, string $nodeId, int $idx)
    {
        $script = SalesScript::findOrFail($scriptId);
        $flow = $script->flow;
        if (isset($flow['nodes'][$nodeId]['branches'][$idx])) {
            $targetId = $flow['nodes'][$nodeId]['branches'][$idx]['next'];
            $this->deleteNodeTree($flow, $targetId);
            array_splice($flow['nodes'][$nodeId]['branches'], $idx, 1);
            $script->update(['flow' => $flow]);
            $this->dispatch('scripts-updated', flow: $flow, scriptId: (int)$scriptId);
        }
    }

    public function deleteScript(int $scriptId)
    {
        SalesScript::findOrFail($scriptId)->delete();
        $this->dispatch('scripts-updated', flow: null, scriptId: (int)$scriptId);
    }

    public function duplicateScript(int $scriptId)
    {
        $original = SalesScript::findOrFail($scriptId);
        $new = $original->replicate();
        $new->name = $original->name . ' (Copie)';
        $new->save();
        session()->flash('script_message', 'Script dupliqué avec succès.');
    }

    private function deleteNodeTree(&$flow, $nodeId)
    {
        $node = $flow['nodes'][$nodeId] ?? null;
        if (!$node) return;
        foreach ($node['branches'] as $branch) $this->deleteNodeTree($flow, $branch['next']);
        unset($flow['nodes'][$nodeId]);
    }

    public function exportToTree($script): array
    {
        $flow = $script->flow;
        if (!$flow || !isset($flow['root']) || !isset($flow['nodes'][$flow['root']])) {
            return ['name' => $script->name, 'content' => '', 'branches' => []];
        }
        return ['name' => $script->name, ...$this->nodeToTreeNode($flow, $flow['root'])];
    }

    private function nodeToTreeNode($flow, $nodeId, $depth = 0): array
    {
        if ($depth > 20) return ['content' => '[Limite de profondeur atteinte]', 'branches' => []];
        $node = $flow['nodes'][$nodeId] ?? null;
        if (!$node) return ['content' => '', 'branches' => []];
        $treeNode = ['content' => $node['content'] ?? '', 'branches' => []];
        foreach ($node['branches'] ?? [] as $branch) {
            if (isset($flow['nodes'][$branch['next']])) {
                $treeNode['branches'][] = [
                    'condition' => $branch['condition'],
                    'message'   => $this->nodeToTreeNode($flow, $branch['next'], $depth + 1)
                ];
            }
        }
        return $treeNode;
    }

    public function handleImportAction()
    {
        if (empty($this->importJson)) {
            $this->addError('importJson', 'Veuillez coller le JSON du script.');
            return;
        }
        try {
            $data = json_decode($this->importJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) throw new \Exception('JSON invalide.');
            if (isset($data['content'])) {
                $nodes = [];
                $rootId = $this->treeNodeToNode($data, $nodes);
                $flow = ['root' => $rootId, 'nodes' => $nodes];
            } elseif (isset($data['flow'])) {
                $flow = $data['flow'];
            } elseif (isset($data['root']) && isset($data['nodes'])) {
                // If the user pasted the raw flow object without a wrapper
                $flow = $data;
            } else {
                throw new \Exception('Format non reconnu.');
            }
            $newScript = SalesScript::create(['name' => ($data['name'] ?? 'Importé') . ' (Importé)', 'flow' => $flow, 'sequences' => []]);
            session()->flash('script_message', 'Script importé avec succès.');
            $this->importJson = '';
            $this->dispatch('scripts-updated', flow: $flow, scriptId: (int)$newScript->id);
        } catch (\Exception $e) {
            $this->addError('importJson', 'Erreur : ' . $e->getMessage());
        }
    }

    private function treeNodeToNode($treeNode, &$nodes, $depth = 0): string
    {
        if ($depth > 20) return 'limited';
        $nodeId = 'node_' . uniqid();
        $nodes[$nodeId] = ['id' => $nodeId, 'content' => $treeNode['content'] ?? '', 'branches' => []];
        foreach ($treeNode['branches'] ?? [] as $branch) {
            if (isset($branch['message'])) {
                $nextNodeId = $this->treeNodeToNode($branch['message'], $nodes, $depth + 1);
                $cond = $branch['condition'];
                $meta = self::BRANCH_CONDITIONS[$cond] ?? ['label' => $cond, 'color' => 'gray'];
                $nodes[$nodeId]['branches'][] = ['condition' => $cond, 'label' => $meta['label'], 'color' => $meta['color'], 'next' => $nextNodeId];
            }
        }
        return $nodeId;
    }

    public function render(): mixed
    {
        return view('livewire.admin.prospect.script-flow-manager', [
            'scripts' => SalesScript::latest()->get()
        ]);
    }
}
