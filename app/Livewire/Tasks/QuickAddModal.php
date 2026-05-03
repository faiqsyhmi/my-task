<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use Modules\Tasks\Models\Task;
use Illuminate\Support\Carbon;

class QuickAddModal extends Component
{
    public bool   $open        = false;
    public string $title       = '';
    public string $description = '';
    public int    $energyLevel = 3;
    public string $dueAt       = '';

    protected array $rules = [
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'energyLevel' => 'required|integer|between:1,5',
        'dueAt'       => 'nullable|date',
    ];

    // Keyboard shortcut: Ctrl+Space opens modal
    #[\Livewire\Attributes\On('open-modal')]
    public function openModal(): void  { $this->open = true; }
    public function closeModal(): void { $this->reset(['open','title','description','energyLevel','dueAt']); $this->energyLevel = 3; }

    public function save(): void
    {
        $this->validate();

        Task::create([
            'user_id'      => auth()->id(),
            'title'        => trim($this->title),
            'description'  => $this->description ?: null,
            'energy_level' => $this->energyLevel,
            'status'       => 'todo',
            'due_at'       => $this->dueAt ? Carbon::parse($this->dueAt) : Carbon::today(),
        ]);

        $this->dispatch('task-added');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.tasks.quick-add-modal');
    }
}
