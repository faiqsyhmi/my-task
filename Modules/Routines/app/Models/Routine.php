<?php

namespace Modules\Routines\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Routine extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'energy_level',
        'estimated_minutes',
        'frequency',
        'last_completed_at',
        'is_active',
    ];

    protected $casts = [
        'last_completed_at' => 'datetime',
        'energy_level'      => 'integer',
        'estimated_minutes' => 'integer',
        'is_active'         => 'boolean',
    ];

    /**
     * Relationship: A routine belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the routine is completed for the current period.
     */
    public function getIsCompletedAttribute(): bool
    {
        if (!$this->last_completed_at) {
            return false;
        }

        if ($this->frequency === 'weekly') {
            return $this->last_completed_at->isCurrentWeek();
        }

        return $this->last_completed_at->isToday();
    }

    /**
     * Scope: Active routines.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
