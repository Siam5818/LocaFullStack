<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeContrat extends Model
{
    use HasFactory;

    protected $table = 'typecontrats';

    protected $fillable = [
        'nom',
        'duree_mois',
        'taux_commission_defaut',
    ];

    protected function casts(): array
    {
        return [
            'taux_commission_defaut' => 'decimal:2',
        ];
    }

    public function contrats(): HasMany
    {
        return $this->hasMany(Contrat::class);
    }
}
