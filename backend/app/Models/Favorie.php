<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Favorie",
    title: "Modèle Favori",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "client_id", type: "integer", example: 2),
        new OA\Property(property: "propriete_id", type: "integer", example: 14)
    ]
)]
class Favorie extends Model
{
    use HasFactory;

    protected $table = 'favories';

    protected $fillable = [
        'client_id',
        'propriete_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function propriete(): BelongsTo
    {
        return $this->belongsTo(Propriete::class);
    }
}
