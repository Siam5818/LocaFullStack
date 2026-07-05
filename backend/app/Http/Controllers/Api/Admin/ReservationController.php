<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\StatutReservation;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreerContratRequest;
use App\Models\Reservation;
use App\Services\ContratService;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ContratService $contratService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            Reservation::with(['client.personne', 'propriete'])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function confirmer(Reservation $reservation): JsonResponse
    {
        $reservation->update(['statut' => StatutReservation::Confirmee]);

        return response()->json(['message' => 'Réservation confirmée.', 'reservation' => $reservation]);
    }

    public function annuler(Reservation $reservation): JsonResponse
    {
        $reservation->update(['statut' => StatutReservation::Annulee]);

        return response()->json(['message' => 'Réservation annulée.', 'reservation' => $reservation]);
    }

    public function creerContrat(CreerContratRequest $request, Reservation $reservation): JsonResponse
    {
        try {
            $contrat = $this->contratService->creerDepuisReservation(
                reservation: $reservation,
                typeContratId: $request->typecontrat_id,
                dateDebut: $request->date_debut,
                dateFin: $request->date_fin,
                montantTotal: $request->montant_total,
                modePaiementVente: $request->mode_paiement_vente,
            );

            return response()->json([
                'message' => 'Contrat créé et envoyé par e-mail au client.',
                'contrat' => $contrat,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
