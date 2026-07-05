<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailApi extends BaseVerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Vérifiez votre adresse e-mail — Location Immobilière')
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line('Merci de vous être inscrit. Veuillez confirmer votre adresse e-mail.')
            ->action('Vérifier mon e-mail', $url)
            ->line('Ce lien expire dans 60 minutes.')
            ->line("Si vous n'avez pas créé de compte, ignorez ce message.");
    }
}
