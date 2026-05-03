<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\Tasks\Models\Task;
use Illuminate\Support\Carbon;

class TaskList extends Component
{
    public string $statusFilter = 'all';
    public ?int   $energyFilter = null;
    public ?string $dateFilter  = null; // YYYY-MM-DD

    // Week navigation offset (0 = current week)
    public int $weekOffset = 0;

    public function mount(): void
    {
        $this->dateFilter = Carbon::today()->toDateString();
    }

    /* ── Calendar Helpers ─────────────────────────────────── */

    public function getWeekDays(): array
    {
        $start = Carbon::now()->startOfWeek()->addWeeks($this->weekOffset);
        $end   = $start->copy()->endOfWeek();
        
        // Fetch all task dates for the current week in one query
        $taskDates = Task::where('user_id', auth()->id())
            ->whereBetween('due_at', [$start->toDateString(), $end->toDateString()])
            ->pluck('due_at')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->toArray();

        $days  = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $dateStr = $day->toDateString();
            
            $days[] = [
                'date'     => $dateStr,
                'label'    => $day->format('D'),
                'num'      => $day->format('j'),
                'isToday'  => $day->isToday(),
                'hasTasks' => in_array($dateStr, $taskDates),
            ];
        }
        return $days;
    }

    public function prevWeek(): void { $this->weekOffset--; }
    public function nextWeek(): void { $this->weekOffset++; }

    public function filterByDate(string $date): void
    {
        $this->dateFilter = ($this->dateFilter === $date) ? null : $date;
    }

    /* ── Status / Energy Filters ─────────────────────────── */

    public function setStatus(string $status): void
    {
        $this->statusFilter = $status;
    }

    public function setEnergy(?int $level): void
    {
        $this->energyFilter = ($this->energyFilter === $level) ? null : $level;
    }

    /* ── Task Actions ────────────────────────────────────── */

    public function toggleStatus(string $id): void
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);
        if ($task->status === 'done') {
            $task->update(['status' => 'todo', 'completed_at' => null]);
        } else {
            $task->update(['status' => 'done', 'completed_at' => now()]);
        }
    }

    public function toggleFlag(string $id): void
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);
        $task->update(['is_flagged' => !$task->is_flagged]);
    }

    public function deleteTask(string $id): void
    {
        Task::where('user_id', auth()->id())->findOrFail($id)->delete();
    }

    #[On('task-added')]
    public function refresh(): void {} // triggers re-render

    /* ── Query ───────────────────────────────────────────── */

    public function getTasks()
    {
        $q = Task::where('user_id', auth()->id())
                 ->orderByRaw("CASE status WHEN 'done' THEN 1 ELSE 0 END")
                 ->orderBy('is_flagged', 'desc')
                 ->orderBy('energy_level', 'desc')
                 ->orderBy('due_at');

        if ($this->statusFilter !== 'all') {
            $q->where('status', $this->statusFilter);
        }

        if ($this->energyFilter !== null) {
            $q->where('energy_level', $this->energyFilter);
        }

        if ($this->dateFilter) {
            $q->whereDate('due_at', $this->dateFilter);
        }

        return $q->get();
    }

    public function render()
    {
        return view('livewire.tasks.task-list', [
            'tasks'    => $this->getTasks(),
            'weekDays' => $this->getWeekDays(),
            'monthLabel' => Carbon::now()->addWeeks($this->weekOffset)->format('F Y'),
        ]);
    }
}
