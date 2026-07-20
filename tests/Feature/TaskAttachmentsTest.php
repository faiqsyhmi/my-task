<?php

namespace Tests\Feature;

use App\Livewire\Tasks\TaskList;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Livewire;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskAttachment;
use Modules\Tasks\Rules\SecureTaskAttachment;
use Tests\TestCase;
use ZipArchive;

class TaskAttachmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_upload_and_download_allowed_attachment(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $task = $this->createTask($user);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('startAddingAttachments', $task->id)
            ->set('pendingAttachments', [UploadedFile::fake()->image('evidence.jpg')])
            ->call('saveAttachments')
            ->assertHasNoErrors();

        $attachment = TaskAttachment::firstOrFail();

        Storage::disk('local')->assertExists($attachment->path);
        $this->actingAs($user)
            ->get(route('tasks.attachments.download', $attachment))
            ->assertOk()
            ->assertDownload('evidence.jpg')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_disallowed_file_is_rejected(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $task = $this->createTask($user);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('startAddingAttachments', $task->id)
            ->set('pendingAttachments', [UploadedFile::fake()->create('payload.php', 10, 'text/x-php')])
            ->call('saveAttachments')
            ->assertHasErrors(['pendingAttachments.0']);

        $this->assertDatabaseCount('task_attachments', 0);
    }

    public function test_pdf_and_text_files_are_accepted_after_content_inspection(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $task = $this->createTask($user);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('startAddingAttachments', $task->id)
            ->set('pendingAttachments', [
                UploadedFile::fake()->createWithContent(
                    'report.pdf',
                    "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF",
                ),
                UploadedFile::fake()->createWithContent('notes.txt', 'Safe project notes.'),
            ])
            ->call('saveAttachments')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('task_attachments', 2);
    }

    public function test_valid_excel_file_passes_content_inspection(): void
    {
        $spreadsheetPath = $this->createXlsxFile();

        try {
            $validator = Validator::make([
                'file' => new UploadedFile(
                    $spreadsheetPath,
                    'budget.xlsx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    null,
                    true,
                ),
            ], [
                'file' => [new SecureTaskAttachment],
            ]);

            $this->assertTrue($validator->passes(), $validator->errors()->first('file'));
        } finally {
            if (is_file($spreadsheetPath)) {
                unlink($spreadsheetPath);
            }
        }
    }

    public function test_arbitrary_zip_renamed_as_excel_is_rejected(): void
    {
        $archivePath = tempnam(sys_get_temp_dir(), 'unsafe-xlsx-');
        $archive = new ZipArchive;
        $archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $archive->addFromString('payload.txt', 'Not a workbook');
        $archive->close();

        try {
            $validator = Validator::make([
                'file' => new UploadedFile($archivePath, 'fake.xlsx', 'application/zip', null, true),
            ], [
                'file' => [new SecureTaskAttachment],
            ]);

            $this->assertTrue($validator->fails());
        } finally {
            if (is_file($archivePath)) {
                unlink($archivePath);
            }
        }
    }

    public function test_per_task_attachment_limit_is_enforced(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $task = $this->createTask($user);

        foreach (range(1, TaskAttachment::MAX_FILES_PER_TASK) as $index) {
            $task->attachments()->create([
                'disk' => 'local',
                'path' => "existing/{$index}.txt",
                'original_name' => "{$index}.txt",
                'mime_type' => 'text/plain',
                'size' => 1,
            ]);
        }

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('startAddingAttachments', $task->id)
            ->set('pendingAttachments', [UploadedFile::fake()->image('extra.jpg')])
            ->call('saveAttachments')
            ->assertHasErrors(['pendingAttachments']);

        $this->assertDatabaseCount('task_attachments', TaskAttachment::MAX_FILES_PER_TASK);
    }

    public function test_account_attachment_storage_quota_is_enforced(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $task = $this->createTask($user);
        $task->attachments()->create([
            'disk' => 'local',
            'path' => 'existing/quota.txt',
            'original_name' => 'quota.txt',
            'mime_type' => 'text/plain',
            'size' => TaskAttachment::MAX_STORAGE_PER_USER_BYTES,
        ]);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('startAddingAttachments', $task->id)
            ->set('pendingAttachments', [UploadedFile::fake()->image('extra.jpg')])
            ->call('saveAttachments')
            ->assertHasErrors(['pendingAttachments']);

        $this->assertDatabaseCount('task_attachments', 1);
    }

    public function test_other_user_cannot_download_or_delete_attachment(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = $this->createTask($owner);
        Storage::disk('local')->put('task-attachments/file.txt', 'private');
        $attachment = $task->attachments()->create([
            'disk' => 'local',
            'path' => 'task-attachments/file.txt',
            'original_name' => 'file.txt',
            'mime_type' => 'text/plain',
            'size' => 7,
        ]);

        $this->actingAs($otherUser)
            ->get(route('tasks.attachments.download', $attachment))
            ->assertNotFound();

        try {
            Livewire::actingAs($otherUser)
                ->test(TaskList::class)
                ->call('deleteAttachment', $attachment->id);

            $this->fail('Unauthorized attachment deletion did not return a 404.');
        } catch (ModelNotFoundException) {
            // Owner-scoped lookup intentionally conceals attachment existence.
        }

        $this->assertDatabaseHas('task_attachments', ['id' => $attachment->id]);
        Storage::disk('local')->assertExists($attachment->path);
    }

    public function test_deleting_task_removes_private_files(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $task = $this->createTask($user);
        Storage::disk('local')->put('task-attachments/file.txt', 'private');
        $task->attachments()->create([
            'disk' => 'local',
            'path' => 'task-attachments/file.txt',
            'original_name' => 'file.txt',
            'mime_type' => 'text/plain',
            'size' => 7,
        ]);

        Livewire::actingAs($user)
            ->test(TaskList::class)
            ->call('deleteTask', $task->id);

        Storage::disk('local')->assertMissing('task-attachments/file.txt');
        $this->assertDatabaseCount('task_attachments', 0);
    }

    private function createTask(User $user): Task
    {
        return Task::create([
            'user_id' => $user->id,
            'title' => 'Task with files',
            'energy_level' => 3,
            'status' => 'todo',
            'due_at' => today(),
        ]);
    }

    private function createXlsxFile(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'valid-xlsx-');
        $archive = new ZipArchive;
        $archive->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $archive->addFromString(
            '[Content_Types].xml',
            '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>',
        );
        $archive->addFromString(
            'xl/workbook.xml',
            '<?xml version="1.0"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"/>',
        );
        $archive->close();

        return $path;
    }
}
