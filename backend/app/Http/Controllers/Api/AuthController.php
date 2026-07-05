<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Client;
use App\Models\Personne;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/register",
        summary: "Inscription d'un nouvel utilisateur (client)",
        tags: ["Authentification"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nom", "prenom", "email", "password", "telephone"],
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Dupont"),
                    new OA\Property(property: "prenom", type: "string", example: "Jean"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "jean.dupont@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret1234*"),
                    new OA\Property(property: "telephone", type: "string", example: "+33612345678")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Inscription réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Inscription réussie. Veuillez vérifier votre e-mail avant de vous connecter.")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Données de validation invalides (ex: e-mail déjà pris, mot de passe trop court)")
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $personne = Personne::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'telephone' => $request->telephone,
            'role'      => 'client',
            'is_active' => true,
        ]);

        Client::create(['personne_id' => $personne->id]);

        event(new Registered($personne));

        return response()->json([
            'message' => 'Inscription réussie. Veuillez vérifier votre e-mail avant de vous connecter.',
        ], 201);
    }

    #[OA\Post(
        path: "/login",
        summary: "Connexion d'un utilisateur (client, bailleur ou admin)",
        tags: ["Authentification"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@locationfullstack.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Admin1234")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Connexion réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Connexion réussie."),
                        new OA\Property(property: "token", type: "string"),
                        new OA\Property(
                            property: "user",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer"),
                                new OA\Property(property: "nom", type: "string"),
                                new OA\Property(property: "prenom", type: "string"),
                                new OA\Property(property: "email", type: "string"),
                                new OA\Property(property: "role", type: "string")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Identifiants invalides"),
            new OA\Response(response: 403, description: "Email non vérifié ou compte désactivé"),
            new OA\Response(response: 429, description: "Trop de tentatives")
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'message' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
            ], 429);
        }

        $personne = Personne::where('email', $request->email)->first();

        if (! $personne || ! Hash::check($request->password, $personne->password)) {
            RateLimiter::hit($throttleKey, 60);
            return response()->json(['message' => 'Identifiants invalides.'], 401);
        }

        if (! $personne->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Veuillez vérifier votre adresse e-mail avant de vous connecter.',
            ], 403);
        }

        if (! $personne->is_active) {
            return response()->json(['message' => 'Compte désactivé.'], 403);
        }

        RateLimiter::clear($throttleKey);

        $token = $personne->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'token'   => $token,
            'user'    => [
                'id'     => $personne->id,
                'nom'    => $personne->nom,
                'prenom' => $personne->prenom,
                'email'  => $personne->email,
                'role'   => $personne->role,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    public function me(Request $request): JsonResponse
    {
        $personne = $request->user()->load(['client', 'admin', 'bailleur']);

        return response()->json($personne);
    }

    public function verifyEmail(Request $request, int $id, string $hash): JsonResponse
    {
        $personne = Personne::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($personne->getEmailForVerification()))) {
            return response()->json(['message' => 'Lien de vérification invalide.'], 403);
        }

        if ($personne->hasVerifiedEmail()) {
            return response()->json(['message' => 'E-mail déjà vérifié.']);
        }

        $personne->markEmailAsVerified();

        return response()->json(['message' => 'E-mail vérifié avec succès. Vous pouvez vous connecter.']);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $personne = Personne::where('email', $request->input('email'))->first();

        if (! $personne) {
            return response()->json(['message' => 'Aucun compte associé à cet e-mail.'], 404);
        }

        if ($personne->hasVerifiedEmail()) {
            return response()->json(['message' => 'E-mail déjà vérifié.']);
        }

        $personne->sendEmailVerificationNotification();

        return response()->json(['message' => 'E-mail de vérification renvoyé.']);
    }
}
