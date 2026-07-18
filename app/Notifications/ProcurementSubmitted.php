<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class ProcurementSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $procurement;

    public function __construct(ProcurementRequest $procurement)
    {
        $this->procurement = $procurement;
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
                    ->subject('New Procurement Request: ' . $this->procurement->request_number)
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('A new procurement request has been submitted by ' . $this->procurement->requester->name . ' and requires your review.')
                    ->line('Item: ' . $this->procurement->item_name)
                    ->action('View Request', route('procurement.show', $this->procurement->id))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'procurement_submitted',
            'procurement_id' => $this->procurement->id,
            'message' => 'New procurement request ' . $this->procurement->request_number . ' submitted by ' . $this->procurement->requester->name,
            'actor_id' => $this->procurement->requested_by,
            'actor_name' => $this->procurement->requester->name,
            'url' => route('procurement.show', $this->procurement->id),
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('New Procurement Request')
            ->icon('/images/icon-192x192.png')
            ->body('Procurement request ' . $this->procurement->request_number . ' requires your review.')
            ->action('View', 'view')
            ->data(['url' => route('procurement.show', $this->procurement->id)]);
    }
}
