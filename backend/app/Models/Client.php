<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ClientModel",
    title: "Modèle Client",
    description: "Représente un locataire ou acheteur potentiel sur la plateforme",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "personne_id", type: "integer", example: 3),
        new OA\Property(property: "date_naissance", type: "string", format: "date", nullable: true, example: "1995-06-15"),
        new OA\Property(property: "adresse", type: "string", nullable: true, example: "45 Avenue de la République, Paris"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'personne_id',
        'date_naissance',
        'adresse',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }

    public function personne(): BelongsTo
    {
        return $this->belongsTo(Personne::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function favories(): HasMany
    {
        return $this->hasMany(Favorie::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class);
    }
}
