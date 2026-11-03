<?php

namespace Database\Factories;

use App\Models\Campagne;
use App\Models\Exploitation;
use App\Models\Parcelle;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Campagne> */
class CampagneFactory extends Factory
{
    protected $model = Campagne::class;

    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            // La parcelle appartient à la même exploitation que la campagne.
            'parcelle_id' => fn (array $attrs) => Parcelle::factory()
                ->create(['exploitation_id' => $attrs['exploitation_id']])->id,
            'libelle' => 'Campagne '.fake()->year(),
            'date_debut' => now()->subMonths(3)->toDateString(),
            'date_fin_prevue' => now()->addMonths(3)->toDateString(),
            'statut' => Campagne::EN_COURS,
        ];
    }
}
