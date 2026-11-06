<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'attachable_type',
        'attachable_id',
        'user_id',
        'file_name',
        'file_path',
        'thumbnail_patch',
        'mime_type',
        'file_size',
    ];

    // Polymorphic relation to parent Ticket or Comment

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    // Attachment uploader
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Human readable filesize
    public function getFormattedSizeAttribute(): string
    {
        $bytes = (float) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }

    // Delete physical file when a comment is deleted
    protected static function booted(): void
    {
        static::deleted(function (Attachment $attachment) {
            if ($attachment->file_path && Storage::disk('local')->exists($attachment->file_path)) {
                Storage::disk('local')->delete($attachment->file_path);
            }
            if ($attachment->thumbnail_path && Storage::disk('local')->exists($attachment->thumbnail_path)) {
                Storage::disk('local')->delete($attachment->thumbnail_patch);
            }
        });
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}
