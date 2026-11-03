<?php

namespace Tests\Support;

use App\Models\Campagne;
use App\Models\Exploitation;
use App\Models\Recolte;
use App\Models\User;

trait CreatesAgroContext
{
    /** @return array{0: Exploitation, 1: User} */
    protected function contexte(string $role = 'responsable'): array
    {
        $exploitation = Exploitation::factory()->create();
        $user = User::factory()->{$role}()->create(['exploitation_id' => $exploitation->id]);

        return [$exploitation, $user];
    }

    protected function campagneEnCours(Exploitation $exploitation): Campagne
    {
        return Campagne::factory()->create(['exploitation_id' => $exploitation->id]);
    }

    protected function recolte(Exploitation $exploitation, float $kg = 500, ?Campagne $campagne = null): Recolte
    {
        $campagne ??= $this->campagneEnCours($exploitation);

        return Recolte::factory()->create([
            'exploitation_id' => $exploitation->id,
            'campagne_id' => $campagne->id,
            'quantite_kg' => $kg,
            'quantite_disponible_kg' => $kg,
        ]);
    }
}
