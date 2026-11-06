<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $event) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $copy = match ($this->event) {
            'created' => ['Cuenta creada', 'Su cuenta fue creada. Verifique su correo antes de acceder al CMS.'],
            'updated' => ['Cuenta actualizada', 'Los datos de su cuenta fueron actualizados.'],
            'deleted' => ['Cuenta eliminada', 'Su cuenta fue eliminada del sistema.'],
            default => ['Aviso de cuenta', 'Hubo un cambio en su cuenta.'],
        };

        return (new MailMessage)
            ->subject($copy[0])
            ->line($copy[1]);
    }
}
