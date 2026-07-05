<?php

namespace Database\Factories;

use App\Models\Bailleur;
use App\Models\Service;
use App\Models\TypePropriete;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProprieteFactory extends Factory
{
    protected $model = \App\Models\Propriete::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->streetName() . ' - ' . fake()->word(),
            'rue' => fake()->streetAddress(),
            'quartier' => fake()->citySuffix(),
            'ville' => 'Dakar',
            'pays' => 'Sénégal',
            'nombre_piece' => fake()->numberBetween(1, 6),
            'dimension' => fake()->numberBetween(20, 400),
            'description' => fake()->paragraph(),
            'cout' => fake()->numberBetween(100000, 5000000),
            'typepropriete_id' => TypePropriete::factory(),
            'bailleur_id' => Bailleur::factory(),
            'service_id' => Service::factory(),
        ];
    }
}
