<?php

namespace App\Models;

use App\Enums\StatutReservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'propriete_id',
        'statut',
        'date_soumission',
    ];

    protected function casts(): array
    {
        return [
            'date_soumission' => 'date',
            'statut'        => StatutReservation::class,
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

    public function contrat(): HasOne
    {
        return $this->hasOne(Contrat::class);
    }

    public function estEnAttente(): bool
    {
        return $this->statut === StatutReservation::EnAttente;;
    }

    public function estConfirmee(): bool
    {
        return $this->statut === StatutReservation::Confirmee;
    }
}
