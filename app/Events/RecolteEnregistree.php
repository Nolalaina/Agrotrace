<?php

namespace App\Events;

use App\Models\Recolte;
use Illuminate\Foundation\Events\Dispatchable;

class RecolteEnregistree
{
    use Dispatchable;

    public int $exploitationId;

    public function __construct(public Recolte $recolte)
    {
        $this->exploitationId = $recolte->exploitation_id;
    }
}
