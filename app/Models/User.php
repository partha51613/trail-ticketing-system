<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function managesProject(int|string|Project $project): bool
    {
        if ($this->role !== 'manager') {
            return false;
        }

        $projectId = $project instanceof Project ? $project->id : (int) $project;

        return $this->managedProjects()->where('projects.id', $projectId)->exists();
    }

    public function managedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_manager')->withTimestamps();
    }

    public function ticketsCreated(): HasMany
    {
        return $this->hasMany(Ticket::class, 'created_by_id');
    }

    public function ticketsAssigned(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function watchedTickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_watchers')->withTimestamps();
    }
}
