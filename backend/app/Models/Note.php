<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Note",
    title: "Modèle Note/Avis",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "client_id", type: "integer", example: 3),
        new OA\Property(property: "propriete_id", type: "integer", example: 12),
        new OA\Property(property: "note", type: "integer", example: 5),
        new OA\Property(property: "commentaire", type: "string", nullable: true, example: "Excellent séjour !")
    ]
)]
class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'propriete_id',
        'note',
        'commentaire',
    ];

    protected function casts(): array
    {
        return [
            'note' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function propriete(): BelongsTo
    {
        return $this->belongsTo(Propriete::class);
    }
}
