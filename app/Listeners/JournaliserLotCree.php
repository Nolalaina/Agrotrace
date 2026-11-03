<?php

namespace App\Listeners;

use App\Events\LotCree;
use Illuminate\Support\Facades\Log;

class JournaliserLotCree
{
    public function handle(LotCree $event): void
    {
        Log::info('Lot créé', [
            'lot' => $event->lot->code,
            'exploitation_id' => $event->exploitationId,
            'poids_kg' => $event->lot->poids_total_kg,
            'user_id' => $event->lot->user_id,
        ]);
    }
}
