<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TypeProprieteSeeder::class,
            ServiceSeeder::class,
            TypeContratSeeder::class,
            EquipementSeeder::class,
            AdminSeeder::class,
            BailleurSeeder::class,
            ClientSeeder::class,
            ProprieteSeeder::class,
        ]);
    }
}
