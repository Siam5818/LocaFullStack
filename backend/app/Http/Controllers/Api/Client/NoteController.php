<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Models\Note;
use App\Models\Propriete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class NoteController extends Controller
{
    #[OA\Get(
        path: "/proprietes/{propriete}/notes",
        summary: "Liste publique des avis sur une propriété",
        tags: ["Client"],
        security: [], // Pas d'authentification requise
        parameters: [
            new OA\Parameter(name: "propriete", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Liste des avis récupérée")
        ]
    )]
    public function index(Propriete $propriete): JsonResponse
    {
        $notes = $propriete->notes()->with('client.personne')->orderBy('created_at', 'desc')->get();

        return response()->json($notes);
    }

    #[OA\Post(
        path: "/proprietes/{propriete}/notes",
        summary: "Publier un avis sur une propriété",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "propriete", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["note"],
                properties: [
                    new OA\Property(property: "note", type: "integer", minimum: 1, maximum: 5, example: 5),
                    new OA\Property(property: "commentaire", type: "string", example: "Superbe logement, très propre !")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Avis publié"),
            new OA\Response(response: 422, description: "Un avis existe déjà ou données invalides"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function store(StoreNoteRequest $request, Propriete $propriete): JsonResponse
    {
        $clientId = $request->user()->client->id;

        $existante = Note::where('client_id', $clientId)
            ->where('propriete_id', $propriete->id)
            ->first();

        if ($existante) {
            return response()->json([
                'message' => 'Vous avez déjà laissé un avis pour cette propriété. Modifiez-le plutôt.',
            ], 422);
        }

        $note = Note::create([
            'client_id'    => $clientId,
            'propriete_id' => $propriete->id,
            'note'         => $request->note,
            'commentaire'  => $request->commentaire,
        ]);

        return response()->json(['message' => 'Avis publié.', 'note' => $note], 201);
    }

    #[OA\Put(
        path: "/notes/{note}",
        summary: "Modifier son avis",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "note", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["note"],
                properties: [
                    new OA\Property(property: "note", type: "integer", minimum: 1, maximum: 5, example: 4),
                    new OA\Property(property: "commentaire", type: "string", example: "Quelques coupures d'eau mais globalement satisfait.")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Avis mis à jour"),
            new OA\Response(response: 403, description: "Accès interdit"),
            new OA\Response(response: 422, description: "Données invalides")
        ]
    )]
    public function update(StoreNoteRequest $request, Note $note): JsonResponse
    {
        $this->autoriserProprietaire($request, $note);

        $note->update($request->validated());

        return response()->json(['message' => 'Avis mis à jour.', 'note' => $note]);
    }

    #[OA\Delete(
        path: "/notes/{note}",
        summary: "Supprimer son avis",
        tags: ["Client"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "note", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Avis supprimé"),
            new OA\Response(response: 403, description: "Accès interdit"),
            new OA\Response(response: 404, description: "Avis introuvable")
        ]
    )]
    public function destroy(Request $request, Note $note): JsonResponse
    {
        $this->autoriserProprietaire($request, $note);

        $note->delete();

        return response()->json(['message' => 'Avis supprimé.']);
    }

    private function autoriserProprietaire(Request $request, Note $note): void
    {
        if ($note->client_id !== $request->user()->client->id) {
            abort(403, 'Cet avis ne vous appartient pas.');
        }
    }
}
