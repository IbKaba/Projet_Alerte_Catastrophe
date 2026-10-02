<?php

namespace Database\Factories;

use App\Enums\Role;
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
            'phone' => fake()->optional()->phoneNumber(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Password123'),
            'role' => Role::CITIZEN,
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function moderator(): static
    {
        return $this->state(fn () => ['role' => Role::MODERATOR]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => Role::ADMIN]);
    }
}
