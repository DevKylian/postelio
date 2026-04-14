<?php

namespace App\Livewire\Admin\Prospect;

use App\Models\SalesScript;
use Livewire\Component;

class ScriptManager extends Component
{
    public $scripts;
    public $editingScriptId = null;
    public $newName = '';

    public function mount()
    {
        $this->loadScripts();
    }

    public function loadScripts()
    {
        $this->scripts = SalesScript::latest()->get();
    }

    public function addScript()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
        ]);

        SalesScript::create([
            'name' => $this->newName,
            'sequences' => [''], // Initialize with one empty sequence
        ]);

        $this->newName = '';
        $this->loadScripts();
        $this->dispatch('scripts-updated');
        session()->flash('script_message', 'Script ajouté.');
    }

    public function addSequence($scriptId)
    {
        $script = SalesScript::findOrFail($scriptId);
        $sequences = $script->sequences;
        $sequences[] = '';
        $script->sequences = $sequences;
        $script->save();
        $this->loadScripts();
        $this->dispatch('scripts-updated');
    }

    public function removeSequence($scriptId, $index)
    {
        $script = SalesScript::findOrFail($scriptId);
        $sequences = $script->sequences;
        unset($sequences[$index]);
        $script->sequences = array_values($sequences);
        $script->save();
        $this->loadScripts();
        $this->dispatch('scripts-updated');
    }

    public function updateSequence($scriptId, $index, $value)
    {
        $script = SalesScript::findOrFail($scriptId);
        $sequences = $script->sequences;
        $sequences[$index] = $value;
        $script->sequences = $sequences;
        $script->save();
        // No full reload needed if we use wire:model.blur or similar, 
        // but for safety with JSON casts we reload.
        $this->loadScripts();
    }

    public function updateName($scriptId, $name)
    {
        $script = SalesScript::findOrFail($scriptId);
        $script->name = $name;
        $script->save();
        $this->loadScripts();
        $this->dispatch('scripts-updated');
    }

    public function deleteScript($scriptId)
    {
        SalesScript::findOrFail($scriptId)->delete();
        $this->loadScripts();
        $this->dispatch('scripts-updated');
    }

    public function render()
    {
        return view('livewire.admin.prospect.script-manager');
    }
}
