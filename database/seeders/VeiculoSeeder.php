<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Veiculo;
use App\Models\Estabelecimento;

class VeiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estabelecimentos = Estabelecimento::where('tipo', 'hospital')->get();

        // Ambulâncias do Hospital Geral de Luanda
        $hgl = $estabelecimentos->where('nome', 'Hospital Geral de Luanda')->first();
        
        Veiculo::create([
            'placa' => 'LD-001-AM',
            'tipo' => 'ambulancia',
            'status' => 'disponivel',
            'estabelecimento_id' => $hgl?->id,
            'modelo' => 'Mercedes-Benz Sprinter',
            'ano' => 2023,
            'equipamentos' => json_encode([
                'Desfibrilador automático',
                'Monitor cardíaco',
                'Cilindros de oxigênio (2x)',
                'Maca hidráulica',
                'Kit de primeiros socorros completo',
                'Aspirador portátil',
                'Kit de intubação',
                'Medicamentos de emergência'
            ]),
            'latitude' => -8.8390,
            'longitude' => 13.2894,
            'ultima_manutencao' => now()->subDays(15),
            'quilometragem' => 25000,
        ]);

        Veiculo::create([
            'placa' => 'LD-002-AM',
            'tipo' => 'ambulancia',
            'status' => 'em_atendimento',
            'estabelecimento_id' => $hgl?->id,
            'modelo' => 'Ford Transit',
            'ano' => 2022,
            'equipamentos' => json_encode([
                'Monitor de sinais vitais',
                'Cilindros de oxigênio (2x)',
                'Maca dobrável',
                'Kit de trauma',
                'Medicamentos básicos',
                'Prancha rígida',
                'Colar cervical'
            ]),
            'latitude' => -8.8200,
            'longitude' => 13.2500,
            'ultima_manutencao' => now()->subDays(30),
            'quilometragem' => 45000,
        ]);

        // Ambulâncias do Hospital Américo Boavida
        $hab = $estabelecimentos->where('nome', 'Hospital Américo Boavida')->first();
        
        Veiculo::create([
            'placa' => 'LD-003-AM',
            'tipo' => 'ambulancia',
            'status' => 'disponivel',
            'estabelecimento_id' => $hab?->id,
            'modelo' => 'Volkswagen Crafter',
            'ano' => 2021,
            'equipamentos' => json_encode([
                'Ventilador portátil',
                'Monitor multiparamétrico',
                'Bomba de infusão',
                'Cilindros de oxigênio (3x)',
                'Maca de transporte',
                'Kit de reanimação neonatal',
                'Medicamentos de UTI móvel'
            ]),
            'latitude' => -8.8157,
            'longitude' => 13.2302,
            'ultima_manutencao' => now()->subDays(10),
            'quilometragem' => 32000,
        ]);

        Veiculo::create([
            'placa' => 'LD-004-AM',
            'tipo' => 'ambulancia',
            'status' => 'manutencao',
            'estabelecimento_id' => $hab?->id,
            'modelo' => 'Renault Master',
            'ano' => 2020,
            'equipamentos' => json_encode([
                'Monitor cardíaco básico',
                'Cilindros de oxigênio (2x)',
                'Maca simples',
                'Kit de primeiros socorros',
                'Medicamentos básicos'
            ]),
            'latitude' => -8.8157,
            'longitude' => 13.2302,
            'ultima_manutencao' => now()->subDays(60),
            'quilometragem' => 78000,
        ]);

        // Ambulâncias do Hospital Provincial do Bengo
        $bengo = $estabelecimentos->where('nome', 'Hospital Provincial do Bengo')->first();
        
        Veiculo::create([
            'placa' => 'BG-001-AM',
            'tipo' => 'ambulancia',
            'status' => 'disponivel',
            'estabelecimento_id' => $bengo?->id,
            'modelo' => 'Toyota Hiace',
            'ano' => 2022,
            'equipamentos' => json_encode([
                'Monitor de sinais vitais',
                'Cilindros de oxigênio (2x)',
                'Maca hidráulica',
                'Kit de emergência',
                'Medicamentos essenciais',
                'Aspirador manual',
                'Kit de parto'
            ]),
            'latitude' => -8.5783,
            'longitude' => 13.6644,
            'ultima_manutencao' => now()->subDays(20),
            'quilometragem' => 18000,
        ]);

        Veiculo::create([
            'placa' => 'BG-002-AM',
            'tipo' => 'ambulancia',
            'status' => 'indisponivel',
            'estabelecimento_id' => $bengo?->id,
            'modelo' => 'Nissan NV200',
            'ano' => 2019,
            'equipamentos' => json_encode([
                'Monitor básico',
                'Cilindro de oxigênio (1x)',
                'Maca dobrável',
                'Kit básico de primeiros socorros'
            ]),
            'latitude' => -8.5783,
            'longitude' => 13.6644,
            'ultima_manutencao' => now()->subDays(90),
            'quilometragem' => 95000,
        ]);

        // Veículos de Apoio
        Veiculo::create([
            'placa' => 'LD-005-AP',
            'tipo' => 'apoio',
            'status' => 'disponivel',
            'estabelecimento_id' => $hgl?->id,
            'modelo' => 'Toyota Land Cruiser',
            'ano' => 2021,
            'equipamentos' => json_encode([
                'Kit de primeiros socorros',
                'Rádio comunicação',
                'GPS',
                'Kit de ferramentas',
                'Combustível extra'
            ]),
            'latitude' => -8.8390,
            'longitude' => 13.2894,
            'ultima_manutencao' => now()->subDays(25),
            'quilometragem' => 42000,
        ]);

        Veiculo::create([
            'placa' => 'LD-006-AP',
            'tipo' => 'apoio',
            'status' => 'disponivel',
            'estabelecimento_id' => $hab?->id,
            'modelo' => 'Ford Ranger',
            'ano' => 2020,
            'equipamentos' => json_encode([
                'Kit de primeiros socorros',
                'Rádio comunicação',
                'Equipamentos de resgate',
                'Gerador portátil'
            ]),
            'latitude' => -8.8157,
            'longitude' => 13.2302,
            'ultima_manutencao' => now()->subDays(40),
            'quilometragem' => 55000,
        ]);

        $this->command->info('Veículos criados com sucesso!');
    }
}
