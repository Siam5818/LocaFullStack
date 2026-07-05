<?php

namespace Tests\Feature\Business;

use App\Models\Admin;
use App\Models\Client;
use App\Models\Propriete;
use App\Models\Reservation;
use App\Models\TypeContrat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContratFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_flux_complet_reservation_vers_contrat_avec_commission(): void
    {
        Mail::fake();

        $admin = Admin::factory()->create();
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create(['cout' => 850000]);
        $typeContrat = TypeContrat::factory()->create(['taux_commission_defaut' => 10]);

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'propriete_id' => $propriete->id,
            'statut' => 'confirmee',
            'date_soumission' => now(),
        ]);

        $adminToken = $admin->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($adminToken)->postJson(
            "/api/admin/reservations/{$reservation->id}/contrat",
            [
                'typecontrat_id' => $typeContrat->id,
                'date_debut' => '2026-07-01',
                'date_fin' => '2027-06-30',
                'montant_total' => 850000,
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('contrats', [
            'reservation_id' => $reservation->id,
            'montant_total' => 850000,
            'montant_commission' => 85000,
            'taux_commission_applique' => 10,
            'statut' => 'actif',
        ]);

        Mail::assertSent(\App\Mail\ContratSigneMail::class);
    }

    public function test_impossible_de_creer_un_contrat_sur_reservation_non_confirmee(): void
    {
        $admin = Admin::factory()->create();
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        $typeContrat = TypeContrat::factory()->create();

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'propriete_id' => $propriete->id,
            'statut' => 'en_attente',
            'date_soumission' => now(),
        ]);

        $adminToken = $admin->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($adminToken)->postJson(
            "/api/admin/reservations/{$reservation->id}/contrat",
            [
                'typecontrat_id' => $typeContrat->id,
                'date_debut' => '2026-07-01',
                'montant_total' => 500000,
            ]
        );

        $response->assertStatus(422);
        $this->assertDatabaseCount('contrats', 0);
    }

    public function test_paiement_marque_paye_genere_un_numero_de_recu(): void
    {
        Mail::fake();

        $admin = Admin::factory()->create();
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        $typeContrat = TypeContrat::factory()->create();

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'propriete_id' => $propriete->id,
            'statut' => 'confirmee',
            'date_soumission' => now(),
        ]);

        $adminToken = $admin->personne->createToken('test')->plainTextToken;

        $this->withToken($adminToken)->postJson(
            "/api/admin/reservations/{$reservation->id}/contrat",
            ['typecontrat_id' => $typeContrat->id, 'date_debut' => '2026-07-01', 'montant_total' => 500000]
        );

        $contrat = \App\Models\Contrat::first();

        $paiementResponse = $this->withToken($adminToken)->postJson(
            "/api/admin/contrats/{$contrat->id}/paiements",
            ['montant' => 500000, 'date_echeance' => '2026-07-01']
        );

        $paiementId = $paiementResponse->json('paiement.id');

        $statutResponse = $this->withToken($adminToken)->patchJson(
            "/api/admin/paiements/{$paiementId}/statut",
            ['statut' => 'paye', 'methode' => 'mobile_money', 'operateur' => 'Wave']
        );

        $statutResponse->assertStatus(200);

        $this->assertDatabaseHas('paiements', [
            'id' => $paiementId,
            'statut' => 'paye',
        ]);

        $paiement = \App\Models\Paiement::find($paiementId);
        $this->assertNotNull($paiement->numero_recu);
        $this->assertStringStartsWith('REC-' . now()->format('Y') . '-', $paiement->numero_recu);
    }
}
