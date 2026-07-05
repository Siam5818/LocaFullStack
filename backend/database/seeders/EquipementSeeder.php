<?php

namespace Database\Seeders;

use App\Models\Equipement;
use Illuminate\Database\Seeder;

class EquipementSeeder extends Seeder
{
    public function run(): void
    {
        $equipements = [
            ['nom' => 'Piscine', 'icone' => 'pool'],
            ['nom' => 'Garage', 'icone' => 'garage'],
            ['nom' => 'Jardin', 'icone' => 'tree'],
            ['nom' => 'Climatisation', 'icone' => 'snowflake'],
            ['nom' => 'Wifi', 'icone' => 'wifi'],
            ['nom' => 'Sécurité 24h', 'icone' => 'shield'],
            ['nom' => 'Ascenseur', 'icone' => 'elevator'],
            ['nom' => 'Meublé', 'icone' => 'sofa'],
        ];

        foreach ($equipements as $equipement) {
            Equipement::firstOrCreate(['nom' => $equipement['nom']], $equipement);
        }
    }
}
