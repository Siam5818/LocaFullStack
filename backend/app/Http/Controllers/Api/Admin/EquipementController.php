<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReferenceRequest;
use App\Models\Equipement;
use Illuminate\Http\JsonResponse;

class EquipementController extends Controller
{
    public function store(StoreReferenceRequest $request): JsonResponse
    {
        $equipement = Equipement::create($request->only('nom', 'icone'));

        return response()->json(['message' => 'Équipement créé.', 'equipement' => $equipement], 201);
    }

    public function update(StoreReferenceRequest $request, Equipement $equipement): JsonResponse
    {
        $equipement->update($request->only('nom', 'icone'));

        return response()->json(['message' => 'Équipement mis à jour.', 'equipement' => $equipement]);
    }
}
