<?php

namespace App\Notifications;

use App\Models\MeetingMemo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MemoReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public MeetingMemo $memo,
        public string $status,
        public ?string $comment,
        public string $reviewerName,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $verb = $this->status === 'approved' ? 'approved' : 'rejected';
        return [
            'type'         => 'memo_reviewed',
            'message'      => "{$this->reviewerName} {$verb} your memo \"{$this->memo->title}\"",
            'memo_id'    => $this->memo->id,
            'memo_title' => $this->memo->title,
            'status'       => $this->status,
            'comment'      => $this->comment,
            'url'          => '/staff/meeting-memos/' . $this->memo->id,
            'actor_name'   => $this->reviewerName,
        ];
    }
}
