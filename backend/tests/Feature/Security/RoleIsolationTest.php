<?php

namespace Tests\Feature\Security;

use App\Models\Bailleur;
use App\Models\Client;
use App\Models\Propriete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_client_ne_peut_pas_acceder_aux_routes_admin(): void
    {
        $client = Client::factory()->create();
        $token = $client->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/admin/clients');

        $response->assertStatus(403);
    }

    public function test_un_bailleur_ne_peut_pas_voir_les_proprietes_dun_autre_bailleur(): void
    {
        $bailleurA = Bailleur::factory()->create();
        $bailleurB = Bailleur::factory()->create();
        $proprieteB = Propriete::factory()->create(['bailleur_id' => $bailleurB->id]);

        $token = $bailleurA->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson("/api/bailleur/proprietes/{$proprieteB->id}");

        $response->assertStatus(403);
    }

    public function test_un_client_ne_peut_pas_voir_la_reservation_dun_autre_client(): void
    {
        $clientA = Client::factory()->create();
        $clientB = Client::factory()->create();
        $propriete = Propriete::factory()->create();

        $reservation = \App\Models\Reservation::create([
            'client_id' => $clientB->id,
            'propriete_id' => $propriete->id,
            'statut' => 'en_attente',
            'date_soumission' => now(),
        ]);

        $token = $clientA->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson("/api/mes-reservations/{$reservation->id}");

        $response->assertStatus(403);
    }

    public function test_un_admin_desactive_ne_peut_plus_acceder_aux_routes_admin(): void
    {
        $admin = \App\Models\Admin::factory()->create();
        $admin->personne->update(['is_active' => false]);

        $token = $admin->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/admin/clients');

        $response->assertStatus(403);
    }

    public function test_route_publique_accessible_sans_authentification(): void
    {
        $response = $this->getJson('/api/proprietes');

        $response->assertStatus(200);
    }

    public function test_route_protegee_refuse_sans_token(): void
    {
        $response = $this->getJson('/api/mes-reservations');

        $response->assertStatus(401);
    }
}
