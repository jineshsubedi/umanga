<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MeetingMemoAttachment;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function destroy(MeetingMemoAttachment $attachment)
    {
        $memo = $attachment->meetingMemo;
        abort_if($memo->created_by !== auth()->id(), 403);
        abort_if($memo->status !== 'draft', 422, 'Cannot delete attachment from a submitted memo.');

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'Attachment removed successfully.');
    }
}
