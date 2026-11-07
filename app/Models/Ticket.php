<?php

namespace App\Models;

use App\Observers\TicketObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

#[ObservedBy([TicketObserver::class])]

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'number',
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
                $ticket->deleted_by_id = Auth::id();
                $ticket->saveQuietly();
            }
        });
    }

    public function isOverdue(): bool
    {
        if (! $this->due_date || $this->status === 'closed') {
            return false;
        }

        $todayInKolkata = now('Asia/Kolkata')->startOfDay();
        $dueDateInKolkata = Carbon::parse($this->due_date->format('Y-m-d'), 'Asia/Kolkata')->startOfDay();

        return $dueDateInKolkata->lt($todayInKolkata);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->isOverdue();
    }

    public function getReferenceCodeAttribute(): string
    {
        $prefix = $this->project?->code ?? 'TCK';
        $num = $this->number ?? $this->id;

        return sprintf('%s-%03d', $prefix, $num);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getNotificationRecipients(int $excludeUserId): Collection
    {
        $recipientsId = collect();

        // Creator
        if ($this->created_by_id) {
            $recipientsId->push($this->created_by_id);
        }

        // Assignee
        if ($this->assigned_to_id) {
            $recipientsId->push($this->assigned_to_id);
        }

        // All Wathcers
        $watchersId = $this->watchers()->pluck('users.id');
        $recipientsId = $recipientsId->concat($watchersId);

        // Commenters/Responders
        $commenterIds = $this->comments()->pluck('user_id');
        $recipientsId = $recipientsId->concat($commenterIds);

        // Filter duplicates
        $uniqueIds = $recipientsId->unique()->reject(fn ($id) => $id == $excludeUserId)->values();

        return User::whereIn('id', $uniqueIds)->get();
    }
}
