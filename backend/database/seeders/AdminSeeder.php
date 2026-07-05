<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Personne;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $personne = Personne::firstOrCreate(
            ['email' => 'admin@locationfullstack.com'],
            [
                'nom'               => 'Sihamoudine',
                'prenom'            => 'Anzize',
                'password'          => Hash::make('Admin1234'),
                'telephone'         => '771112233',
                'role'              => 'admin',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        Admin::firstOrCreate(
            ['personne_id' => $personne->id],
            ['niveau_acces' => 'super_admin']
        );
    }
}
