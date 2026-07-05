<?php

namespace Tests\Unit\Enums;

use App\Enums\StatutReservation;
use PHPUnit\Framework\TestCase;

class StatutReservationTest extends TestCase
{
    public function test_en_attente_peut_etre_confirmee(): void
    {
        $this->assertTrue(StatutReservation::EnAttente->peutEtreConfirmee());
    }

    public function test_en_attente_peut_etre_annulee(): void
    {
        $this->assertTrue(StatutReservation::EnAttente->peutEtreAnnulee());
    }

    public function test_confirmee_ne_peut_plus_etre_annulee(): void
    {
        $this->assertFalse(StatutReservation::Confirmee->peutEtreAnnulee());
    }

    public function test_annulee_ne_peut_plus_etre_confirmee(): void
    {
        $this->assertFalse(StatutReservation::Annulee->peutEtreConfirmee());
    }

    public function test_label_retourne_le_bon_texte_francais(): void
    {
        $this->assertSame('En attente', StatutReservation::EnAttente->label());
        $this->assertSame('Confirmée', StatutReservation::Confirmee->label());
        $this->assertSame('Annulée', StatutReservation::Annulee->label());
    }

    public function test_value_correspond_a_la_colonne_base_de_donnees(): void
    {
        $this->assertSame('en_attente', StatutReservation::EnAttente->value);
        $this->assertSame('confirmee', StatutReservation::Confirmee->value);
        $this->assertSame('annulee', StatutReservation::Annulee->value);
    }
}
