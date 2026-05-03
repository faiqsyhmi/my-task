<?php

namespace Modules\Tasks\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasUuids;

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
        'due_at'       => 'datetime',
        'completed_at' => 'datetime',
        'is_flagged'   => 'boolean',
        'energy_level' => 'integer',
    ];

    /**
     * Relationship: A task belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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