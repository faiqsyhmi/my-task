<?php

namespace App\Livewire\Routines;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\Routines\Models\Routine;
use Illuminate\Support\Carbon;

class RoutineList extends Component
{
    public ?int $energyFilter = null;
    public array $selectedRoutineIds = [];
    public bool $selectAll = false;

    /* ── Energy Filters ─────────────────────────── */

    public function setEnergy(?int $level): void
    {
        $this->energyFilter = ($this->energyFilter === $level) ? null : $level;
    }

    /* ── Routine Actions ────────────────────────────────── */

    public function toggleStatus(string $id): void
    {
        $routine = Routine::where('user_id', auth()->id())->findOrFail($id);
        
        if ($routine->is_completed) {
            // If already completed today, "uncheck" it by setting last_completed_at to yesterday or null
            $routine->update(['last_completed_at' => null]);
        } else {
            // Mark as completed today
            $routine->update(['last_completed_at' => now()]);
        }
    }

    public function deleteRoutine(string $id): void
    {
        Routine::where('user_id', auth()->id())->findOrFail($id)->delete();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedRoutineIds = $this->getRoutines()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedRoutineIds = [];
        }
    }

    public function updatedSelectedRoutineIds(): void
    {
        $allIds = $this->getRoutines()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->selectAll = count($this->selectedRoutineIds) === count($allIds) && count($allIds) > 0;
    }

    public function bulkDelete(): void
    {
        Routine::where('user_id', auth()->id())->whereIn('id', $this->selectedRoutineIds)->delete();
        $this->selectedRoutineIds = [];
        $this->selectAll = false;
    }

    public function bulkComplete(): void
    {
        Routine::where('user_id', auth()->id())->whereIn('id', $this->selectedRoutineIds)->update([
            'last_completed_at' => now(),
        ]);
        $this->selectedRoutineIds = [];
        $this->selectAll = false;
    }

    #[On('routine-added')]
    public function refresh(): void {}

    /* ── Query ───────────────────────────────────────────── */

    public function getRoutines()
    {
        $q = Routine::where('user_id', auth()->id())
                 ->active()
                 ->orderByRaw("CASE WHEN last_completed_at IS NOT NULL AND DATE(last_completed_at) = CURRENT_DATE THEN 1 ELSE 0 END")
                 ->orderBy('energy_level', 'desc')
                 ->orderBy('title');

        if ($this->energyFilter !== null) {
            $q->where('energy_level', $this->energyFilter);
        }

        return $q->get();
    }

    public function render()
    {
        return view('livewire.routines.routine-list', [
            'routines' => $this->getRoutines(),
        ]);
    }
}
