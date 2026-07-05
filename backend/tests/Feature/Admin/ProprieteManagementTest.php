<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Bailleur;
use App\Models\Equipement;
use App\Models\Propriete;
use App\Models\Service;
use App\Models\TypePropriete;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProprieteManagementTest extends TestCase
{
    use RefreshDatabase;

    private function adminToken(): string
    {
        $admin = Admin::factory()->create();
        return $admin->personne->createToken('test')->plainTextToken;
    }

    public function test_admin_peut_creer_une_propriete(): void
    {
        $token = $this->adminToken();
        $bailleur = Bailleur::factory()->create();
        $type = TypePropriete::factory()->create();
        $service = Service::factory()->create();

        $response = $this->withToken($token)->postJson('/api/admin/proprietes', [
            'nom' => 'Villa Test',
            'ville' => 'Dakar',
            'nombre_piece' => 4,
            'dimension' => 200,
            'description' => 'Description test',
            'cout' => 500000,
            'typepropriete_id' => $type->id,
            'bailleur_id' => $bailleur->id,
            'service_id' => $service->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('proprietes', ['nom' => 'Villa Test']);
    }

    public function test_admin_peut_modifier_une_propriete(): void
    {
        $token = $this->adminToken();
        $propriete = Propriete::factory()->create(['nom' => 'Ancien nom']);

        $response = $this->withToken($token)->putJson("/api/admin/proprietes/{$propriete->id}", [
            'nom' => 'Nouveau nom',
            'ville' => $propriete->ville,
            'nombre_piece' => $propriete->nombre_piece,
            'dimension' => $propriete->dimension,
            'description' => $propriete->description,
            'cout' => $propriete->cout,
            'typepropriete_id' => $propriete->typepropriete_id,
            'bailleur_id' => $propriete->bailleur_id,
            'service_id' => $propriete->service_id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('proprietes', ['id' => $propriete->id, 'nom' => 'Nouveau nom']);
    }

    public function test_admin_peut_supprimer_une_propriete(): void
    {
        $token = $this->adminToken();
        $propriete = Propriete::factory()->create();

        $response = $this->withToken($token)->deleteJson("/api/admin/proprietes/{$propriete->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('proprietes', ['id' => $propriete->id]);
    }

    public function test_admin_peut_synchroniser_les_equipements(): void
    {
        $token = $this->adminToken();
        $propriete = Propriete::factory()->create();
        $equipements = Equipement::factory()->count(3)->create();

        $response = $this->withToken($token)->putJson(
            "/api/admin/proprietes/{$propriete->id}/equipements",
            ['equipement_ids' => $equipements->pluck('id')->toArray()]
        );

        $response->assertStatus(200);
        $this->assertCount(3, $propriete->equipements()->get());
    }

    public function test_creation_propriete_echoue_sans_champs_requis(): void
    {
        $token = $this->adminToken();

        $response = $this->withToken($token)->postJson('/api/admin/proprietes', []);

        $response->assertStatus(422);
    }

    public function test_non_admin_ne_peut_pas_creer_de_propriete(): void
    {
        $client = \App\Models\Client::factory()->create();
        $token = $client->personne->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/admin/proprietes', []);

        $response->assertStatus(403);
    }
}