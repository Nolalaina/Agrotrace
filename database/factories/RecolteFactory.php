<?php

namespace Database\Factories;

use App\Models\Campagne;
use App\Models\Exploitation;
use App\Models\Recolte;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Recolte> */
class RecolteFactory extends Factory
{
    protected $model = Recolte::class;

    public function definition(): array
    {
        $quantite = fake()->randomFloat(2, 100, 800);

        return [
            'exploitation_id' => Exploitation::factory(),
            'campagne_id' => fn (array $attrs) => Campagne::factory()
                ->create(['exploitation_id' => $attrs['exploitation_id']])->id,
            'date_recolte' => now()->subDays(fake()->numberBetween(0, 20))->toDateString(),
            'quantite_kg' => $quantite,
            'quantite_disponible_kg' => $quantite,
            'calibre' => fake()->randomElement(Recolte::CALIBRES),
        ];
    }
}
