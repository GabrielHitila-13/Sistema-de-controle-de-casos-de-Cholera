<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Estabelecimento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar alguns estabelecimentos para vincular usuários
        $estabelecimentos = Estabelecimento::limit(5)->get();

        $users = [
            // Administrador Principal
            [
                'name' => 'Administrador do Sistema',
                'email' => 'admin@sistema.gov.ao',
                'papel' => 'administrador',
                'password' => Hash::make('admin123'),
                'estabelecimento_id' => null,
            ],
            
            // Gestores
            [
                'name' => 'Dr. António Silva - Gestor Provincial',
                'email' => 'gestor.provincial@saude.gov.ao',
                'papel' => 'gestor',
                'password' => Hash::make('gestor123'),
                'estabelecimento_id' => null,
            ],
            [
                'name' => 'Dra. Maria Santos - Gestora Municipal',
                'email' => 'gestor.municipal@saude.gov.ao',
                'papel' => 'gestor',
                'password' => Hash::make('gestor123'),
                'estabelecimento_id' => null,
            ],
            
            // Médicos
            [
                'name' => 'Dr. João Cardoso - Médico Chefe',
                'email' => 'medico.chefe@hospital.gov.ao',
                'papel' => 'medico',
                'password' => Hash::make('medico123'),
                'estabelecimento_id' => $estabelecimentos->first()?->id,
            ],
            [
                'name' => 'Dra. Ana Fernandes - Médica',
                'email' => 'medica@hospital.gov.ao',
                'papel' => 'medico',
                'password' => Hash::make('medico123'),
                'estabelecimento_id' => $estabelecimentos->skip(1)->first()?->id,
            ],
            
            // Técnicos
            [
                'name' => 'Carlos Sousa - Técnico de Saúde',
                'email' => 'tecnico1@saude.gov.ao',
                'papel' => 'tecnico',
                'password' => Hash::make('tecnico123'),
                'estabelecimento_id' => $estabelecimentos->skip(2)->first()?->id,
            ],
            
            // Enfermeiros
            [
                'name' => 'Enfª Luísa Pereira - Enfermeira Chefe',
                'email' => 'enfermeira.chefe@hospital.gov.ao',
                'papel' => 'enfermeiro',
                'password' => Hash::make('enfermeiro123'),
                'estabelecimento_id' => $estabelecimentos->skip(3)->first()?->id,
            ],
        ];

        foreach ($users as $userData) {
            // Verificar se o usuário já existe
            $existingUser = User::where('email', $userData['email'])->first();
            
            if (!$existingUser) {
                $user = User::create($userData);
                
                // Atualizar último acesso para alguns usuários (simulando uso recente)
                if ($user->papel !== 'tecnico') {
                    $horasAtras = rand(1, 48); // Entre 1 e 48 horas atrás
                    $user->update(['ultimo_acesso' => now()->subHours($horasAtras)]);
                }
                
                $this->command->info("Usuário criado: {$userData['email']}");
            } else {
                $this->command->warn("Usuário já existe: {$userData['email']}");
            }
        }

        $this->command->info('Processo de criação de usuários concluído!');
        $this->command->info('- Administradores: ' . User::where('papel', 'administrador')->count());
        $this->command->info('- Gestores: ' . User::where('papel', 'gestor')->count());
        $this->command->info('- Médicos: ' . User::where('papel', 'medico')->count());
        $this->command->info('- Técnicos: ' . User::where('papel', 'tecnico')->count());
        $this->command->info('- Enfermeiros: ' . User::where('papel', 'enfermeiro')->count());
    }
}
