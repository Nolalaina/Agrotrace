<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Parcelle;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Parcelle> */
class ParcelleFactory extends Factory
{
    protected $model = Parcelle::class;

    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'code' => 'P'.fake()->unique()->numerify('###'),
            'nom' => 'Parcelle '.fake()->word(),
            'superficie_ha' => fake()->randomFloat(2, 0.5, 6),
            'localisation' => fake()->streetName(),
            'variete' => fake()->randomElement(['Mauritius', 'Tai So', 'Wai Chee']),
            'etat' => 'productive',
            'date_creation' => now()->subYears(5)->toDateString(),
        ];
    }
}
