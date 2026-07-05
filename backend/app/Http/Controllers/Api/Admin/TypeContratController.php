<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeContrat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TypeContratController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TypeContrat::all(), 200);
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom'                     => ['required', 'string', 'max:100'],
            'duree_mois'              => ['nullable', 'integer', 'min:1'],
            'taux_commission_defaut'  => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $type = TypeContrat::create($request->all());

        return response()->json(['message' => 'Type de contrat créé.', 'type' => $type], 201);
    }

    public function update(Request $request, TypeContrat $typecontrat): JsonResponse
    {
        $request->validate([
            'nom'                     => ['sometimes', 'string', 'max:100'],
            'duree_mois'              => ['nullable', 'integer', 'min:1'],
            'taux_commission_defaut'  => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ]);

        $typecontrat->update($request->all());

        return response()->json(['message' => 'Type de contrat mis à jour.', 'type' => $typecontrat]);
    }
}
