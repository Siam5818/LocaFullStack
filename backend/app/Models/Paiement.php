<?php

namespace App\Models;

use App\Enums\MethodePaiement;
use App\Enums\StatutPaiement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Paiement",
    title: "Modèle Paiement",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "contrat_id", type: "integer", example: 8),
        new OA\Property(property: "montant", type: "string", example: "450.00"),
        new OA\Property(property: "date_echeance", type: "string", format: "date-time", example: "2026-07-05T00:00:00Z"),
        new OA\Property(property: "date_paiement", type: "string", format: "date-time", nullable: true, example: "2026-06-22T14:30:00Z"),
        new OA\Property(property: "statut", type: "string", example: "paye"),
        new OA\Property(property: "methode", type: "string", nullable: true, example: "carte_bancaire"),
        new OA\Property(property: "operateur", type: "string", nullable: true, example: "Stripe"),
        new OA\Property(property: "reference_transaction", type: "string", nullable: true, example: "ch_3MvXyL"),
        new OA\Property(property: "numero_recu", type: "string", nullable: true, example: "REC-2026-0084")
    ]
)]
class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'contrat_id',
        'montant',
        'date_echeance',
        'date_paiement',
        'statut',
        'methode',
        'operateur',
        'reference_transaction',
        'numero_recu',
    ];

    protected function casts(): array
    {
        return [
            'montant'       => 'decimal:2',
            'date_echeance' => 'datetime',
            'date_paiement' => 'datetime',
            'statut'        => StatutPaiement::class,
            'methode'       => MethodePaiement::class,
        ];
    }

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    public function estEnRetard(): bool
    {
        return $this->statut === StatutPaiement::EnAttente
            && $this->date_echeance->isPast();
    }
}
