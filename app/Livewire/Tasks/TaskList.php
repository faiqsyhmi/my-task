<?php

namespace App\Livewire\Tasks;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskAttachment;
use Modules\Tasks\Rules\SecureTaskAttachment;
use Throwable;

class TaskList extends Component
{
    use WithFileUploads;

    public string $statusFilter = 'all';

    public ?int $energyFilter = null;

    public ?string $dateFilter = null; // YYYY-MM-DD

    public array $expandedTaskIds = [];

    public array $selectedTaskIds = [];

    public bool $selectAll = false;

    public ?string $editingTaskId = null;

    public string $editingDescription = '';

    public ?string $attachmentTaskId = null;

    public array $pendingAttachments = [];

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
        $end = $start->copy()->endOfWeek();

        // Fetch all task dates for the current week in one query
        $taskDates = Task::where('user_id', auth()->id())
            ->whereBetween('due_at', [$start->toDateString(), $end->toDateString()])
            ->pluck('due_at')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->toArray();

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $dateStr = $day->toDateString();

            $days[] = [
                'date' => $dateStr,
                'label' => $day->format('D'),
                'num' => $day->format('j'),
                'isToday' => $day->isToday(),
                'hasTasks' => in_array($dateStr, $taskDates),
            ];
        }

        return $days;
    }

    public function prevWeek(): void
    {
        $this->weekOffset--;
    }

    public function nextWeek(): void
    {
        $this->weekOffset++;
    }

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

    public function setTaskStatus(string $id, string $status): void
    {
        if (! in_array($status, Task::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'The selected task status is invalid.',
            ]);
        }

        $task = Task::where('user_id', auth()->id())->findOrFail($id);
        $task->update([
            'status' => $status,
            'completed_at' => $status === 'done' ? now() : null,
        ]);
    }

    public function toggleFlag(string $id): void
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);
        $task->update(['is_flagged' => ! $task->is_flagged]);
    }

    public function deleteTask(string $id): void
    {
        Task::where('user_id', auth()->id())->findOrFail($id)->delete();

        if ($this->editingTaskId === $id) {
            $this->cancelEditingDetails();
        }
    }

    public function startAddingAttachments(string $id): void
    {
        Task::where('user_id', auth()->id())->findOrFail($id);

        $this->attachmentTaskId = $id;
        $this->pendingAttachments = [];
        $this->resetValidation('pendingAttachments');

        if (! in_array($id, $this->expandedTaskIds, true)) {
            $this->expandedTaskIds[] = $id;
        }
    }

    public function saveAttachments(): void
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($this->attachmentTaskId);

        $this->validate([
            'pendingAttachments' => [
                'required',
                'array',
                'min:1',
                'max:'.TaskAttachment::MAX_FILES_PER_UPLOAD,
            ],
            'pendingAttachments.*' => [
                'required',
                'file',
                'max:'.TaskAttachment::MAX_FILE_SIZE_KB,
                new SecureTaskAttachment,
            ],
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($task, &$storedPaths): void {
                DB::table('users')->where('id', auth()->id())->lockForUpdate()->first();

                $lockedTask = Task::where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->findOrFail($task->id);

                if ($lockedTask->attachments()->count() + count($this->pendingAttachments) > TaskAttachment::MAX_FILES_PER_TASK) {
                    throw ValidationException::withMessages([
                        'pendingAttachments' => 'A task may have no more than '.TaskAttachment::MAX_FILES_PER_TASK.' files.',
                    ]);
                }

                $usedBytes = TaskAttachment::query()
                    ->whereHas('task', fn ($query) => $query->where('user_id', auth()->id()))
                    ->sum('size');
                $pendingBytes = collect($this->pendingAttachments)->sum(fn ($file) => $file->getSize());

                if ($usedBytes + $pendingBytes > TaskAttachment::MAX_STORAGE_PER_USER_BYTES) {
                    throw ValidationException::withMessages([
                        'pendingAttachments' => 'Your attachment storage limit has been reached.',
                    ]);
                }

                foreach ($this->pendingAttachments as $file) {
                    $path = $file->store("task-attachments/{$lockedTask->id}", 'local');

                    if (! is_string($path)) {
                        throw new \RuntimeException('Attachment could not be stored.');
                    }

                    $storedPaths[] = $path;

                    $lockedTask->attachments()->create([
                        'disk' => 'local',
                        'path' => $path,
                        'original_name' => $this->safeOriginalName($file->getClientOriginalName()),
                        'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                        'size' => $file->getSize(),
                    ]);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);

            throw $exception;
        }

        $this->cancelAddingAttachments();
    }

    public function cancelAddingAttachments(): void
    {
        $this->attachmentTaskId = null;
        $this->pendingAttachments = [];
        $this->resetValidation('pendingAttachments');
    }

    public function deleteAttachment(string $id): void
    {
        TaskAttachment::query()
            ->whereHas('task', fn ($query) => $query->where('user_id', auth()->id()))
            ->findOrFail($id)
            ->delete();
    }

    public function startEditingDetails(string $id): void
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);

        $this->editingTaskId = (string) $task->id;
        $this->editingDescription = $task->description ?? '';
        $this->resetValidation('editingDescription');

        if (! in_array($id, $this->expandedTaskIds, true)) {
            $this->expandedTaskIds[] = $id;
        }
    }

    public function saveDetails(): void
    {
        $this->validate([
            'editingDescription' => ['nullable', 'string', 'max:1000'],
        ]);

        $task = Task::where('user_id', auth()->id())->findOrFail($this->editingTaskId);
        $description = trim($this->editingDescription);

        $task->update([
            'description' => $description !== '' ? $description : null,
        ]);

        $this->cancelEditingDetails();
    }

    public function cancelEditingDetails(): void
    {
        $this->editingTaskId = null;
        $this->editingDescription = '';
        $this->resetValidation('editingDescription');
    }

    public function toggleExpand(string $id): void
    {
        if (in_array($id, $this->expandedTaskIds)) {
            $this->expandedTaskIds = array_diff($this->expandedTaskIds, [$id]);
        } else {
            $this->expandedTaskIds[] = $id;
        }
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedTaskIds = $this->getTasks()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedTaskIds = [];
        }
    }

    public function updatedSelectedTaskIds(): void
    {
        $allIds = $this->getTasks()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->selectAll = count($this->selectedTaskIds) === count($allIds) && count($allIds) > 0;
    }

    public function bulkDelete(): void
    {
        Task::where('user_id', auth()->id())
            ->whereIn('id', $this->selectedTaskIds)
            ->get()
            ->each->delete();
        $this->selectedTaskIds = [];
        $this->selectAll = false;
    }

    public function bulkComplete(): void
    {
        Task::where('user_id', auth()->id())->whereIn('id', $this->selectedTaskIds)->update([
            'status' => 'done',
            'completed_at' => now(),
        ]);
        $this->selectedTaskIds = [];
        $this->selectAll = false;
    }

    public function bulkIncomplete(): void
    {
        Task::where('user_id', auth()->id())->whereIn('id', $this->selectedTaskIds)->update([
            'status' => 'todo',
            'completed_at' => null,
        ]);
        $this->selectedTaskIds = [];
        $this->selectAll = false;
    }

    #[On('task-added')]
    public function refresh(): void {} // triggers re-render

    /* ── Query ───────────────────────────────────────────── */

    public function getTasks()
    {
        $q = Task::with('attachments')
            ->where('user_id', auth()->id())
            ->orderByRaw("CASE status WHEN 'done' THEN 1 ELSE 0 END")
            ->orderBy('is_flagged', 'desc')
            ->orderBy('energy_level', 'desc')
            ->orderBy('due_at');

        if ($this->statusFilter === 'starred') {
            $q->where('is_flagged', true);
        } elseif ($this->statusFilter !== 'all') {
            $q->where('status', $this->statusFilter);
        }

        if ($this->energyFilter !== null) {
            $q->where('energy_level', $this->energyFilter);
        }

        if ($this->dateFilter && $this->statusFilter !== 'starred') {
            $q->whereDate('due_at', $this->dateFilter);
        }

        return $q->get();
    }

    private function safeOriginalName(string $name): string
    {
        $basename = basename(str_replace('\\', '/', $name));
        $sanitized = preg_replace('/[^\pL\pN._ -]/u', '_', $basename) ?: 'attachment';

        return Str::limit($sanitized, 200, '');
    }

    public function render()
    {
        return view('livewire.tasks.task-list', [
            'tasks' => $this->getTasks(),
            'weekDays' => $this->getWeekDays(),
            'monthLabel' => Carbon::now()->addWeeks($this->weekOffset)->format('F Y'),
        ]);
    }
}
