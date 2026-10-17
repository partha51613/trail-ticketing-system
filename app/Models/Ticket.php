<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Project;
use App\Models\User;
class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'project_id',
        'created_by_id',
        'assigned_to_id',
        'status',
        'priority',
        'due_date',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id')->withTrashed();
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id')->withTrashed();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function watchers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_watchers')->withTimestamps();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(TicketActivityLog::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id')->withTrashed();
    }

    protected static function booted(): void
    {
        static::deleting(function (Ticket $ticket) {
            if (! $ticket->isForceDeleting()) {
                $ticket->deleted_by_id = auth()->id();
                $ticket->saveQuietly();
            }
        });
    }
}
