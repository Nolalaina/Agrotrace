<?php

namespace App\Listeners;

use App\Events\StockCritique;
use App\Models\Exploitation;
use App\Notifications\StockCritiqueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

/** Exécuté en file d'attente : l'envoi des notifications ne ralentit pas la requête. */
class NotifierStockCritique implements ShouldQueue
{
    public function handle(StockCritique $event): void
    {
        $responsables = Exploitation::find($event->exploitationId)?->responsables()->get();

        if ($responsables && $responsables->isNotEmpty()) {
            Notification::send($responsables, new StockCritiqueNotification($event->intrant));
        }
    }
}
