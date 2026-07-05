<?php

namespace App\Http\Controllers\Api\Client;

use App\Enums\StatutReservation;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReservationController extends Controller
{
    #[OA\Get(
        path: "/mes-reservations",
        summary: "Lister les réservations du client connecté",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: "Liste des réservations récupérée"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['propriete.imagePrincipale', 'contrat'])
            ->where('client_id', $request->user()->client->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reservations);
    }

    #[OA\Post(
        path: "/mes-reservations",
        summary: "Creer une reservation sur une propriete",
        tags: ["Client"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["propriete_id"],
                properties: [new OA\Property(property: "propriete_id", type: "integer", example: 1)]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Reservation creee"),
            new OA\Response(response: 422, description: "Propriete introuvable")
        ]
    )]
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $reservation = Reservation::create([
            'client_id'       => $request->user()->client->id,
            'propriete_id'    => $request->propriete_id,
            'statut'          => StatutReservation::EnAttente,
            'date_soumission' => now()->toDateString(),
        ]);

        return response()->json([
            'message'     => 'Réservation soumise avec succès.',
            'reservation' => $reservation,
        ], 201);
    }

    #[OA\Get(
        path: "/mes-reservations/{reservation}",
        summary: "Afficher une réservation spécifique",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "reservation", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Détails de la réservation"),
            new OA\Response(response: 403, description: "Accès interdit"),
            new OA\Response(response: 404, description: "Réservation introuvable")
        ]
    )]
    public function show(Request $request, Reservation $reservation): JsonResponse
    {
        $this->autoriserProprietaire($request, $reservation);

        return response()->json($reservation->load('propriete', 'contrat'));
    }

    #[OA\Delete(
        path: "/mes-reservations/{reservation}",
        summary: "Annuler une réservation en attente",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "reservation", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Réservation annulée"),
            new OA\Response(response: 422, description: "Impossible d'annuler cette réservation"),
            new OA\Response(response: 403, description: "Accès interdit")
        ]
    )]
    public function destroy(Request $request, Reservation $reservation): JsonResponse
    {
        $this->autoriserProprietaire($request, $reservation);

        if (! $reservation->estEnAttente()) {
            return response()->json([
                'message' => 'Seule une réservation en attente peut être annulée.',
            ], 422);
        }

        $reservation->delete();

        return response()->json(['message' => 'Réservation annulée.']);
    }

    private function autoriserProprietaire(Request $request, Reservation $reservation): void
    {
        if ($reservation->client_id !== $request->user()->client->id) {
            abort(403, 'Cette réservation ne vous appartient pas.');
        }
    }
}
