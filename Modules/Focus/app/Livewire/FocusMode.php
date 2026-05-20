<?php

namespace Modules\Focus\Livewire;

use Livewire\Component;
use Modules\Tasks\Models\Task;
use Illuminate\Support\Facades\Auth;

class FocusMode extends Component
{
    public $selectedTaskId;
    public $activeTask;
    public $isFocusing = false;

    public function mount($taskId = null)
    {
        if ($taskId) {
            $this->activeTask = Task::find($taskId);
            if ($this->activeTask && $this->activeTask->user_id === Auth::id()) {
                $this->selectedTaskId = $taskId;
                $this->startFocus();
            }
        }

        if (!$this->activeTask) {
            // Try to find a flagged task or the first todo task
            $this->activeTask = Task::where('user_id', Auth::id())
                ->where('status', 'todo')
                ->orderBy('is_flagged', 'desc')
                ->first();

            if ($this->activeTask) {
                $this->selectedTaskId = $this->activeTask->id;
            }
        }
    }

    public function selectTask($taskId)
    {
        $this->selectedTaskId = $taskId;
        $this->activeTask = Task::find($taskId);
    }

    public function startFocus()
    {
        if ($this->activeTask) {
            $this->isFocusing = true;
            $this->activeTask->update(['status' => 'doing']);
        }
    }

    public function stopFocus()
    {
        $this->isFocusing = false;
    }

    public function completeTask()
    {
        if ($this->activeTask) {
            $this->activeTask->update([
                'status' => 'done',
                'completed_at' => now(),
            ]);
            $this->isFocusing = false;
            $this->mount(); // Refresh
        }
    }

    public function render()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->where('status', 'todo')
            ->get();

        return view('focus::livewire.focus-mode', [
            'tasks' => $tasks,
        ])->layout('focus::components.layouts.master');
    }
}
