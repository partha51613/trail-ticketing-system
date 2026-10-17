<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Ticket;
use App\Models\User;
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
}
