<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistema.gov.ao',
            'password' => Hash::make('admin123'),
            'papel' => 'administrador',
            'ativo' => true,
        ]);

        User::create([
            'name' => 'Dr. João Silva',
            'email' => 'medico@sistema.gov.ao',
            'password' => Hash::make('medico123'),
            'papel' => 'medico',
            'ativo' => true,
        ]);
    }
}