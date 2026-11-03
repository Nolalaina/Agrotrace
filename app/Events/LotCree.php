<?php

namespace App\Events;

use App\Models\Lot;
use Illuminate\Foundation\Events\Dispatchable;

class LotCree
{
    use Dispatchable;

    public int $exploitationId;

    public function __construct(public Lot $lot)
    {
        $this->exploitationId = $lot->exploitation_id;
    }
}
