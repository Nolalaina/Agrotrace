<?php

namespace App\Events;

use App\Models\Intrant;
use Illuminate\Foundation\Events\Dispatchable;

class StockCritique
{
    use Dispatchable;

    public int $exploitationId;

    public function __construct(public Intrant $intrant)
    {
        $this->exploitationId = $intrant->exploitation_id;
    }
}
