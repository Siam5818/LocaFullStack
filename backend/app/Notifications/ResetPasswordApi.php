<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordApi extends Notification
{
    use Queueable;

    public function __construct(private readonly string $url)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe — Location Immobilière')
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line('Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe.')
            ->action('Réinitialiser mon mot de passe', $this->url)
            ->line('Ce lien expire dans 60 minutes.')
            ->line("Si vous n'avez pas demandé de réinitialisation, ignorez ce message.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
