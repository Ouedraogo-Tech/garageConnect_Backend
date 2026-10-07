<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
            'technicien_id' => null,
            'client_id' => null,
        ]);
    }

    public function technicien(?int $technicienId = null): static
    {
        return $this->state(fn () => [
            'role' => 'technicien',
            'technicien_id' => $technicienId,
            'client_id' => null,
        ]);
    }

    public function client(?int $clientId = null): static
    {
        return $this->state(fn () => [
            'role' => 'client',
            'technicien_id' => null,
            'client_id' => $clientId,
        ]);
    }
}
