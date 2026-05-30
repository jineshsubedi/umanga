<?php

namespace App\Notifications;

use App\Models\MeetingMemo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MemoSubmitted extends Notification
{
    use Queueable;

    public function __construct(public MeetingMemo $memo) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'       => 'memo_submitted',
            'message'    => "{$this->memo->creator->name} submitted the memo \"{$this->memo->title}\" for review",
            'memo_id'    => $this->memo->id,
            'memo_title' => $this->memo->title,
            'url'        => '/memos/' . $this->memo->id,
            'actor_name' => $this->memo->creator->name,
        ];
    }
}
