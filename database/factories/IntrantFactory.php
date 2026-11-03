<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Intrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Intrant> */
class IntrantFactory extends Factory
{
    protected $model = Intrant::class;

    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'nom' => fake()->unique()->words(2, true),
            'categorie' => 'engrais',
            'unite' => 'kg',
            'quantite_disponible' => 100,
            'seuil_minimum' => 20,
        ];
    }
}
