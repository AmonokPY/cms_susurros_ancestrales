<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyAccountNotification extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirme su correo')
            ->view('emails.verify', [
                'url' => $url,
                'user' => $notifiable,
            ]);
    }
}
