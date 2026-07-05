<?php

namespace App\Enums;

enum StatutContrat: string
{
    case Actif = 'actif';
    case Termine = 'termine';
    case Resilie = 'resilie';

    public function label(): string
    {
        return match ($this) {
            self::Actif => 'Actif',
            self::Termine => 'Terminé',
            self::Resilie => 'Résilié',
        };
    }

    public function peutEtreResilie(): bool
    {
        return $this === self::Actif;
    }
}
