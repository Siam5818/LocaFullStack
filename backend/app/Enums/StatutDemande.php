<?php

namespace App\Enums;

enum StatutDemande: string
{
    case Nouvelle = 'nouvelle';
    case Traitee = 'traitee';
    case Fermee = 'fermee';

    public function label(): string
    {
        return match ($this) {
            self::Nouvelle => 'Nouvelle',
            self::Traitee => 'Traitée',
            self::Fermee => 'Fermée',
        };
    }
}
