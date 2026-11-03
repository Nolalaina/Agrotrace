<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecolteNonAffecteeNotification extends Notification
{
    use Queueable;

    public function __construct(public int $nombre, public float $quantiteKg, public int $joursSeuil)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'recolte_non_affectee',
            'message' => sprintf(
                '%d récolte(s) (%s kg) ne sont affectées à aucun lot depuis plus de %d jours : risque de perte du fruit.',
                $this->nombre, number_format($this->quantiteKg, 2, ',', ' '), $this->joursSeuil
            ),
        ];
    }
}
