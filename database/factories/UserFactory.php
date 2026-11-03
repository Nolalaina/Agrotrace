<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => \Illuminate\Support\Str::random(10),
            'exploitation_id' => Exploitation::factory(),
            'role' => User::ROLE_TECHNICIEN,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_ADMIN]);
    }

    public function responsable(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_RESPONSABLE]);
    }

    public function technicien(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_TECHNICIEN]);
    }
}
