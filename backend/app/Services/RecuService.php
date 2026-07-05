<?php

namespace App\Services;

use App\Models\CompteurRecu;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class RecuService
{
    public function genererNumeroRecu(Paiement $paiement): string
    {
        if ($paiement->numero_recu !== null) {
            return $paiement->numero_recu;
        }

        return DB::transaction(function () use ($paiement) {
            $annee = (int) now()->format('Y');

            $compteur = CompteurRecu::where('annee', $annee)
                ->lockForUpdate()
                ->first();

            if ($compteur === null) {
                CompteurRecu::create([
                    'annee'          => $annee,
                    'dernier_numero' => 0,
                ]);

                $compteur = CompteurRecu::where('annee', $annee)
                    ->lockForUpdate()
                    ->first();
            }

            $compteur->dernier_numero += 1;
            $compteur->save();

            $numero = sprintf('REC-%d-%06d', $annee, $compteur->dernier_numero);

            $paiement->numero_recu = $numero;
            $paiement->save();

            return $numero;
        });
    }
}
