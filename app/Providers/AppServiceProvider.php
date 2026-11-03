<?php

namespace App\Providers;

use App\Events\LotCree;
use App\Events\LotVendu;
use App\Events\RecolteEnregistree;
use App\Events\StockCritique;
use App\Listeners\InvalidateDashboardCache;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.agro');

        // Les Policies sont découvertes automatiquement (Parcelle → ParcellePolicy…).

        // Gates sans modèle associé
        Gate::define('voir-finances', fn (User $u) => $u->peutGerer());
        Gate::define('gerer-utilisateurs', fn (User $u) => $u->isAdmin());

        // Un même listener pour quatre événements (invalidation du cache du dashboard).
        Event::listen(
            [RecolteEnregistree::class, LotCree::class, LotVendu::class, StockCritique::class],
            InvalidateDashboardCache::class
        );

        // Journalisation de toute tentative d'accès interdit (Policy ou Gate refusée).
        Gate::after(function (?User $user, string $ability, ?bool $result) {
            if ($result === false) {
                Log::warning('Accès interdit', [
                    'user_id' => $user?->id,
                    'exploitation_id' => $user?->exploitation_id,
                    'ability' => $ability,
                ]);
            }
        });
    }
}
