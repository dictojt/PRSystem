<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * OTP email for superadmin re-auth. Sent synchronously so it works without a queue worker.
 * Configure MAIL_MAILER=smtp (and SMTP settings) in .env to receive real emails.
 */
class SuperadminOtpNotification extends Notification
{
    public function __construct(
        public string $otp
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your PRS Admin verification code')
            ->line('Your one-time verification code for an admin action is:')
            ->line('**' . $this->otp . '**')
            ->line('This code expires in 5 minutes. Do not share it.');
    }
}
