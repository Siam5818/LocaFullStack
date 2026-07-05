<?php

namespace Database\Seeders;

use App\Models\TypePropriete;
use Illuminate\Database\Seeder;

class TypeProprieteSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['nom' => 'Appartement', 'description' => 'Logement dans un immeuble collectif'],
            ['nom' => 'Villa', 'description' => 'Maison individuelle avec jardin'],
            ['nom' => 'Studio', 'description' => 'Petit logement une pièce'],
            ['nom' => 'Duplex', 'description' => 'Logement sur deux niveaux'],
            ['nom' => 'Bureau', 'description' => 'Espace professionnel'],
            ['nom' => 'Terrain', 'description' => 'Terrain nu, constructible ou non'],
        ];

        foreach ($types as $type) {
            TypePropriete::firstOrCreate(['nom' => $type['nom']], $type);
        }
    }
}
