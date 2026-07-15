<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PreRegistrationVerificationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $verificationUrl,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirm Registration Email')
            ->line('Please verify your email to continue your registration request.')
            ->action('Verify Registration Email', $this->verificationUrl)
            ->line('Your registration will only proceed after this verification step.')
            ->line('If you did not initiate this registration, no further action is required.');
    }
}
