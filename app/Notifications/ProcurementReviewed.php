<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class ProcurementReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    public $procurement;
    public $status;
    public $comment;
    public $reviewerName;

    public function __construct(ProcurementRequest $procurement, $status, $comment, $reviewerName)
    {
        $this->procurement = $procurement;
        $this->status = $status;
        $this->comment = $comment;
        $this->reviewerName = $reviewerName;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        
        if ($notifiable->email_notifications) {
            $channels[] = 'mail';
        }
        
        if ($notifiable->push_notifications) {
            $channels[] = WebPushChannel::class;
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Procurement Request ' . $this->status . ': ' . $this->procurement->request_number)
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your procurement request has been ' . $this->status . ' by ' . $this->reviewerName . '.')
                    ->line('Comment: ' . ($this->comment ?: 'No comment provided.'))
                    ->action('View Request', route('procurement.show', $this->procurement->id))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'procurement_reviewed',
            'procurement_id' => $this->procurement->id,
            'message' => 'Procurement request ' . $this->procurement->request_number . ' was ' . $this->status . ' by ' . $this->reviewerName,
            'actor_name' => $this->reviewerName,
            'status' => $this->status,
            'url' => route('procurement.show', $this->procurement->id),
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Procurement Request ' . ucfirst($this->status))
            ->icon('/images/icon-192x192.png')
            ->body('Request ' . $this->procurement->request_number . ' was ' . $this->status . ' by ' . $this->reviewerName)
            ->action('View', 'view')
            ->data(['url' => route('procurement.show', $this->procurement->id)]);
    }
}
