<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Favorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FavorieController extends Controller
{
    #[OA\Get(
        path: "/mes-favoris",
        summary: "Lister les favoris du client connecté",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: "Liste des favoris récupérée"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $favoris = Favorie::with('propriete.imagePrincipale')
            ->where('client_id', $request->user()->client->id)
            ->get();

        return response()->json($favoris);
    }

    #[OA\Post(
        path: "/favoris",
        summary: "Ajouter une propriété aux favoris",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["propriete_id"],
                properties: [new OA\Property(property: "propriete_id", type: "integer", example: 1)]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Ajouté aux favoris"),
            new OA\Response(response: 200, description: "Déjà présent dans les favoris"),
            new OA\Response(response: 422, description: "Données invalides ou propriété introuvable")
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'propriete_id' => ['required', 'integer', 'exists:proprietes,id'],
        ]);

        $clientId = $request->user()->client->id;

        $existant = Favorie::where('client_id', $clientId)
            ->where('propriete_id', $request->propriete_id)
            ->first();

        if ($existant) {
            return response()->json(['message' => 'Déjà dans vos favoris.', 'favorie' => $existant]);
        }

        $favorie = Favorie::create([
            'client_id'    => $clientId,
            'propriete_id' => $request->propriete_id,
        ]);

        return response()->json(['message' => 'Ajouté aux favoris.', 'favorie' => $favorie], 201);
    }

    #[OA\Delete(
        path: "/favoris/{favorie}",
        summary: "Retirer une propriété des favoris",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "favorie", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Retiré des favoris"),
            new OA\Response(response: 403, description: "Accès interdit"),
            new OA\Response(response: 404, description: "Favori introuvable")
        ]
    )]
    public function destroy(Request $request, Favorie $favorie): JsonResponse
    {
        if ($favorie->client_id !== $request->user()->client->id) {
            abort(403, 'Ce favori ne vous appartient pas.');
        }

        $favorie->delete();

        return response()->json(['message' => 'Retiré des favoris.']);
    }
}
