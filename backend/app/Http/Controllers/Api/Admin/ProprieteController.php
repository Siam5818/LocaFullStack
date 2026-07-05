<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProprieteRequest;
use App\Models\Propriete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProprieteController extends Controller
{
    public function store(StoreProprieteRequest $request): JsonResponse
    {
        $propriete = Propriete::create($request->except('equipement_ids'));

        if ($request->has('equipement_ids')) {
            $propriete->equipements()->sync($request->equipement_ids);
        }

        return response()->json([
            'message'   => 'Propriété créée avec succès.',
            'propriete' => $propriete->load('equipements'),
        ], 201);
    }

    public function update(StoreProprieteRequest $request, Propriete $propriete): JsonResponse
    {
        $propriete->update($request->except('equipement_ids'));

        if ($request->has('equipement_ids')) {
            $propriete->equipements()->sync($request->equipement_ids);
        }

        return response()->json([
            'message'   => 'Propriété mise à jour.',
            'propriete' => $propriete->load('equipements'),
        ]);
    }

    public function destroy(Propriete $propriete): JsonResponse
    {
        $propriete->delete();

        return response()->json(['message' => 'Propriété supprimée.']);
    }

    public function syncEquipements(Request $request, Propriete $propriete): JsonResponse
    {
        $request->validate([
            'equipement_ids'   => ['required', 'array'],
            'equipement_ids.*' => ['integer', 'exists:equipements,id'],
        ]);

        $propriete->equipements()->sync($request->equipement_ids);

        return response()->json([
            'message'   => 'Équipements mis à jour.',
            'propriete' => $propriete->load('equipements'),
        ]);
    }
}
