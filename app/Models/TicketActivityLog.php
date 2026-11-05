<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'field_changed',
        'old_value',
        'new_value',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function getFormattedFieldAttribute(): string
    {
        return match ($this->field_changed) {
            'assigned_to_id' => 'assignee',
            'project_id' => 'project',
            'due_date' => 'due_date',
            default => str_replace('_', ' ', $this->field_changed),
        };
    }

    // Resolve raw IDs into friendly names
    public function formatValue(?string $value): string
    {

        if ($value == null || $value == '') {
            return 'Unassigned';
        }

        if ($this->field_changed === 'assigned_to_id') {
            return User::withTrashed()->find($value)?->name ?? "User #{$value}";
        }

        if ($this->field_changed === 'project_id') {
            return Project::withTrashed()->find($value)?->name ?? "Project #{$value}";
        }

        if ($this->field_changed === 'status' || $this->field_changed === 'priority') {
            return ucwords(str_replace('_', ' ', $value));
        }

        return $value;
    }
}
