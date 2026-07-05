<?php

namespace Tests\Feature\Auth;

use App\Models\Personne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_inscription_cree_une_personne_et_un_client(): void
    {
        $response = $this->postJson('/api/register', [
            'nom' => 'Sow',
            'prenom' => 'Aminata',
            'email' => 'aminata@example.com',
            'telephone' => '771234567',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
        ]);

        $response->assertStatus(201);

        $personne = Personne::where('email', 'aminata@example.com')->first();
        $this->assertNotNull($personne);
        $this->assertSame('client', $personne->role);
        $this->assertNotNull($personne->client);
        $this->assertNull($personne->email_verified_at);
    }

    public function test_inscription_refuse_un_role_injecte_dans_le_payload(): void
    {
        $response = $this->postJson('/api/register', [
            'nom' => 'Diop',
            'prenom' => 'Malick',
            'email' => 'malick@example.com',
            'telephone' => '771234568',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
            'role' => 'admin',
        ]);

        $response->assertStatus(201);

        $personne = Personne::where('email', 'malick@example.com')->first();
        $this->assertSame('client', $personne->role);
    }

    public function test_inscription_refuse_email_deja_utilise(): void
    {
        Personne::factory()->create(['email' => 'existe@example.com']);

        $response = $this->postJson('/api/register', [
            'nom' => 'Test',
            'prenom' => 'Test',
            'email' => 'existe@example.com',
            'telephone' => '771234569',
            'password' => 'Password1234',
            'password_confirmation' => 'Password1234',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_inscription_refuse_mot_de_passe_faible(): void
    {
        $response = $this->postJson('/api/register', [
            'nom' => 'Test',
            'prenom' => 'Test',
            'email' => 'faible@example.com',
            'telephone' => '771234570',
            'password' => 'abc',
            'password_confirmation' => 'abc',
        ]);

        $response->assertStatus(422);
    }
}
