<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefaultPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $defaultPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your PRS default password')
            ->line('Your password reset request has been approved by an administrator.')
            ->line('Your new default password is: **' . $this->defaultPassword . '**')
            ->line('Please sign in and change your password if the system allows.')
            ->line('Do not share this password with anyone.');
    }
}
