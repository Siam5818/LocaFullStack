<?php

namespace App\Http\Controllers\Api\Bailleur;

use App\Http\Controllers\Controller;
use App\Services\BailleurRevenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RevenuController extends Controller
{
    public function __construct(
        private readonly BailleurRevenuService $revenuService,
    ) {}

    #[OA\Get(
        path: "/bailleur/revenus",
        summary: "Obtenir le résumé financier et le tableau des revenus du bailleur",
        tags: ["Bailleur"],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: "Résumé des revenus financier généré avec succès"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $bailleur = $request->user()->bailleur;

        return response()->json(
            $this->revenuService->resume($bailleur)
        );
    }
}
