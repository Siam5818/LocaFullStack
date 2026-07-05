<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReferenceRequest;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function store(StoreReferenceRequest $request): JsonResponse
    {
        $service = Service::create($request->only('nom'));

        return response()->json(['message' => 'Service créé.', 'service' => $service], 201);
    }

    public function update(StoreReferenceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->only('nom'));

        return response()->json(['message' => 'Service mis à jour.', 'service' => $service]);
    }
}
