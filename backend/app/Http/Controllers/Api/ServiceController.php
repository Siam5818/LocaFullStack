<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ServiceController extends Controller
{
    #[OA\Get(
        path: "/services",
        summary: "Lister tous les services inclus / optionnels",
        tags: ["Propriétés"],
        security: [],
        responses: [
            new OA\Response(response: 200, description: "Liste des services récupérée")
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(
            Service::orderBy('nom')->get()
        );
    }
}
