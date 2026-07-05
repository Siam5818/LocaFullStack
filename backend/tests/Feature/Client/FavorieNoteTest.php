<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use App\Models\Propriete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavorieNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_peut_ajouter_un_favori(): void
    {
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        $token = $client->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/favoris', ['propriete_id' => $propriete->id]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('favories', ['client_id' => $client->id, 'propriete_id' => $propriete->id]);
    }

    public function test_ajouter_deux_fois_le_meme_favori_ne_duplique_pas(): void
    {
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        $token = $client->personne->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/favoris', ['propriete_id' => $propriete->id]);
        $this->withToken($token)->postJson('/api/favoris', ['propriete_id' => $propriete->id]);

        $this->assertDatabaseCount('favories', 1);
    }

    public function test_client_peut_supprimer_son_favori(): void
    {
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        $favorie = \App\Models\Favorie::create(['client_id' => $client->id, 'propriete_id' => $propriete->id]);
        $token = $client->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->deleteJson("/api/favoris/{$favorie->id}");

        $response->assertStatus(200);
        $this->assertDatabaseCount('favories', 0);
    }

    public function test_client_ne_peut_pas_supprimer_le_favori_dun_autre(): void
    {
        $clientA = Client::factory()->create();
        $clientB = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        $favorie = \App\Models\Favorie::create(['client_id' => $clientB->id, 'propriete_id' => $propriete->id]);
        $token = $clientA->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->deleteJson("/api/favoris/{$favorie->id}");

        $response->assertStatus(403);
        $this->assertDatabaseCount('favories', 1);
    }

    public function test_client_peut_laisser_un_avis(): void
    {
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        $token = $client->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson(
            "/api/proprietes/{$propriete->id}/notes",
            ['note' => 5, 'commentaire' => 'Excellent']
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('notes', ['client_id' => $client->id, 'propriete_id' => $propriete->id, 'note' => 5]);
    }

    public function test_client_ne_peut_pas_laisser_deux_avis_sur_la_meme_propriete(): void
    {
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        $token = $client->personne->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson("/api/proprietes/{$propriete->id}/notes", ['note' => 5]);
        $response = $this->withToken($token)->postJson("/api/proprietes/{$propriete->id}/notes", ['note' => 3]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('notes', 1);
    }

    public function test_avis_publics_visibles_sans_authentification(): void
    {
        $client = Client::factory()->create();
        $propriete = Propriete::factory()->create();
        \App\Models\Note::create([
            'client_id' => $client->id,
            'propriete_id' => $propriete->id,
            'note' => 4,
            'commentaire' => 'Bien',
        ]);

        $response = $this->getJson("/api/proprietes/{$propriete->id}/notes");

        $response->assertStatus(200)->assertJsonCount(1);
    }
}