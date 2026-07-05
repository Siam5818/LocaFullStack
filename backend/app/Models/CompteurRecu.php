<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompteurRecu extends Model
{
    protected $table = 'compteurs_recus';

    protected $fillable = [
        'annee',
        'dernier_numero',
    ];
}
