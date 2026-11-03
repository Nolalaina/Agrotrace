<?php

namespace App\Notifications;

use App\Models\Intrant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Canaux : « database » (alerte visible dans l'application) et « mail » (le
 * responsable est prévenu même hors de l'application ; MAIL_MAILER=log en développement).
 */
class StockCritiqueNotification extends Notification
{
    use Queueable;

    public function __construct(public Intrant $intrant)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Alerte stock critique : '.$this->intrant->nom)
            ->line($this->message());
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'stock_critique',
            'intrant_id' => $this->intrant->id,
            'message' => $this->message(),
        ];
    }

    private function message(): string
    {
        return sprintf(
            'Le stock de « %s » (%s %s) est inférieur ou égal au seuil minimum (%s %s).',
            $this->intrant->nom, $this->intrant->quantite_disponible, $this->intrant->unite,
            $this->intrant->seuil_minimum, $this->intrant->unite
        );
    }
}
