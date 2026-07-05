<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BailleurModel",
    title: "Modèle Bailleur",
    description: "Représente un propriétaire de biens immobiliers sur la plateforme",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "personne_id", type: "integer", example: 2),
        new OA\Property(property: "iban", type: "string", nullable: true, example: "FR763000..."),
        new OA\Property(property: "nom_banque", type: "string", nullable: true, example: "Société Générale"),
        new OA\Property(property: "created_by_admin_id", type: "integer", nullable: true, example: 1),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Bailleur extends Model
{
    use HasFactory;

    protected $fillable = [
        'personne_id',
        'iban',
        'nom_banque',
        'created_by_admin_id',
    ];

    public function personne(): BelongsTo
    {
        return $this->belongsTo(Personne::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    public function proprietes(): HasMany
    {
        return $this->hasMany(Propriete::class);
    }
}
