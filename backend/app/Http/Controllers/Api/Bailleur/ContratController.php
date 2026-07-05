<?php

namespace App\Http\Controllers\Api\Bailleur;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ContratController extends Controller
{
    public function __construct(
        private readonly PdfService $pdfService,
    ) {}

    #[OA\Get(
        path: "/bailleur/contrats",
        summary: "Lister les contrats liés aux biens du bailleur connecté",
        tags: ["Bailleur"],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: "Liste des contrats du bailleur récupérée"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $bailleurId = $request->user()->bailleur->id;

        $contrats = Contrat::with(['reservation.propriete', 'reservation.client.personne', 'typeContrat'])
            ->whereHas('reservation.propriete', function ($query) use ($bailleurId) {
                $query->where('bailleur_id', $bailleurId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($contrats);
    }

    #[OA\Get(
        path: "/bailleur/contrats/{contrat}",
        summary: "Afficher les détails d'un contrat spécifique du bailleur",
        tags: ["Bailleur"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "contrat", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Détails du contrat récupérés"),
            new OA\Response(response: 403, description: "Accès interdit"),
            new OA\Response(response: 404, description: "Contrat introuvable")
        ]
    )]
    public function show(Request $request, Contrat $contrat): JsonResponse
    {
        $this->autoriserProprietaire($request, $contrat);

        return response()->json(
            $contrat->load(['reservation.propriete', 'reservation.client.personne', 'typeContrat', 'paiements'])
        );
    }

    #[OA\Get(
        path: "/bailleur/contrats/{contrat}/paiements",
        summary: "Lister les paiements associés à un contrat spécifique",
        tags: ["Bailleur"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "contrat", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Historique des paiements récupéré"),
            new OA\Response(response: 403, description: "Accès interdit")
        ]
    )]
    public function paiements(Request $request, Contrat $contrat): JsonResponse
    {
        $this->autoriserProprietaire($request, $contrat);

        return response()->json($contrat->paiements()->orderBy('date_echeance')->get());
    }

    #[OA\Get(
        path: "/bailleur/contrats/{contrat}/telecharger",
        summary: "Télécharger le PDF du contrat (Vue Bailleur)",
        tags: ["Bailleur"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "contrat", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Fichier PDF du contrat", content: new OA\MediaType(mediaType: "application/pdf")),
            new OA\Response(response: 403, description: "Accès interdit")
        ]
    )]
    public function telecharger(Request $request, Contrat $contrat): Response
    {
        $this->autoriserProprietaire($request, $contrat);

        $pdf = $this->pdfService->genererContrat($contrat);
        $nom = $this->pdfService->nomFichierContrat($contrat);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$nom}\"",
        ]);
    }

    #[OA\Get(
        path: "/bailleur/contrats/{contrat}/paiements/{paiement}/recu",
        summary: "Télécharger le reçu net après commission au format PDF",
        tags: ["Bailleur"],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: "contrat", in: "path", required: true, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "paiement", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Fichier PDF du reçu bailleur", content: new OA\MediaType(mediaType: "application/pdf")),
            new OA\Response(response: 404, description: "Reçu introuvable"),
            new OA\Response(response: 403, description: "Accès interdit")
        ]
    )]
    public function recuPaiement(Request $request, Contrat $contrat, Paiement $paiement): Response
    {
        $this->autoriserProprietaire($request, $contrat);

        if ($paiement->contrat_id !== $contrat->id || $paiement->numero_recu === null) {
            abort(404, 'Reçu introuvable pour ce paiement.');
        }

        // pourClient: false -> le bailleur voit le montant NET après commission.
        $pdf = $this->pdfService->genererRecu($paiement, pourClient: false);
        $nom = $this->pdfService->nomFichierRecu($paiement);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$nom}\"",
        ]);
    }

    private function autoriserProprietaire(Request $request, Contrat $contrat): void
    {
        $contrat->loadMissing('reservation.propriete');

        if ($contrat->reservation->propriete->bailleur_id !== $request->user()->bailleur->id) {
            abort(403, 'Ce contrat ne concerne pas une de vos propriétés.');
        }
    }
}
