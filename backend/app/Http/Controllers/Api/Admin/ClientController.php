<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Client::with('personne')->orderBy('created_at', 'desc')->get()
        );
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json($client->load('personne', 'reservations', 'favories'));
    }

    public function desactiver(Client $client): JsonResponse
    {
        $client->personne->update(['is_active' => false]);

        return response()->json(['message' => 'Client désactivé.']);
    }

    public function activer(Client $client): JsonResponse
    {
        $client->personne->update(['is_active' => true]);

        return response()->json(['message' => 'Client activé.']);
    }
}
