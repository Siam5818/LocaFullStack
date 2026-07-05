<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "LocaFullStack API",
    version: "1.0.0",
    description: "API de gestion d'une plateforme immobilière (location/vente) avec gestion des bailleurs, clients, contrats, paiements et commissions de plateforme.",
    contact: new OA\Contact(email: "contact@locationfullstack.com")
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Serveur de développement local"
)]
#[OA\Tag(name: "Authentification", description: "Inscription, connexion, vérification d'email")]
#[OA\Tag(name: "Propriétés", description: "Consultation publique des propriétés")]
#[OA\Tag(name: "Client", description: "Réservations, favoris, avis (rôle client)")]
#[OA\Tag(name: "Bailleur", description: "Consultation des biens et revenus (rôle bailleur)")]
#[OA\Tag(name: "Admin", description: "Gestion complète de la plateforme (rôle admin)")]
class SwaggerInfoController extends Controller
{
    // Tout est géré par les attributs PHP 8 ci-dessus
}
