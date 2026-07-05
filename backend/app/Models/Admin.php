<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AdminModel",
    title: "Modèle Admin",
    description: "Représente un administrateur du système",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "personne_id", type: "integer", example: 1),
        new OA\Property(property: "niveau_acces", type: "string", example: "super_admin"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'personne_id',
        'niveau_acces',
    ];

    public function personne(): BelongsTo
    {
        return $this->belongsTo(Personne::class);
    }

    public function bailleursCrees(): HasMany
    {
        return $this->hasMany(Bailleur::class, 'created_by_admin_id');
    }
}
