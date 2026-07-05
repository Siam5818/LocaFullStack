<?php

namespace Tests\Feature\Bailleur;

use App\Models\Bailleur;
use App\Models\Propriete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_bailleur_voit_uniquement_ses_propres_proprietes(): void
    {
        $bailleurA = Bailleur::factory()->create();
        $bailleurB = Bailleur::factory()->create();

        Propriete::factory()->count(2)->create(['bailleur_id' => $bailleurA->id]);
        Propriete::factory()->count(3)->create(['bailleur_id' => $bailleurB->id]);

        $token = $bailleurA->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/bailleur/proprietes');

        $response->assertStatus(200)->assertJsonCount(2);
    }

    public function test_bailleur_peut_voir_le_detail_de_sa_propre_propriete(): void
    {
        $bailleur = Bailleur::factory()->create();
        $propriete = Propriete::factory()->create(['bailleur_id' => $bailleur->id]);

        $token = $bailleur->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson("/api/bailleur/proprietes/{$propriete->id}");

        $response->assertStatus(200)->assertJsonPath('id', $propriete->id);
    }

    public function test_revenus_bailleur_retourne_un_resume_vide_sans_contrat(): void
    {
        $bailleur = Bailleur::factory()->create();
        $token = $bailleur->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/bailleur/revenus');

        $response->assertStatus(200)->assertJsonPath('nombre_contrats_actifs', 0);
    }

    public function test_client_ne_peut_pas_acceder_aux_routes_bailleur(): void
    {
        $client = \App\Models\Client::factory()->create();
        $token = $client->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/bailleur/proprietes');

        $response->assertStatus(403);
    }
}