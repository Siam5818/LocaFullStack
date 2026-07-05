<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Personne;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['nom' => 'Sow', 'prenom' => 'Aminata', 'email' => 'aminata.sow@example.com', 'telephone' => '771234600'],
            ['nom' => 'Fall', 'prenom' => 'Ibrahima', 'email' => 'ibrahima.fall@example.com', 'telephone' => '771234601'],
        ];

        foreach ($clients as $data) {
            $personne = Personne::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nom'               => $data['nom'],
                    'prenom'            => $data['prenom'],
                    'password'          => Hash::make('Client1234'),
                    'telephone'         => $data['telephone'],
                    'role'              => 'client',
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );

            Client::firstOrCreate(['personne_id' => $personne->id]);
        }
    }
}
