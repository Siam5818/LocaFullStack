<?php

namespace Database\Seeders;

use App\Models\Bailleur;
use App\Models\Equipement;
use App\Models\Propriete;
use App\Models\Service;
use App\Models\TypePropriete;
use Illuminate\Database\Seeder;

class ProprieteSeeder extends Seeder
{
    public function run(): void
    {
        $bailleurs = Bailleur::all();
        $typeVilla = TypePropriete::where('nom', 'Villa')->first();
        $typeAppart = TypePropriete::where('nom', 'Appartement')->first();
        $typeStudio = TypePropriete::where('nom', 'Studio')->first();
        $location = Service::where('nom', 'Location')->first();
        $vente = Service::where('nom', 'Vente')->first();

        $proprietes = [
            [
                'nom' => 'Villa Almadies vue mer',
                'rue' => 'Route des Almadies',
                'quartier' => 'Almadies',
                'ville' => 'Dakar',
                'nombre_piece' => 5,
                'dimension' => 250,
                'description' => 'Magnifique villa avec vue sur mer, proche de la plage.',
                'cout' => 850000,
                'typepropriete_id' => $typeVilla->id,
                'bailleur_id' => $bailleurs[0]->id,
                'service_id' => $location->id,
                'equipements' => ['Piscine', 'Garage', 'Jardin', 'Climatisation'],
            ],
            [
                'nom' => 'Appartement moderne Mermoz',
                'rue' => 'Avenue Cheikh Anta Diop',
                'quartier' => 'Mermoz',
                'ville' => 'Dakar',
                'nombre_piece' => 3,
                'dimension' => 90,
                'description' => 'Appartement rénové, proche des commodités.',
                'cout' => 350000,
                'typepropriete_id' => $typeAppart->id,
                'bailleur_id' => $bailleurs[0]->id,
                'service_id' => $location->id,
                'equipements' => ['Wifi', 'Climatisation', 'Ascenseur'],
            ],
            [
                'nom' => 'Studio meublé Plateau',
                'rue' => 'Rue Carnot',
                'quartier' => 'Plateau',
                'ville' => 'Dakar',
                'nombre_piece' => 1,
                'dimension' => 35,
                'description' => 'Studio idéal pour étudiant ou jeune actif.',
                'cout' => 150000,
                'typepropriete_id' => $typeStudio->id,
                'bailleur_id' => $bailleurs[1]->id,
                'service_id' => $location->id,
                'equipements' => ['Wifi', 'Meublé'],
            ],
            [
                'nom' => 'Villa de prestige Ngor',
                'rue' => 'Route de Ngor',
                'quartier' => 'Ngor',
                'ville' => 'Dakar',
                'nombre_piece' => 6,
                'dimension' => 400,
                'description' => 'Villa de standing à vendre, finitions haut de gamme.',
                'cout' => 95000000,
                'typepropriete_id' => $typeVilla->id,
                'bailleur_id' => $bailleurs[1]->id,
                'service_id' => $vente->id,
                'equipements' => ['Piscine', 'Garage', 'Jardin', 'Sécurité 24h'],
            ],
        ];

        foreach ($proprietes as $data) {
            $equipementsNoms = $data['equipements'];
            unset($data['equipements']);

            $propriete = Propriete::firstOrCreate(
                ['nom' => $data['nom']],
                $data
            );

            $equipementIds = Equipement::whereIn('nom', $equipementsNoms)->pluck('id');
            $propriete->equipements()->sync($equipementIds);
        }
    }
}
