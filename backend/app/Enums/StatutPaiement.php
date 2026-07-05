<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case EnAttente = 'en_attente';
    case Paye = 'paye';
    case EnRetard = 'en_retard';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Paye => 'Payé',
            self::EnRetard => 'En retard',
        };
    }

    // Couleurs douces adaptées pour l'impression PDF
    public function couleur(): array
    {
        return match ($this) {
            self::Paye => [
                'bg'    => '#e6f4ea', // Vert pastel
                'texte' => '#137333'  // Vert foncé
            ],
            self::EnAttente => [
                'bg'    => '#ffeec2', // Jaune/Orange pastel
                'texte' => '#b06000'  // Orange foncé
            ],
            self::EnRetard => [
                'bg'    => '#fce8e6', // Rouge pastel
                'texte' => '#c5221f'  // Rouge foncé
            ],
        };
    }
}
