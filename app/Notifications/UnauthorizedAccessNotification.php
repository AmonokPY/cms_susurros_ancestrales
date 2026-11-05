<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnauthorizedAccessNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $email,
        public readonly ?string $ip,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Intento de acceso no autorizado')
            ->line('Se detectó un intento de acceso al CMS que no cumple las políticas de autorización.')
            ->line('Correo involucrado: '.$this->email)
            ->line('IP: '.($this->ip ?: 'desconocida'));
    }
}
