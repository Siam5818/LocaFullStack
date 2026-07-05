<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatutDemande;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDemandeRequest;
use App\Models\Demande;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class DemandeController extends Controller
{
    #[OA\Post(
        path: "/demandes",
        summary: "Soumettre une demande de contact ou de renseignement",
        tags: ["Authentification"],
        security: [], // Accessible publiquement, lie le compte si connecté
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nom", "email", "sujet", "message"],
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Dupont"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "jean@example.com"),
                    new OA\Property(property: "sujet", type: "string", example: "Demande de visite"),
                    new OA\Property(property: "message", type: "string", example: "Bonjour, je souhaiterais visiter le bien...")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Demande enregistrée avec succès"),
            new OA\Response(response: 422, description: "Données de formulaire invalides")
        ]
    )]
    public function store(StoreDemandeRequest $request): JsonResponse
    {
        $clientId = null;

        if ($request->user() && $request->user()->isClient()) {
            $clientId = $request->user()->client->id;
        }

        $demande = Demande::create([
            ...$request->validated(),
            'client_id' => $clientId,
            'statut'    => StatutDemande::Nouvelle,
        ]);

        return response()->json([
            'message' => 'Votre demande a bien été envoyée. Nous vous répondrons rapidement.',
            'demande' => $demande,
        ], 201);
    }
}
