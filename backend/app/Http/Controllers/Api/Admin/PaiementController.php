<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\StatutPaiement;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaiementRequest;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Services\PaiementService;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaiementController extends Controller
{
    public function __construct(
        private readonly PaiementService $paiementService,
        private readonly PdfService $pdfService,
    ) {}

    public function store(StorePaiementRequest $request, Contrat $contrat): JsonResponse
    {
        $paiement = $contrat->paiements()->create([
            'montant'       => $request->montant,
            'date_echeance' => $request->date_echeance,
            'statut'        => StatutPaiement::EnAttente,
        ]);

        return response()->json([
            'message'  => 'Échéance de paiement créée.',
            'paiement' => $paiement,
        ], 201);
    }

    public function updateStatut(Request $request, Paiement $paiement): JsonResponse
    {
        $request->validate([
            'statut'                 => ['required', 'in:paye,en_retard'],
            'methode'                => ['required_if:statut,paye', 'in:mobile_money,cash,carte_bancaire'],
            'operateur'              => ['nullable', 'string', 'max:50'],
            'reference_transaction'  => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->statut === 'paye') {
            $paiement = $this->paiementService->marquerPaye(
                paiement: $paiement,
                methode: $request->methode,
                operateur: $request->operateur,
                referenceTransaction: $request->reference_transaction,
            );
        } else {
            $paiement = $this->paiementService->marquerEnRetard($paiement);
        }

        return response()->json(['message' => 'Statut du paiement mis à jour.', 'paiement' => $paiement]);
    }

    public function recu(Paiement $paiement): Response
    {
        if ($paiement->numero_recu === null) {
            abort(404, 'Ce paiement n\'a pas encore de reçu (statut non payé).');
        }

        $pdf = $this->pdfService->genererRecu($paiement, pourClient: true);
        $nom = $this->pdfService->nomFichierRecu($paiement);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$nom}\"",
        ]);
    }
}
