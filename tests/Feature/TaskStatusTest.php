<?php

namespace Tests\Feature;

use App\Livewire\Tasks\TaskList;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Tasks\Models\Task;
use Tests\TestCase;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_set_each_task_status(): void
    {
        $user = User::factory()->create();
        $task = $this->createTask($user);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('setTaskStatus', $task->id, 'doing');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'doing',
            'completed_at' => null,
        ]);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('setTaskStatus', $task->id, 'done');

        $task->refresh();
        $this->assertSame('done', $task->status);
        $this->assertNotNull($task->completed_at);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('setTaskStatus', $task->id, 'todo');

        $task->refresh();
        $this->assertSame('todo', $task->status);
        $this->assertNull($task->completed_at);
    }

    public function test_invalid_task_status_is_rejected(): void
    {
        $user = User::factory()->create();
        $task = $this->createTask($user);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('setTaskStatus', $task->id, 'archived')
            ->assertHasErrors('status');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'todo',
        ]);
    }

    public function test_user_cannot_change_another_users_task_status(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = $this->createTask($owner);

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($otherUser)
            ->test(TaskList::class)
            ->call('setTaskStatus', $task->id, 'done');
    }

    private function createTask(User $user): Task
    {
        return Task::create([
            'user_id' => $user->id,
            'title' => 'Test task',
            'energy_level' => 3,
            'status' => 'todo',
            'due_at' => today(),
        ]);
    }
}
