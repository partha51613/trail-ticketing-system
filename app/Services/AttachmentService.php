<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    /**
     * Store the original file untouched, and generate a fast ~40KB thumbnail if it's an image.
     */
    public function storeAttachment(UploadedFile $file, Model $attachable, int $userId): Attachment
    {
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $datePath = date('Y/m');
        $uuid = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');

        // 1. Store the exact, uncompressed, pixel-perfect original file
        $storagePath = $file->storeAs("attachments/{$datePath}", "{$uuid}.{$extension}", 'local');

        // 2. Generate a lightweight thumbnail if it is a viewable image (JPEG, PNG, WEBP)
        $thumbnailPath = null;
        if (str_starts_with($mimeType, 'image/') && $extension !== 'gif') {
            $thumbnailPath = $this->generateThumbnail($file, $datePath, $uuid);
        }

        return $attachable->attachments()->create([
            'id'             => $uuid,
            'user_id'        => $userId,
            'file_name'      => $originalName,
            'file_path'      => $storagePath,
            'thumbnail_path' => $thumbnailPath,
            'mime_type'      => $mimeType,
            'file_size'      => $file->getSize(),
        ]);
    }

    /**
     * Create a fast ~40KB WebP/JPEG thumbnail (max width: 450px) using GD.
     */
    protected function generateThumbnail(UploadedFile $file, string $datePath, string $uuid): ?string
    {
        $srcPath = $file->getRealPath();
        $mime = $file->getMimeType();

        $srcImage = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($srcPath),
            'image/png'  => @imagecreatefrompng($srcPath),
            'image/webp' => @imagecreatefromwebp($srcPath),
            default      => false,
        };

        if (! $srcImage) {
            return null;
        }

        // Downscale to max 450px width for fast comment streams
        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);
        $maxThumbWidth = 450;

        if ($origWidth > $maxThumbWidth) {
            $thumbWidth = $maxThumbWidth;
            $thumbHeight = (int) round(($origHeight / $origWidth) * $maxThumbWidth);
        } else {
            $thumbWidth = $origWidth;
            $thumbHeight = $origHeight;
        }

        $thumbCanvas = imagecreatetruecolor($thumbWidth, $thumbHeight);

        // Preserve PNG transparency if needed
        imagealphablending($thumbCanvas, false);
        imagesavealpha($thumbCanvas, true);

        imagecopyresampled($thumbCanvas, $srcImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);
        imagedestroy($srcImage);

        // Encode as lightweight WebP (quality: 80)
        ob_start();
        if (function_exists('imagewebp')) {
            imagewebp($thumbCanvas, null, 80);
            $thumbExt = 'webp';
        } else {
            imagejpeg($thumbCanvas, null, 80);
            $thumbExt = 'jpg';
        }
        $thumbData = ob_get_clean();
        imagedestroy($thumbCanvas);

        $thumbStoragePath = "attachments/{$datePath}/{$uuid}_thumb.{$thumbExt}";
        Storage::disk('local')->put($thumbStoragePath, $thumbData);

        return $thumbStoragePath;
    }
}
