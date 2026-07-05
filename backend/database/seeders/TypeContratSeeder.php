<?php

namespace Database\Seeders;

use App\Models\TypeContrat;
use Illuminate\Database\Seeder;

class TypeContratSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['nom' => 'Bail location 1 an', 'duree_mois' => 12, 'taux_commission_defaut' => 10.00],
            ['nom' => 'Bail location 2 ans', 'duree_mois' => 24, 'taux_commission_defaut' => 8.00],
            ['nom' => 'Vente définitive', 'duree_mois' => null, 'taux_commission_defaut' => 5.00],
        ];

        foreach ($types as $type) {
            TypeContrat::firstOrCreate(['nom' => $type['nom']], $type);
        }
    }
}
