<?php

namespace App\Services;

use App\Enums\StatutPaiement;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaiementModel",
    title: "Structure d'un Paiement",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "contrat_id", type: "integer", example: 8),
        new OA\Property(property: "montant", type: "number", format: "float", example: 450.00),
        new OA\Property(property: "date_echeance", type: "string", format: "date", example: "2026-07-05"),
        new OA\Property(property: "statut", type: "string", example: "paye"),
        new OA\Property(property: "methode", type: "string", nullable: true, example: "carte_bancaire"),
        new OA\Property(property: "operateur", type: "string", nullable: true, example: "Stripe"),
        new OA\Property(property: "reference_transaction", type: "string", nullable: true, example: "ch_3MvXyL..."),
        new OA\Property(property: "date_paimement", type: "string", format: "date", nullable: true, example: "2026-06-22"),
        new OA\Property(property: "numero_recu", type: "string", nullable: true, example: "REC-2026-0084")
    ]
)]
class PaiementService
{
    public function __construct(
        private readonly RecuService $recuService,
    ) {}

    /**
     * Marque un paiement comme effectivement payé, et lui attribue
     * un numéro de reçu de façon atomique.
     */
    public function marquerPaye(
        Paiement $paiement,
        string $methode,
        ?string $operateur = null,
        ?string $referenceTransaction = null,
        ?string $datePaiement = null,
    ): Paiement {
        return DB::transaction(function () use (
            $paiement,
            $methode,
            $operateur,
            $referenceTransaction,
            $datePaiement,
        ) {
            $paiement->update([
                'statut'                 => StatutPaiement::Paye,
                'methode'                => $methode,
                'operateur'              => $operateur,
                'reference_transaction'  => $referenceTransaction,
                'date_paiement'          => $datePaiement ?? now()->toDateString(),
            ]);

            $this->recuService->genererNumeroRecu($paiement);

            return $paiement->refresh();
        });
    }

    public function marquerEnRetard(Paiement $paiement): Paiement
    {
        $paiement->update(['statut' => StatutPaiement::EnRetard]);
        return $paiement;
    }

    /**
     * Détecte automatiquement les paiements en retard (échéance dépassée,
     * encore en attente). Destiné à être appelé par une tâche planifiée (cron).
     */
    public function detecterRetards(): int
    {
        return Paiement::where('statut', StatutPaiement::EnAttente)
            ->whereDate('date_echeance', '<', now())
            ->update(['statut' => StatutPaiement::EnRetard]);
    }
}
