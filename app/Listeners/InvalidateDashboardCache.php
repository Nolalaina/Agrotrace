<?php

namespace App\Listeners;

use App\Services\DashboardService;

/**
 * Enregistré explicitement dans AppServiceProvider pour 4 événements.
 * Le paramètre n'est pas typé afin d'éviter l'auto-découverte en double.
 */
class InvalidateDashboardCache
{
    public function __construct(private DashboardService $dashboard)
    {
    }

    public function handle($event): void
    {
        $this->dashboard->oublier($event->exploitationId);
    }
}
