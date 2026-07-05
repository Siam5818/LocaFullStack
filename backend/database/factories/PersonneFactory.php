<?php

namespace Database\Factories;

use App\Models\Personne;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Personne>
 */
class PersonneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('Password1234'),
            'telephone' => fake()->numerify('77#######'),
            'role' => 'client',
            'is_active' => true,
            'email_verified_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function bailleur(): static
    {
        return $this->state(['role' => 'bailleur']);
    }

    public function client(): static
    {
        return $this->state(['role' => 'client']);
    }

    public function nonVerifie(): static
    {
        return $this->state(['email_verified_at' => null]);
    }

    public function inactif(): static
    {
        return $this->state(['is_active' => false]);
    }
}
