<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GabineteSeeder::class,
            EstabelecimentoSeeder::class,
            UserSeeder::class,
            VeiculoSeeder::class,
            PacienteSeeder::class,
        ]);
    }
}
