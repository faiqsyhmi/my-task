<?php

namespace Modules\Tasks\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TaskAttachment extends Model
{
    use HasUuids;

    public const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'pdf',
        'xlsx',
        'txt',
    ];

    public const MAX_FILES_PER_UPLOAD = 5;

    public const MAX_FILES_PER_TASK = 10;

    public const MAX_FILE_SIZE_KB = 10 * 1024;

    public const MAX_STORAGE_PER_USER_BYTES = 500 * 1024 * 1024;

    protected $fillable = [
        'task_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (TaskAttachment $attachment): void {
            Storage::disk($attachment->disk)->delete($attachment->path);
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
