<?php

namespace Modules\Tasks\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasUuids;

    public const STATUSES = ['todo', 'doing', 'done'];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'energy_level',
        'status',
        'is_flagged',
        'due_at',
        'completed_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_flagged' => 'boolean',
        'energy_level' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Task $task): void {
            $task->attachments()->get()->each->delete();
        });
    }

    /**
     * Relationship: A task belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }

    /**
     * Scope: Easy wins (low energy).
     */
    public function scopeEasyWins(Builder $query): Builder
    {
        return $query->where('energy_level', '<=', 2)->where('status', 'todo');
    }

    /**
     * Scope: Filter by energy level.
     */
    public function scopeByEnergy(Builder $query, int $level): Builder
    {
        return $query->where('energy_level', $level);
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
