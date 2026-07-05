<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Bailleur;
use App\Models\Personne;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BailleurSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        $bailleurs = [
            ['nom' => 'Diop', 'prenom' => 'Moussa', 'email' => 'moussa.diop@example.com', 'telephone' => '771234500'],
            ['nom' => 'Ndiaye', 'prenom' => 'Fatou', 'email' => 'fatou.ndiaye@example.com', 'telephone' => '771234501'],
        ];

        foreach ($bailleurs as $data) {
            $personne = Personne::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nom'               => $data['nom'],
                    'prenom'            => $data['prenom'],
                    'password'          => Hash::make('Bailleur1234'),
                    'telephone'         => $data['telephone'],
                    'role'              => 'bailleur',
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );

            Bailleur::firstOrCreate(
                ['personne_id' => $personne->id],
                ['created_by_admin_id' => $admin->id]
            );
        }
    }
}
