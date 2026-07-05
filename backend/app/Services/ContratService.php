<?php

namespace App\Services;

use App\Enums\StatutContrat;
use App\Mail\ContratSigneMail;
use App\Models\Contrat;
use App\Models\Reservation;
use App\Models\TypeContrat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ContratModel",
    title: "Structure d'un Contrat",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "reservation_id", type: "integer", example: 4),
        new OA\Property(property: "typecontrat_id", type: "integer", example: 2),
        new OA\Property(property: "date_debut", type: "string", format: "date", example: "2026-06-22"),
        new OA\Property(property: "date_fin", type: "string", format: "date", nullable: true, example: "2027-06-22"),
        new OA\Property(property: "montant_total", type: "number", format: "float", example: 1200.00),
        new OA\Property(property: "taux_commission_applique", type: "number", format: "float", example: 10.0),
        new OA\Property(property: "montant_commission", type: "number", format: "float", example: 120.00),
        new OA\Property(property: "mode_paiement_vente", type: "string", nullable: true, example: "unique"),
        new OA\Property(property: "statut", type: "string", example: "actif")
    ]
)]
class ContratService
{
    public function __construct(
        private readonly PdfService $pdfService,
    ) {}

    /**
     * Transforme une réservation confirmée en contrat signé.
     * Calcule et fige la commission au moment de la signature.
     */
    public function creerDepuisReservation(
        Reservation $reservation,
        int $typeContratId,
        string $dateDebut,
        ?string $dateFin,
        float $montantTotal,
        ?string $modePaiementVente = null,
    ): Contrat {
        if (! $reservation->estConfirmee()) {
            throw new InvalidArgumentException(
                'Seule une réservation confirmée peut être transformée en contrat.'
            );
        }

        if ($reservation->contrat()->exists()) {
            throw new InvalidArgumentException(
                'Cette réservation a déjà un contrat associé.'
            );
        }

        $typeContrat = TypeContrat::findOrFail($typeContratId);

        $tauxApplique = (float) $typeContrat->taux_commission_defaut;
        $montantCommission = round($montantTotal * $tauxApplique / 100, 2);

        $contrat = DB::transaction(function () use (
            $reservation,
            $typeContrat,
            $dateDebut,
            $dateFin,
            $montantTotal,
            $tauxApplique,
            $montantCommission,
            $modePaiementVente,
        ) {
            return Contrat::create([
                'reservation_id'            => $reservation->id,
                'typecontrat_id'            => $typeContrat->id,
                'date_debut'                => $dateDebut,
                'date_fin'                  => $dateFin,
                'montant_total'             => $montantTotal,
                'taux_commission_applique'  => $tauxApplique,
                'montant_commission'        => $montantCommission,
                'mode_paiement_vente'       => $modePaiementVente,
                'statut'                    => StatutContrat::Actif,
            ]);
        });

        $this->envoyerContratParEmail($contrat);

        return $contrat;
    }

    /**
     * Envoie le PDF du contrat signé par e-mail au client concerné.
     */
    public function envoyerContratParEmail(Contrat $contrat): void
    {
        $contrat->load('reservation.client.personne');

        $personne = $contrat->reservation->client->personne;
        $pdfContenu = $this->pdfService->genererContrat($contrat);
        $nomFichier = $this->pdfService->nomFichierContrat($contrat);

        Mail::to($personne->email)->send(
            new ContratSigneMail($contrat, $pdfContenu, $nomFichier)
        );
    }

    public function resilier(Contrat $contrat): Contrat
    {
        $contrat->update(['statut' => StatutContrat::Resilie]);
        return $contrat;
    }
}
