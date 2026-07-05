<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ImagePropriete",
    title: "Modèle Image de Propriété",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "propriete_id", type: "integer", example: 12),
        new OA\Property(property: "chemin", type: "string", example: "proprietes/image1.jpg"),
        new OA\Property(property: "is_principale", type: "boolean", example: true),
        new OA\Property(property: "ordre", type: "integer", example: 0)
    ]
)]
class ImagePropriete extends Model
{
    use HasFactory;

    protected $table = 'images_propriete';

    protected $fillable = [
        'propriete_id',
        'chemin',
        'is_principale',
        'ordre',
    ];

    protected function casts(): array
    {
        return [
            'is_principale' => 'boolean',
        ];
    }

    public function propriete(): BelongsTo
    {
        return $this->belongsTo(Propriete::class);
    }
}
