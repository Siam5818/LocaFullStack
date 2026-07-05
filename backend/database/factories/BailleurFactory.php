<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Personne;
use Illuminate\Database\Eloquent\Factories\Factory;

class BailleurFactory extends Factory
{
    protected $model = \App\Models\Bailleur::class;

    public function definition(): array
    {
        return [
            'personne_id' => Personne::factory()->bailleur(),
            'created_by_admin_id' => Admin::factory(),
        ];
    }
}
