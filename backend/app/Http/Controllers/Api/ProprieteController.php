<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterProprieteRequest;
use App\Models\Propriete;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProprieteController extends Controller
{
    #[OA\Get(
        path: "/proprietes",
        summary: "Liste des proprietes avec filtres et pagination par curseur",
        tags: ["Proprietes"],
        parameters: [
            new OA\Parameter(name: "ville", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "prix_min", in: "query", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "prix_max", in: "query", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "type_id", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "service_id", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "piece_min", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "tri", in: "query", schema: new OA\Schema(type: "string", enum: ["recent", "prix_asc", "prix_desc"])),
            new OA\Parameter(name: "cursor", in: "query", schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Liste paginee des proprietes")
        ]
    )]
    public function index(FilterProprieteRequest $request): JsonResponse
    {
        $query = Propriete::query()
            ->with(['typePropriete', 'service', 'bailleur.personne', 'imagePrincipale', 'equipements']);

        if ($ville = $request->validated('ville')) {
            $query->where('ville', 'like', "%{$ville}%");
        }

        if ($prixMin = $request->validated('prix_min')) {
            $query->where('cout', '>=', $prixMin);
        }

        if ($prixMax = $request->validated('prix_max')) {
            $query->where('cout', '<=', $prixMax);
        }

        if ($typeId = $request->validated('type_id')) {
            $query->where('typepropriete_id', $typeId);
        }

        if ($serviceId = $request->validated('service_id')) {
            $query->where('service_id', $serviceId);
        }

        if ($pieceMin = $request->validated('piece_min')) {
            $query->where('nombre_piece', '>=', $pieceMin);
        }

        // Tri : toujours départagé par 'id' pour garantir un curseur stable.
        match ($request->validated('tri', 'recent')) {
            'prix_asc'  => $query->orderBy('cout', 'asc')->orderBy('id', 'asc'),
            'prix_desc' => $query->orderBy('cout', 'desc')->orderBy('id', 'desc'),
            default     => $query->orderBy('created_at', 'desc')->orderBy('id', 'desc'),
        };

        $proprietes = $query->cursorPaginate(12)->withQueryString();

        return response()->json($proprietes);
    }

    #[OA\Get(
        path: "/proprietes/{propriete}",
        summary: "Detail d'une propriete (avec note moyenne et nombre d'avis)",
        tags: ["Proprietes"],
        parameters: [
            new OA\Parameter(name: "propriete", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Detail de la propriete"),
            new OA\Response(response: 404, description: "Propriete introuvable")
        ]
    )]
    public function show(Propriete $propriete): JsonResponse
    {
        $propriete->load([
            'typePropriete',
            'service',
            'bailleur.personne',
            'images',
            'equipements',
            'notes.client.personne',
        ]);

        return response()->json([
            'propriete'     => $propriete,
            'note_moyenne'  => $propriete->noteMoyenne(),
            'nombre_avis'   => $propriete->notes()->count(),
        ]);
    }
}
