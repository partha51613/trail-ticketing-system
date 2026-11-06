<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    public function show(Request $request, Attachment $attachment): BinaryFileResponse | StreamedResponse
    {
        $this->authorizeView($request, $attachment);

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'File not found on storage');
        }

        $fullPath = Storage::disk('local')->path($attachment->file_path);

        if ($attachment->isImage() || $attachment->mime_type === 'application/pdf') {
            return response()->file($fullPath, [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'inline; filename="' . addslashes($attachment->file_name) . '"',
            ]);
        }

        return response()->download($fullPath, $attachment->file_name);
    }

    public function thumbnail(Request $request, Attachment $attachment): BinaryFileResponse
    {
        $this->authorizeView($request, $attachment);
        $path = $attachment->thumbnail_path ?: $attachment->file_path;

        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }
        return response()->file(Storage::disk('local')->path($path), [
            'Cache-Control' => 'public, max-age=86400', // cache for 1 day in browser
        ]);
    }

    public function destroy(Request $request, Attachment $attachment)
    {
        // Must be author within 5 mins, or admin
        if ($request->user()->id !== $attachment->user_id && $request->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
        $attachment->delete();
        return back()->with('success', 'Attachment removed successfully.');
    }
    protected function authorizeView(Request $request, Attachment $attachment): void
    {
        // Authenticated users have read access
        if (! $request->user()) {
            abort(401);
        }
    }
}
