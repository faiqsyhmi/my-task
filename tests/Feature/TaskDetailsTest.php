<?php

namespace Tests\Feature;

use App\Livewire\Tasks\TaskList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Tasks\Models\Task;
use Tests\TestCase;

class TaskDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_edit_existing_task_details(): void
    {
        $user = User::factory()->create();
        $task = $this->createTask($user, 'Old details');

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('startEditingDetails', $task->id)
            ->assertSet('editingDescription', 'Old details')
            ->set('editingDescription', 'Updated details')
            ->call('saveDetails')
            ->assertHasNoErrors()
            ->assertSet('editingTaskId', null);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'description' => 'Updated details',
        ]);
    }

    public function test_user_can_add_details_to_task_without_details(): void
    {
        $user = User::factory()->create();
        $task = $this->createTask($user);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('startEditingDetails', $task->id)
            ->assertSet('editingDescription', '')
            ->set('editingDescription', 'Details added later')
            ->call('saveDetails')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'description' => 'Details added later',
        ]);
    }

    private function createTask(User $user, ?string $description = null): Task
    {
        return Task::create([
            'user_id' => $user->id,
            'title' => 'Test task',
            'description' => $description,
            'energy_level' => 3,
            'status' => 'todo',
            'due_at' => today(),
        ]);
    }
}
