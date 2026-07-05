<?php

namespace App\Models;

use App\Enums\StatutContrat;
use App\Enums\StatutPaiement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Contrat",
    title: "Modèle Contrat",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "reservation_id", type: "integer", example: 5),
        new OA\Property(property: "typecontrat_id", type: "integer", example: 2),
        new OA\Property(property: "date_debut", type: "string", format: "date", example: "2026-06-22"),
        new OA\Property(property: "date_fin", type: "string", format: "date", nullable: true, example: "2027-06-22"),
        new OA\Property(property: "montant_total", type: "string", example: "1200.00"),
        new OA\Property(property: "taux_commission_applique", type: "string", example: "10.00"),
        new OA\Property(property: "montant_commission", type: "string", example: "120.00"),
        new OA\Property(property: "mode_paiement_vente", type: "string", enum: ["unique", "echelonne"], nullable: true, example: "unique"),
        new OA\Property(property: "statut", type: "string", example: "actif")
    ]
)]
class Contrat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'typecontrat_id',
        'date_debut',
        'date_fin',
        'montant_total',
        'taux_commission_applique',
        'montant_commission',
        'mode_paiement_vente',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_debut'               => 'date',
            'date_fin'                 => 'date',
            'montant_total'            => 'decimal:2',
            'taux_commission_applique' => 'decimal:2',
            'montant_commission'       => 'decimal:2',
            'statut'                   => StatutContrat::class,
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function typeContrat(): BelongsTo
    {
        return $this->belongsTo(TypeContrat::class, 'typecontrat_id');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function montantNetBailleur(): string
    {
        return bcsub((string) $this->montant_total, (string) $this->montant_commission, 2);
    }

    public function montantPaye(): string
    {
        return (string) $this->paiements()
        ->where('statut', StatutPaiement::Paye->value)
        ->sum('montant');
    }

    public function soldeRestant(): string
    {
        return bcsub((string) $this->montant_total, $this->montantPaye(), 2);
    }
}
