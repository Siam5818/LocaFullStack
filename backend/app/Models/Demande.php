<?php

namespace App\Models;

use App\Enums\StatutDemande;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Demande",
    title: "Modèle Demande de Contact",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "client_id", type: "integer", nullable: true, example: 3),
        new OA\Property(property: "nom", type: "string", example: "Dupont"),
        new OA\Property(property: "email", type: "string", format: "email", example: "jean@example.com"),
        new OA\Property(property: "telephone", type: "string", example: "+33612345678"),
        new OA\Property(property: "objet", type: "string", example: "Demande d'information"),
        new OA\Property(property: "description", type: "string", example: "Détail de la demande..."),
        new OA\Property(property: "statut", type: "string", example: "nouvelle")
    ]
)]
class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'nom',
        'email',
        'telephone',
        'objet',
        'description',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'statut' => StatutDemande::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
