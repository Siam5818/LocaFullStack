<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBailleurRequest;
use App\Models\Bailleur;
use App\Models\Personne;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BailleurController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Bailleur::with('personne')->orderBy('created_at', 'desc')->get()
        );
    }

    public function show(Bailleur $bailleur): JsonResponse
    {
        return response()->json($bailleur->load('personne', 'proprietes'));
    }

    public function store(StoreBailleurRequest $request): JsonResponse
    {
        $adminId = $request->user()->admin->id;

        $personne = Personne::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'telephone' => $request->telephone,
            'role'      => 'bailleur',
            'is_active' => true,
            // Créé par un admin : pas de vérification email requise.
            'email_verified_at' => now(),
        ]);

        $bailleur = Bailleur::create([
            'personne_id'         => $personne->id,
            'iban'                => $request->iban,
            'nom_banque'          => $request->nom_banque,
            'created_by_admin_id' => $adminId,
        ]);

        return response()->json([
            'message'  => 'Bailleur créé avec succès.',
            'bailleur' => $bailleur->load('personne'),
        ], 201);
    }

    public function update(Request $request, Bailleur $bailleur): JsonResponse
    {
        $request->validate([
            'iban'       => ['nullable', 'string', 'max:50'],
            'nom_banque' => ['nullable', 'string', 'max:100'],
        ]);

        $bailleur->update($request->only('iban', 'nom_banque'));

        return response()->json(['message' => 'Bailleur mis à jour.', 'bailleur' => $bailleur]);
    }

    public function desactiver(Bailleur $bailleur): JsonResponse
    {
        $bailleur->personne->update(['is_active' => false]);

        return response()->json(['message' => 'Bailleur désactivé.']);
    }
}
