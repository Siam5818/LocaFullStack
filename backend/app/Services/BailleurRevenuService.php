<?php

namespace App\Services;

use App\Enums\StatutPaiement;
use App\Enums\StatutContrat;
use App\Models\Bailleur;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BailleurResumeRevenu",
    title: "Résumé des revenus du bailleur",
    description: "Structure des données financières retournée pour le tableau de bord d'un bailleur",
    properties: [
        new OA\Property(property: "total_brut_contrats", type: "number", format: "float", example: 15000.00),
        new OA\Property(property: "total_commission", type: "number", format: "float", example: 1500.00),
        new OA\Property(property: "total_paye_par_clients", type: "number", format: "float", example: 5000.00),
        new OA\Property(property: "net_percu_estime", type: "number", format: "float", example: 4500.00),
        new OA\Property(property: "en_attente", type: "number", format: "float", example: 1000.00),
        new OA\Property(property: "en_retard", type: "number", format: "float", example: 250.00),
        new OA\Property(property: "nombre_contrats_actifs", type: "integer", example: 3)
    ]
)]
class BailleurRevenuService
{
    /**
     * Résumé des revenus d'un bailleur : total net perçu, en attente, commission totale prélevée.
     */
    public function resume(Bailleur $bailleur): array
    {
        $contrats = $bailleur->proprietes()
            ->with('reservations.contrat.paiements')
            ->get()
            ->pluck('reservations')
            ->flatten()
            ->pluck('contrat')
            ->filter();

        $totalCommission = $contrats->sum('montant_commission');
        $totalBrut = $contrats->sum('montant_total');

        $paiements = $contrats->pluck('paiements')->flatten();

        $totalPaye = $paiements->where('statut', StatutPaiement::Paye)->sum('montant');
        $totalEnAttente = $paiements->where('statut', StatutPaiement::EnAttente)->sum('montant');
        $totalEnRetard = $paiements->where('statut', StatutPaiement::EnRetard)->sum('montant');

        // Montant net = ce qui revient réellement au bailleur après commission,
        // calculé proportionnellement sur ce qui a été payé.
        $proportionCommission = $totalBrut > 0 ? $totalCommission / $totalBrut : 0;
        $netPercu = $totalPaye * (1 - $proportionCommission);

        return [
            'total_brut_contrats'   => round($totalBrut, 2),
            'total_commission'      => round($totalCommission, 2),
            'total_paye_par_clients' => round($totalPaye, 2),
            'net_percu_estime'      => round($netPercu, 2),
            'en_attente'            => round($totalEnAttente, 2),
            'en_retard'             => round($totalEnRetard, 2),
            'nombre_contrats_actifs' => $contrats->where('statut', StatutContrat::Actif)->count(),
        ];
    }
}
