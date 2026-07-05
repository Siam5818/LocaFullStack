<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfilRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class ProfilController extends Controller
{
    #[OA\Put(
        path: "/me",
        summary: "Mettre à jour les informations du profil connecté",
        tags: ["Authentification"],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Dupont"),
                    new OA\Property(property: "prenom", type: "string", example: "Jean"),
                    new OA\Property(property: "telephone", type: "string", example: "+33600000000")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Profil mis à jour avec succès"),
            new OA\Response(response: 401, description: "Non authentifié"),
            new OA\Response(response: 422, description: "Données de modification invalides")
        ]
    )]
    public function update(UpdateProfilRequest $request): JsonResponse
    {
        $personne = $request->user();
        $personne->update($request->validated());

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user'    => $personne,
        ]);
    }

    #[OA\Put(
        path: "/me/password",
        summary: "Changer le mot de passe de l'utilisateur connecté",
        tags: ["Authentification"],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["current_password", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "current_password", type: "string", format: "password", example: "AncienM0t!"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "NouveauM0t!"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "NouveauM0t!")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Mot de passe modifié avec succès"),
            new OA\Response(response: 422, description: "Mot de passe actuel incorrect ou critères non respectés"),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $personne = $request->user();

        if (! Hash::check($request->current_password, $personne->password)) {
            return response()->json([
                'message' => 'Le mot de passe actuel est incorrect.',
            ], 422);
        }

        $personne->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Mot de passe mis à jour.']);
    }
}
