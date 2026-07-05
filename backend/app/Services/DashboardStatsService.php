<?php

namespace App\Services;

use App\Enums\StatutReservation;
use App\Enums\StatutContrat;
use App\Enums\StatutPaiement;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Propriete;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AdminDashboardStats",
    title: "Statistiques du tableau de bord Admin",
    description: "Données globales d'activité pour la page d'accueil d'administration",
    properties: [
        new OA\Property(property: "reservations_en_attente", type: "integer", example: 5),
        new OA\Property(property: "contrats_actifs", type: "integer", example: 14),
        new OA\Property(property: "proprietes_disponibles", type: "integer", example: 42),
        new OA\Property(property: "nouveaux_clients_mois", type: "integer", example: 8),
        new OA\Property(property: "paiements_en_retard", type: "integer", example: 2),
        new OA\Property(property: "revenus_commission_mois", type: "number", format: "float", example: 1850.50)
    ]
)]
class DashboardStatsService
{
    public function statistiques(): array
    {
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        return [
            'reservations_en_attente'  => Reservation::where('statut', StatutReservation::EnAttente)->count(),
            'contrats_actifs'          => Contrat::where('statut', StatutContrat::Actif)->count(),
            'proprietes_disponibles'   => Propriete::count(),
            'nouveaux_clients_mois'    => Client::whereBetween('created_at', [$debutMois, $finMois])->count(),
            'paiements_en_retard'      => Paiement::where('statut', StatutPaiement::EnRetard)->count(),
            'revenus_commission_mois'  => Contrat::whereBetween('created_at', [$debutMois, $finMois])
                ->sum('montant_commission'),
        ];
    }
}
