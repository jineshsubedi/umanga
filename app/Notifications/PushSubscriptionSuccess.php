<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PushSubscriptionSuccess extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [\NotificationChannels\WebPush\WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable)
    {
        return (new \NotificationChannels\WebPush\WebPushMessage)
            ->title('Subscription Successful!')
            ->icon('/icons/logo.png')
            ->options(['vibrate' => [200, 100, 200, 100, 200, 100, 200]])
            ->body('You will now receive push notifications.')
            ->action('View App', 'view_app');
    }
}
