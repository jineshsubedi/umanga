<?php

namespace App\Notifications;

use App\Models\MeetingMemo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class MemoSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MeetingMemo $memo) {}

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
        return (new MailMessage)
            ->subject('Memo Submitted for Review')
            ->line("{$this->memo->creator->name} submitted the memo \"{$this->memo->title}\" for review.")
            ->action('View Memo', url('/memos/' . $this->memo->id))
            ->line('Thank you for using our application!');
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

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Memo Submitted for Review')
            ->icon('/icons/logo.png')
            ->options(['vibrate' => [200, 100, 200, 100, 200, 100, 200]])
            ->body("{$this->memo->creator->name} submitted the memo \"{$this->memo->title}\" for review.")
            ->action('View Memo', '/memos/' . $this->memo->id)
            ->data(['url' => '/memos/' . $this->memo->id]);
    }
}
