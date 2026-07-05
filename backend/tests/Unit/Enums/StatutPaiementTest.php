<?php

namespace Tests\Unit\Enums;

use App\Enums\MethodePaiement;
use App\Enums\StatutPaiement;
use PHPUnit\Framework\TestCase;

class StatutPaiementTest extends TestCase
{
    public function test_toutes_les_valeurs_attendues_existent(): void
    {
        $valeurs = array_map(fn ($case) => $case->value, StatutPaiement::cases());

        $this->assertEqualsCanonicalizing(
            ['en_attente', 'paye', 'en_retard'],
            $valeurs
        );
    }

    public function test_label_methode_paiement(): void
    {
        $this->assertSame('Mobile Money', MethodePaiement::MobileMoney->label());
        $this->assertSame('Espèces', MethodePaiement::Cash->label());
        $this->assertSame('Carte bancaire', MethodePaiement::CarteBancaire->label());
    }

    public function test_from_reconstruit_le_bon_cas_depuis_la_string(): void
    {
        $this->assertSame(StatutPaiement::Paye, StatutPaiement::from('paye'));
        $this->assertSame(MethodePaiement::MobileMoney, MethodePaiement::from('mobile_money'));
    }
}