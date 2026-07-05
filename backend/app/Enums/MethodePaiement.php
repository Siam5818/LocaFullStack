<?php

namespace App\Enums;

enum MethodePaiement: string
{
    case MobileMoney = 'mobile_money';
    case Cash = 'cash';
    case CarteBancaire = 'carte_bancaire';

    public function label(): string
    {
        return match ($this) {
            self::MobileMoney => 'Mobile Money',
            self::Cash => 'Espèces',
            self::CarteBancaire => 'Carte bancaire',
        };
    }
}
