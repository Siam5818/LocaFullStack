<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Equipement",
    title: "Modèle Équipement",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "nom", type: "string", example: "Wi-Fi"),
        new OA\Property(property: "icone", type: "string", nullable: true, example: "fa-wifi")
    ]
)]
class Equipement extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'icone'];

    public function proprietes(): BelongsToMany
    {
        return $this->belongsToMany(Propriete::class, 'propriete_equipement');
    }
}
