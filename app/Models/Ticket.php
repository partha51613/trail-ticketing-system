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

    /**
     * Scope a query to search across ticket title, key, description, project, and users.
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        $search = trim($search);

        return $query->where(function ($sub) use ($search) {
            if (is_numeric($search)) {
                $sub->orWhere('id', (int) $search)
                    ->orWhere('number', (int) $search);
            }

            if (str_contains($search, '-')) {
                [$codePart, $numPart] = explode('-', $search, 2);
                if (is_numeric(ltrim($numPart, '0')) || $numPart === '0' || is_numeric($numPart)) {
                    $numInt = (int) ltrim($numPart, '0');
                    $sub->orWhere(function ($codeSub) use ($codePart, $numInt) {
                        $codeSub->whereHas('project', function ($p) use ($codePart) {
                            $p->where('code', 'like', $codePart);
                        })->where(function ($nSub) use ($numInt) {
                            $nSub->where('number', $numInt)->orWhere('id', $numInt);
                        });
                    });
                }
            }

            $sub->orWhere('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('project', function ($p) use ($search) {
                    $p->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhereHas('creator', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('assignedTo', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Scope a query to filter tickets by status tab ('active', 'closed', 'deleted').
     */
    public function scopeForTab($query, string $tab)
    {
        return match ($tab) {
            'closed' => $query->where('status', 'closed'),
            'deleted' => $query->onlyTrashed(),
            default => $query->where('status', '!=', 'closed'),
        };
    }

    /**
     * Scope a query to apply standard ticket filters.
     */
    public function scopeFilterBy($query, array $filters)
    {
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['status']) && (!isset($filters['tab']) || $filters['tab'] === 'active')) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['assigned_to_id'])) {
            if ($filters['assigned_to_id'] === 'unassigned') {
                $query->whereNull('assigned_to_id');
            } else {
                $query->where('assigned_to_id', $filters['assigned_to_id']);
            }
        }

        if (!empty($filters['watched']) && auth()->check()) {
            $query->whereHas('watchers', function ($sub) {
                $sub->where('users.id', auth()->id());
            });
        }

        if (!empty($filters['created_by_id'])) {
            $query->where('created_by_id', $filters['created_by_id']);
        }

        return $query;
    }
}
