<?php

namespace App\Notifications;

use App\Models\MeetingMinute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MinuteReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public MeetingMinute $minute,
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
            'type'         => 'minute_reviewed',
            'message'      => "{$this->reviewerName} {$verb} your minute \"{$this->minute->title}\"",
            'minute_id'    => $this->minute->id,
            'minute_title' => $this->minute->title,
            'status'       => $this->status,
            'comment'      => $this->comment,
            'url'          => '/client/meeting-minutes/' . $this->minute->id,
            'actor_name'   => $this->reviewerName,
        ];
    }
}
