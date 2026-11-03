<?php

namespace Database\Factories;

use App\Models\Activite;
use App\Models\Campagne;
use App\Models\Exploitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Activite> */
class ActiviteFactory extends Factory
{
    protected $model = Activite::class;

    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'campagne_id' => fn (array $a) => Campagne::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'user_id' => fn (array $a) => User::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'type' => fake()->randomElement(Activite::TYPES),
            'date' => now()->subDays(fake()->numberBetween(1, 30))->toDateString(),
            'description' => fake()->sentence(),
            'cout' => fake()->randomFloat(2, 0, 200000),
        ];
    }
}
