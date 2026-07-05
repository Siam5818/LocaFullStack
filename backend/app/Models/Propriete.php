<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propriete extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'rue',
        'quartier',
        'ville',
        'pays',
        'latitude',
        'longitude',
        'nombre_piece',
        'dimension',
        'description',
        'cout',
        'typepropriete_id',
        'bailleur_id',
        'service_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'cout'      => 'decimal:2',
        ];
    }

    public function typePropriete(): BelongsTo
    {
        return $this->belongsTo(TypePropriete::class, 'typepropriete_id');
    }

    public function bailleur(): BelongsTo
    {
        return $this->belongsTo(Bailleur::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ImagePropriete::class)->orderBy('ordre');
    }

    public function imagePrincipale(): HasMany
    {
        return $this->hasMany(ImagePropriete::class)->where('is_principale', true);
    }

    public function equipements(): BelongsToMany
    {
        return $this->belongsToMany(Equipement::class, 'propriete_equipement');
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

    public function noteMoyenne(): float
    {
        return round($this->notes()->avg('note') ?? 0, 1);
    }
}
