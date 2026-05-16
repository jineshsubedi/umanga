<?php

namespace App\Notifications;

use App\Models\MeetingMinute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MinuteSubmitted extends Notification
{
    use Queueable;

    public function __construct(public MeetingMinute $minute) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'         => 'minute_submitted',
            'message'      => "{$this->minute->creator->name} submitted \"{$this->minute->title}\" for review",
            'minute_id'    => $this->minute->id,
            'minute_title' => $this->minute->title,
            'url'          => '/manager/meeting-minutes/' . $this->minute->id,
            'actor_name'   => $this->minute->creator->name,
        ];
    }
}
