<?php

namespace App\Notifications;

use App\Models\MeetingMemo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class MemoReviewed extends Notification implements ShouldQueue
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
        $channels = [];
        if ($notifiable->database_notifications) {
            $channels[] = 'database';
        }
        if ($notifiable->email_notifications) {
            $channels[] = 'mail';
        }
        if ($notifiable->push_notifications) {
            $channels[] = WebPushChannel::class;
        }
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $verb = $this->status === 'approved' ? 'approved' : 'rejected';
        $message = (new MailMessage)
            ->subject('Memo ' . ucfirst($verb))
            ->line("{$this->reviewerName} {$verb} the memo \"{$this->memo->title}\".");
            
        if ($this->comment) {
            $message->line("Comment: {$this->comment}");
        }
            
        return $message
            ->action('View Memo', url('/memos/' . $this->memo->id))
            ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable): array
    {
        $verb = $this->status === 'approved' ? 'approved' : 'rejected';
        return [
            'type'         => 'memo_reviewed',
            'message'      => "{$this->reviewerName} {$verb} the memo \"{$this->memo->title}\"",
            'memo_id'    => $this->memo->id,
            'memo_title' => $this->memo->title,
            'status'       => $this->status,
            'comment'      => $this->comment,
            'url'          => '/memos/' . $this->memo->id,
            'actor_name'   => $this->reviewerName,
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $verb = $this->status === 'approved' ? 'approved' : 'rejected';
        $body = "{$this->reviewerName} {$verb} the memo \"{$this->memo->title}\".";
        
        if ($this->comment) {
            $body .= "\nComment: {$this->comment}";
        }
        
        return (new WebPushMessage)
            ->title('Memo ' . ucfirst($verb))
            ->icon('/icons/logo.png')
            ->options(['vibrate' => [200, 100, 200, 100, 200, 100, 200]])
            ->body($body)
            ->action('View Memo', '/memos/' . $this->memo->id)
            ->data(['url' => '/memos/' . $this->memo->id]);
    }
}
