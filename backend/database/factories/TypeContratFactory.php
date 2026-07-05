<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TypeContratFactory extends Factory
{
    protected $model = \App\Models\TypeContrat::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->word(),
            'duree_mois' => 12,
            'taux_commission_defaut' => 10,
        ];
    }
}
