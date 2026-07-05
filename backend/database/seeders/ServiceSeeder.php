<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Location', 'Vente'] as $nom) {
            Service::firstOrCreate(['nom' => $nom]);
        }
    }
}
