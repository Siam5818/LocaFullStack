<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipementFactory extends Factory
{
    protected $model = \App\Models\Equipement::class;

    public function definition(): array
    {
        return ['nom' => fake()->unique()->word(), 'icone' => fake()->word()];
    }
}
