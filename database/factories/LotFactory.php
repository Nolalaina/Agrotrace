<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Lot;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Lot> */
class LotFactory extends Factory
{
    protected $model = Lot::class;

    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'code' => 'LOT-'.Str::upper(Str::random(8)),
            'poids_total_kg' => 0,
            'statut' => Lot::OUVERT,
        ];
    }
}
