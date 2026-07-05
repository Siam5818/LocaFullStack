<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class EquipementController extends Controller
{
    #[OA\Get(
        path: "/equipements",
        summary: "Lister tous les équipements disponibles",
        tags: ["Propriétés"],
        security: [],
        responses: [
            new OA\Response(response: 200, description: "Liste des équipements récupérée")
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(
            Equipement::orderBy('nom')->get()
        );
    }
}
