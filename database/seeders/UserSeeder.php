<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Estabelecimento;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estabelecimentos = Estabelecimento::all();

        // Administrador
        User::create([
            'name' => 'Dr. António Silva',
            'email' => 'admin@sistema.gov.ao',
            'password' => Hash::make('admin123'),
            'papel' => 'administrador',
            'estabelecimento_id' => $estabelecimentos->first()?->id,
            'email_verified_at' => now(),
        ]);

        // Gestores Provinciais
        User::create([
            'name' => 'Dra. Maria Fernandes',
            'email' => 'gestor.luanda@saude.gov.ao',
            'password' => Hash::make('gestor123'),
            'papel' => 'gestor',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Geral de Luanda')?->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Dr. João Baptista',
            'email' => 'gestor.bengo@saude.gov.ao',
            'password' => Hash::make('gestor123'),
            'papel' => 'gestor',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Provincial do Bengo')?->id,
            'email_verified_at' => now(),
        ]);

        // Médicos
        User::create([
            'name' => 'Dr. Carlos Mendes',
            'email' => 'medico1@hospital.gov.ao',
            'password' => Hash::make('medico123'),
            'papel' => 'medico',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Geral de Luanda')?->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Dra. Ana Costa',
            'email' => 'medico2@hospital.gov.ao',
            'password' => Hash::make('medico123'),
            'papel' => 'medico',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Américo Boavida')?->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Dr. Paulo Neto',
            'email' => 'medico3@hospital.gov.ao',
            'password' => Hash::make('medico123'),
            'papel' => 'medico',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Provincial do Bengo')?->id,
            'email_verified_at' => now(),
        ]);

        // Técnicos de Saúde
        User::create([
            'name' => 'Manuel Santos',
            'email' => 'tecnico1@saude.gov.ao',
            'password' => Hash::make('tecnico123'),
            'papel' => 'tecnico',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Centro de Saúde da Ingombota')?->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Isabel Rodrigues',
            'email' => 'tecnico2@saude.gov.ao',
            'password' => Hash::make('tecnico123'),
            'papel' => 'tecnico',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Centro de Saúde de Viana')?->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Roberto Lima',
            'email' => 'tecnico3@saude.gov.ao',
            'password' => Hash::make('tecnico123'),
            'papel' => 'tecnico',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Centro de Saúde de Cacuaco')?->id,
            'email_verified_at' => now(),
        ]);

        // Enfermeiros
        User::create([
            'name' => 'Luísa Pereira',
            'email' => 'enfermeiro1@hospital.gov.ao',
            'password' => Hash::make('enfermeiro123'),
            'papel' => 'enfermeiro',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Geral de Luanda')?->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pedro Gonçalves',
            'email' => 'enfermeiro2@hospital.gov.ao',
            'password' => Hash::make('enfermeiro123'),
            'papel' => 'enfermeiro',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Américo Boavida')?->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Teresa Alves',
            'email' => 'enfermeiro3@saude.gov.ao',
            'password' => Hash::make('enfermeiro123'),
            'papel' => 'enfermeiro',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Posto de Saúde do Sambizanga')?->id,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Usuários criados com sucesso!');
        $this->command->info('Total de usuários: ' . User::count());
    }
}
