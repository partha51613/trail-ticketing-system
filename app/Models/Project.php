<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Project;
use App\Models\Ticket;
class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'closed_by_id',
        'closed_at',
        'deleted_by_id',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_id')->withTrashed();
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_manager')->withTimestamps();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id')->withTrashed();
    }

    protected static function booted(): void
    {
        static::deleting(function (Project $project) {
            if (! $project->isForceDeleting()) {
                $project->deleted_by_id = Auth::id();
                $project->saveQuietly();
            }
        });
    }

    public function code(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? strtoupper(trim($value)) : null,
        );
    }
}
