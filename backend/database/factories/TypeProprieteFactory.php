<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TypeProprieteFactory extends Factory
{
    protected $model = \App\Models\TypePropriete::class;

    public function definition(): array
    {
        return ['nom' => fake()->unique()->word(), 'description' => fake()->sentence()];
    }
}
