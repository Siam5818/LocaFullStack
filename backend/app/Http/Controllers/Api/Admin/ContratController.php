<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Services\ContratService;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContratController extends Controller
{
    public function __construct(
        private readonly PdfService $pdfService,
        private readonly ContratService $contratService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            Contrat::with(['reservation.client.personne', 'reservation.propriete', 'typeContrat'])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function show(Contrat $contrat): JsonResponse
    {
        return response()->json(
            $contrat->load([
                'reservation.client.personne',
                'reservation.propriete.bailleur.personne',
                'typeContrat',
                'paiements',
            ])
        );
    }

    public function telecharger(Contrat $contrat): Response
    {
        $pdf = $this->pdfService->genererContrat($contrat);
        $nom = $this->pdfService->nomFichierContrat($contrat);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$nom}\"",
        ]);
    }

    public function resilier(Contrat $contrat): JsonResponse
    {
        $this->contratService->resilier($contrat);

        return response()->json(['message' => 'Contrat résilié.']);
    }
}
