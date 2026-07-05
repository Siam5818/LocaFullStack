<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\StatutDemande;
use App\Http\Controllers\Controller;
use App\Models\Demande;
use Illuminate\Http\JsonResponse;

class DemandeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Demande::with('client.personne')->orderBy('created_at', 'desc')->get()
        );
    }

    public function traiter(Demande $demande): JsonResponse
    {
        $demande->update(['statut' => StatutDemande::Traitee]);

        return response()->json(['message' => 'Demande marquée comme traitée.']);
    }

    public function fermer(Demande $demande): JsonResponse
    {
        $demande->update(['statut' => StatutDemande::Fermee]);

        return response()->json(['message' => 'Demande fermée.']);
    }
}
