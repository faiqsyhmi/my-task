<?php

namespace App\Livewire\Routines;

use Livewire\Component;
use Modules\Routines\Models\Routine;

class QuickAddModal extends Component
{
    public bool   $open        = false;
    public string $title       = '';
    public string $description = '';
    public int    $energyLevel = 3;
    public string $frequency   = 'daily';

    protected array $rules = [
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'energyLevel' => 'required|integer|between:1,5',
        'frequency'   => 'required|string|in:daily,weekly',
    ];

    #[\Livewire\Attributes\On('open-modal')]
    public function openModal(): void  { $this->open = true; }
    public function closeModal(): void { $this->reset(['open','title','description','energyLevel','frequency']); $this->energyLevel = 3; }

    public function save(): void
    {
        $this->validate();

        Routine::create([
            'user_id'           => auth()->id(),
            'title'             => trim($this->title),
            'description'       => $this->description ?: null,
            'energy_level'      => $this->energyLevel,
            'frequency'         => $this->frequency,
            'is_active'         => true,
            'last_completed_at' => null,
        ]);

        $this->dispatch('routine-added');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.routines.quick-add-modal');
    }
}
