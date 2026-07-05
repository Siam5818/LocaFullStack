<?php

namespace App\Http\Controllers\Api\Bailleur;

use App\Http\Controllers\Controller;
use App\Models\Propriete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProprieteController extends Controller
{
    #[OA\Get(
        path: "/bailleur/proprietes",
        summary: "Lister les propriétés appartenant au bailleur connecté",
        tags: ["Bailleur"],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: "Liste des propriétés récupérée"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $proprietes = Propriete::with(['typePropriete', 'service', 'images', 'equipements'])
            ->where('bailleur_id', $request->user()->bailleur->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($proprietes);
    }

    #[OA\Get(
        path: "/bailleur/proprietes/{propriete}",
        summary: "Afficher le détail complet d'une de ses propriétés (avec réservations)",
        tags: ["Bailleur"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "propriete", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Détails de la propriété récupérés"),
            new OA\Response(response: 403, description: "Accès interdit"),
            new OA\Response(response: 404, description: "Propriété introuvable")
        ]
    )]
    public function show(Request $request, Propriete $propriete): JsonResponse
    {
        $this->autoriserProprietaire($request, $propriete);

        return response()->json(
            $propriete->load(['typePropriete', 'service', 'images', 'equipements', 'reservations.client.personne'])
        );
    }

    private function autoriserProprietaire(Request $request, Propriete $propriete): void
    {
        if ($propriete->bailleur_id !== $request->user()->bailleur->id) {
            abort(403, 'Cette propriété ne vous appartient pas.');
        }
    }
}
