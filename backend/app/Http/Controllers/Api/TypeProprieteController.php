<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypePropriete;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TypeProprieteController extends Controller
{
    #[OA\Get(
        path: "/typeproprietes",
        summary: "Lister tous les types de biens (Appartement, Villa...)",
        tags: ["Propriétés"],
        security: [],
        responses: [
            new OA\Response(response: 200, description: "Liste des types de biens récupérée")
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(
            TypePropriete::orderBy('nom')->get()
        );
    }
}
