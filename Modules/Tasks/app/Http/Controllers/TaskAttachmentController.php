<?php

namespace Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Tasks\Models\TaskAttachment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskAttachmentController extends Controller
{
    public function download(string $attachment): StreamedResponse
    {
        $attachmentModel = TaskAttachment::query()
            ->whereHas('task', fn ($query) => $query->where('user_id', auth()->id()))
            ->findOrFail($attachment);

        abort_unless(Storage::disk($attachmentModel->disk)->exists($attachmentModel->path), 404);

        return Storage::disk($attachmentModel->disk)->download(
            $attachmentModel->path,
            $attachmentModel->original_name,
            [
                'Cache-Control' => 'private, no-store',
                'Content-Security-Policy' => "default-src 'none'; sandbox",
                'Content-Type' => $attachmentModel->mime_type,
                'X-Content-Type-Options' => 'nosniff',
            ],
        );
    }
}
