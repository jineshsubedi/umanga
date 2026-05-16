<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MeetingMinuteAttachment;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function destroy(MeetingMinuteAttachment $attachment)
    {
        $minute = $attachment->meetingMinute;
        abort_if($minute->created_by !== auth()->id(), 403);
        abort_if($minute->status !== 'draft', 422, 'Cannot delete attachment from a submitted minute.');

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'Attachment removed successfully.');
    }
}
