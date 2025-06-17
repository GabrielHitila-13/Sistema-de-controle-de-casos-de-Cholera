<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\Estabelecimento;
use Illuminate\Database\Seeder;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        // Primeiro, vamos garantir que temos estabelecimentos
        $estabelecimentos = Estabelecimento::all();
        
        if ($estabelecimentos->isEmpty()) {
            $this->command->warn('Nenhum estabelecimento encontrado. Execute primeiro o GabineteSeeder e EstabelecimentoSeeder.');
            return;
        }

        $pacientes = [
            // CASOS DE ALTO RISCO - Urgentes
            [
                'nome' => 'Maria Santos Silva',
                'bi' => '004567890LA041',
                'telefone' => '+244 923 456 789',
                'data_nascimento' => '1985-03-15',
                'sexo' => 'feminino',
                'sintomas' => 'diarreia aquosa, vomito, desidratacao, fraqueza',
                'risco' => 'alto',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(2),
            ],
            [
                'nome' => 'João Manuel Cardoso',
                'bi' => '005678901LA042',
                'telefone' => '+244 924 567 890',
                'data_nascimento' => '1978-07-22',
                'sexo' => 'masculino',
                'sintomas' => 'diarreia aquosa abundante, vomito intenso, sinais graves de desidratacao, prostração',
                'risco' => 'alto',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subMinutes(30),
            ],
            [
                'nome' => 'Ana Cristina Fernandes',
                'bi' => '006789012LA043',
                'telefone' => '+244 925 678 901',
                'data_nascimento' => '1992-11-08',
                'sexo' => 'feminino',
                'sintomas' => 'diarreia aquosa, desidratacao severa, vomito, dor abdominal intensa',
                'risco' => 'alto',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(1),
            ],

            // CASOS DE MÉDIO RISCO - Atenção
            [
                'nome' => 'Pedro António Neto',
                'bi' => '007890123LA044',
                'telefone' => '+244 926 789 012',
                'data_nascimento' => '1990-05-12',
                'sexo' => 'masculino',
                'sintomas' => 'diarreia, vomito, febre baixa',
                'risco' => 'medio',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(4),
            ],
            [
                'nome' => 'Luísa Domingos Pereira',
                'bi' => '008901234LA045',
                'telefone' => '+244 927 890 123',
                'data_nascimento' => '1987-09-25',
                'sexo' => 'feminino',
                'sintomas' => 'vomito frequente, dor abdominal, fraqueza moderada',
                'risco' => 'medio',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(6),
            ],
            [
                'nome' => 'Carlos Eduardo Sousa',
                'bi' => '009012345LA046',
                'telefone' => '+244 928 901 234',
                'data_nascimento' => '1995-01-30',
                'sexo' => 'masculino',
                'sintomas' => 'diarreia moderada, vomito ocasional, mal-estar geral',
                'risco' => 'medio',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(3),
            ],

            // CASOS DE BAIXO RISCO - Monitoramento
            [
                'nome' => 'Isabel Rodrigues Costa',
                'bi' => '010123456LA047',
                'telefone' => '+244 929 012 345',
                'data_nascimento' => '1993-12-18',
                'sexo' => 'feminino',
                'sintomas' => 'febre baixa, dor abdominal leve',
                'risco' => 'baixo',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(8),
            ],
            [
                'nome' => 'Miguel Ângelo Tavares',
                'bi' => '011234567LA048',
                'telefone' => '+244 930 123 456',
                'data_nascimento' => '1989-04-03',
                'sexo' => 'masculino',
                'sintomas' => 'mal-estar geral, dor de cabeça',
                'risco' => 'baixo',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(12),
            ],
            [
                'nome' => 'Fernanda Lopes Mendes',
                'bi' => '012345678LA049',
                'telefone' => '+244 931 234 567',
                'data_nascimento' => '1991-08-14',
                'sexo' => 'feminino',
                'sintomas' => 'febre leve, fraqueza',
                'risco' => 'baixo',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(10),
            ],

            // CASOS ESPECIAIS - Diferentes faixas etárias
            [
                'nome' => 'Esperança Mateus (Idosa)',
                'bi' => '013456789LA050',
                'telefone' => '+244 932 345 678',
                'data_nascimento' => '1955-06-20', // 68 anos
                'sexo' => 'feminino',
                'sintomas' => 'diarreia aquosa, vomito, desidratacao moderada',
                'risco' => 'alto', // Alto risco devido à idade
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(5),
            ],
            [
                'nome' => 'Benedito Augusto (Idoso)',
                'bi' => '014567890LA051',
                'telefone' => '+244 933 456 789',
                'data_nascimento' => '1948-02-10', // 75 anos
                'sexo' => 'masculino',
                'sintomas' => 'diarreia, vomito, fraqueza extrema',
                'risco' => 'alto',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(7),
            ],
            [
                'nome' => 'Júlia Nascimento (Jovem)',
                'bi' => '015678901LA052',
                'telefone' => '+244 934 567 890',
                'data_nascimento' => '2005-10-12', // 18 anos
                'sexo' => 'feminino',
                'sintomas' => 'diarreia leve, náuseas',
                'risco' => 'baixo',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(14),
            ],

            // CASOS SEM TELEFONE (Vulneráveis)
            [
                'nome' => 'António Domingos (Sem contato)',
                'bi' => '016789012LA053',
                'telefone' => null,
                'data_nascimento' => '1982-03-28',
                'sexo' => 'masculino',
                'sintomas' => 'diarreia aquosa, vomito, sinais de desidratacao',
                'risco' => 'alto',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(9),
            ],
            [
                'nome' => 'Rosa Maria (Sem contato)',
                'bi' => '017890123LA054',
                'telefone' => null,
                'data_nascimento' => '1975-11-15',
                'sexo' => 'feminino',
                'sintomas' => 'febre, dor abdominal, mal-estar',
                'risco' => 'medio',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(16),
            ],

            // CASOS RECENTES (Últimas 24h)
            [
                'nome' => 'Francisco Silva Novo',
                'bi' => '018901234LA055',
                'telefone' => '+244 935 678 901',
                'data_nascimento' => '1988-07-05',
                'sexo' => 'masculino',
                'sintomas' => 'diarreia aquosa abundante, vomito intenso, desidratacao',
                'risco' => 'alto',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subMinutes(45),
            ],
            [
                'nome' => 'Catarina Lopes Recente',
                'bi' => '019012345LA056',
                'telefone' => '+244 936 789 012',
                'data_nascimento' => '1994-12-22',
                'sexo' => 'feminino',
                'sintomas' => 'vomito, dor abdominal, febre',
                'risco' => 'medio',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(2),
            ],

            // CASOS DE DIFERENTES PROVÍNCIAS (se houver estabelecimentos)
            [
                'nome' => 'Manuel Benguela Costa',
                'bi' => '020123456LA057',
                'telefone' => '+244 937 890 123',
                'data_nascimento' => '1986-05-18',
                'sexo' => 'masculino',
                'sintomas' => 'diarreia, febre alta, fraqueza',
                'risco' => 'medio',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subHours(20),
            ],
            [
                'nome' => 'Glória Huambo Ferreira',
                'bi' => '021234567LA058',
                'telefone' => '+244 938 901 234',
                'data_nascimento' => '1990-09-07',
                'sexo' => 'feminino',
                'sintomas' => 'sintomas leves, monitoramento preventivo',
                'risco' => 'baixo',
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subDays(1),
            ],
        ];

        foreach ($pacientes as $dadosPaciente) {
            // Verificar se o paciente já existe
            $existingPaciente = Paciente::where('bi', $dadosPaciente['bi'])->first();
            
            if (!$existingPaciente) {
                $paciente = Paciente::create($dadosPaciente);

                // Gerar QR Code
                $this->gerarQrCode($paciente);
                
                $this->command->info("Paciente criado: {$dadosPaciente['nome']}");
            } else {
                $this->command->warn("Paciente já existe: {$dadosPaciente['nome']} (BI: {$dadosPaciente['bi']})");
            }
        }

        $this->command->info('Pacientes processados com sucesso!');
        $this->command->info('- Alto risco: ' . Paciente::where('risco', 'alto')->count() . ' pacientes');
        $this->command->info('- Médio risco: ' . Paciente::where('risco', 'medio')->count() . ' pacientes');
        $this->command->info('- Baixo risco: ' . Paciente::where('risco', 'baixo')->count() . ' pacientes');
    }

    private function gerarQrCode($paciente)
    {
        $qrData = json_encode([
            'id' => $paciente->id,
            'nome' => $paciente->nome,
            'bi' => $paciente->bi,
            'telefone' => $paciente->telefone,
            'risco' => $paciente->risco,
            'data_triagem' => $paciente->data_triagem?->format('Y-m-d H:i:s'),
            'estabelecimento' => $paciente->estabelecimento->nome ?? null,
        ]);

        try {
            $qrCode = QrCode::size(200)->generate($qrData);
            $paciente->update(['qr_code' => base64_encode($qrCode)]);
        } catch (\Exception $e) {
            // Se falhar na geração do QR Code, continua sem ele
            $this->command->warn("Erro ao gerar QR Code para paciente {$paciente->nome}: " . $e->getMessage());
        }
    }
}
