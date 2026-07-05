<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReferenceRequest;
use App\Models\TypePropriete;
use Illuminate\Http\JsonResponse;

class TypeProprieteController extends Controller
{
    public function store(StoreReferenceRequest $request): JsonResponse
    {
        $type = TypePropriete::create($request->only('nom', 'description'));

        return response()->json(['message' => 'Type de propriété créé.', 'type' => $type], 201);
    }

    public function update(StoreReferenceRequest $request, TypePropriete $typepropriete): JsonResponse
    {
        $typepropriete->update($request->only('nom', 'description'));

        return response()->json(['message' => 'Type de propriété mis à jour.', 'type' => $typepropriete]);
    }

    public function destroy(TypePropriete $typepropriete): JsonResponse
    {
        $typepropriete->delete();

        return response()->json(['message' => 'Type de propriété supprimé.']);
    }
}
