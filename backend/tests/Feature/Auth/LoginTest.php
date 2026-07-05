<?php

namespace Tests\Feature\Auth;

use App\Models\Personne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_connexion_reussie_retourne_un_token(): void
    {
        Personne::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password1234'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'Password1234',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token', 'user' => ['id', 'role']]);
    }

    public function test_connexion_refusee_avec_mauvais_mot_de_passe(): void
    {
        Personne::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'MauvaisPassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_connexion_refusee_si_email_non_verifie(): void
    {
        Personne::factory()->nonVerifie()->create([
            'email' => 'nonverifie@example.com',
            'password' => bcrypt('Password1234'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'nonverifie@example.com',
            'password' => 'Password1234',
        ]);

        $response->assertStatus(403);
    }

    public function test_connexion_refusee_si_compte_desactive(): void
    {
        Personne::factory()->inactif()->create([
            'email' => 'inactif@example.com',
            'password' => bcrypt('Password1234'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'inactif@example.com',
            'password' => 'Password1234',
        ]);

        $response->assertStatus(403);
    }

    public function test_rate_limiting_bloque_apres_5_tentatives_echouees(): void
    {
        Personne::factory()->create(['email' => 'test@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', ['email' => 'test@example.com', 'password' => 'Faux']);
        }

        $response = $this->postJson('/api/login', ['email' => 'test@example.com', 'password' => 'Faux']);

        $response->assertStatus(429);
    }
}
