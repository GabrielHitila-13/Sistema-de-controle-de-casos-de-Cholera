<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Paciente;
use App\Models\Estabelecimento;
use App\Services\ColeraDetectionService;
use Carbon\Carbon;

class PacienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estabelecimentos = Estabelecimento::all();
        $coleraService = new ColeraDetectionService();

        // Função para gerar BI angolano
        $gerarBI = function() {
            $provincias = ['LA', 'BE', 'LU', 'HU', 'BI', 'CN', 'CC', 'CS', 'CU', 'HI', 'KS', 'KK', 'KN', 'LN', 'ML', 'MX', 'NM', 'UG'];
            $provincia = $provincias[array_rand($provincias)];
            $numero = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $sequencia = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $digito = rand(0, 9);
            return $numero . $provincia . $sequencia . $digito;
        };

        // Casos confirmados de cólera
        $dataNascimento1 = Carbon::now()->subYears(34)->subMonths(rand(1, 12))->subDays(rand(1, 28));
        $paciente1 = Paciente::create([
            'nome' => 'Maria João Santos',
            'bi' => $gerarBI(),
            'idade' => 34,
            'data_nascimento' => $dataNascimento1,
            'sexo' => 'feminino',
            'endereco' => 'Bairro Sambizanga, Luanda',
            'telefone' => '+244 923 111 222',
            'sintomas' => 'Diarreia aquosa profusa, vômitos, desidratação severa, cãibras musculares',
            'observacoes' => 'Paciente apresentou sintomas há 2 dias. Histórico de consumo de água de fonte não tratada.',
            'risco' => 'alto',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Geral de Luanda')?->id ?? $estabelecimentos->first()->id,
            'data_triagem' => now()->subDays(2),
            'contato_caso_confirmado' => true,
            'area_surto' => true,
            'agua_contaminada' => true,
            'numero_caso' => 'COL-2024-001',
        ]);

        $resultado1 = $coleraService->avaliarPaciente($paciente1);
        $paciente1->update([
            'diagnostico_colera' => $resultado1['diagnostico'],
            'probabilidade_colera' => $resultado1['probabilidade'],
            'data_diagnostico' => now()->subDays(2),
            'sintomas_colera' => $resultado1['sintomas_identificados'],
            'fatores_risco' => $resultado1['fatores_risco'],
            'recomendacoes' => $resultado1['recomendacoes'],
        ]);

        $dataNascimento2 = Carbon::now()->subYears(45)->subMonths(rand(1, 12))->subDays(rand(1, 28));
        $paciente2 = Paciente::create([
            'nome' => 'António Fernandes',
            'bi' => $gerarBI(),
            'idade' => 45,
            'data_nascimento' => $dataNascimento2,
            'sexo' => 'masculino',
            'endereco' => 'Município de Viana, Luanda',
            'telefone' => '+244 924 333 444',
            'sintomas' => 'Diarreia líquida abundante, vômitos frequentes, desidratação, fraqueza extrema',
            'observacoes' => 'Paciente trabalha no mercado do peixe. Início dos sintomas há 3 dias.',
            'risco' => 'alto',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Hospital Américo Boavida')?->id ?? $estabelecimentos->first()->id,
            'data_triagem' => now()->subDays(3),
            'contato_caso_confirmado' => false,
            'area_surto' => true,
            'agua_contaminada' => true,
            'numero_caso' => 'COL-2024-002',
        ]);

        $resultado2 = $coleraService->avaliarPaciente($paciente2);
        $paciente2->update([
            'diagnostico_colera' => $resultado2['diagnostico'],
            'probabilidade_colera' => $resultado2['probabilidade'],
            'data_diagnostico' => now()->subDays(3),
            'sintomas_colera' => $resultado2['sintomas_identificados'],
            'fatores_risco' => $resultado2['fatores_risco'],
            'recomendacoes' => $resultado2['recomendacoes'],
        ]);

        // Casos prováveis
        $dataNascimento3 = Carbon::now()->subYears(28)->subMonths(rand(1, 12))->subDays(rand(1, 28));
        $paciente3 = Paciente::create([
            'nome' => 'Luísa Pereira',
            'bi' => $gerarBI(),
            'idade' => 28,
            'data_nascimento' => $dataNascimento3,
            'sexo' => 'feminino',
            'endereco' => 'Bairro Ingombota, Luanda',
            'telefone' => '+244 925 555 666',
            'sintomas' => 'Diarreia aquosa, vômitos ocasionais, desidratação leve',
            'observacoes' => 'Paciente visitou área com casos confirmados há 1 semana.',
            'risco' => 'medio',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Centro de Saúde da Ingombota')?->id ?? $estabelecimentos->first()->id,
            'data_triagem' => now()->subDays(1),
            'contato_caso_confirmado' => true,
            'area_surto' => false,
            'agua_contaminada' => false,
            'numero_caso' => 'COL-2024-003',
        ]);

        $resultado3 = $coleraService->avaliarPaciente($paciente3);
        $paciente3->update([
            'diagnostico_colera' => $resultado3['diagnostico'],
            'probabilidade_colera' => $resultado3['probabilidade'],
            'data_diagnostico' => now()->subDays(1),
            'sintomas_colera' => $resultado3['sintomas_identificados'],
            'fatores_risco' => $resultado3['fatores_risco'],
            'recomendacoes' => $resultado3['recomendacoes'],
        ]);

        // Casos suspeitos
        $dataNascimento4 = Carbon::now()->subYears(52)->subMonths(rand(1, 12))->subDays(rand(1, 28));
        $paciente4 = Paciente::create([
            'nome' => 'Carlos Mendes',
            'bi' => $gerarBI(),
            'idade' => 52,
            'data_nascimento' => $dataNascimento4,
            'sexo' => 'masculino',
            'endereco' => 'Município de Cacuaco, Luanda',
            'telefone' => '+244 926 777 888',
            'sintomas' => 'Diarreia, náuseas, dor abdominal',
            'observacoes' => 'Sintomas iniciaram ontem. Paciente nega contato com casos conhecidos.',
            'risco' => 'baixo',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Centro de Saúde de Cacuaco')?->id ?? $estabelecimentos->first()->id,
            'data_triagem' => now()->subHours(12),
            'contato_caso_confirmado' => false,
            'area_surto' => false,
            'agua_contaminada' => true,
            'numero_caso' => 'COL-2024-004',
        ]);

        $resultado4 = $coleraService->avaliarPaciente($paciente4);
        $paciente4->update([
            'diagnostico_colera' => $resultado4['diagnostico'],
            'probabilidade_colera' => $resultado4['probabilidade'],
            'data_diagnostico' => now()->subHours(12),
            'sintomas_colera' => $resultado4['sintomas_identificados'],
            'fatores_risco' => $resultado4['fatores_risco'],
            'recomendacoes' => $resultado4['recomendacoes'],
        ]);

        // Casos descartados
        $dataNascimento5 = Carbon::now()->subYears(31)->subMonths(rand(1, 12))->subDays(rand(1, 28));
        $paciente5 = Paciente::create([
            'nome' => 'Ana Costa',
            'bi' => $gerarBI(),
            'idade' => 31,
            'data_nascimento' => $dataNascimento5,
            'sexo' => 'feminino',
            'endereco' => 'Bairro Maianga, Luanda',
            'telefone' => '+244 927 999 000',
            'sintomas' => 'Dor de cabeça, febre baixa, mal-estar geral',
            'observacoes' => 'Paciente com sintomas gripais. Sem sintomas gastrointestinais.',
            'risco' => 'baixo',
            'estabelecimento_id' => $estabelecimentos->firstWhere('nome', 'Posto de Saúde do Sambizanga')?->id ?? $estabelecimentos->first()->id,
            'data_triagem' => now()->subHours(6),
            'contato_caso_confirmado' => false,
            'area_surto' => false,
            'agua_contaminada' => false,
            'numero_caso' => 'COL-2024-005',
        ]);

        $resultado5 = $coleraService->avaliarPaciente($paciente5);
        $paciente5->update([
            'diagnostico_colera' => $resultado5['diagnostico'],
            'probabilidade_colera' => $resultado5['probabilidade'],
            'data_diagnostico' => now()->subHours(6),
            'sintomas_colera' => $resultado5['sintomas_identificados'],
            'fatores_risco' => $resultado5['fatores_risco'],
            'recomendacoes' => $resultado5['recomendacoes'],
        ]);

        // Mais casos para estatísticas
        $nomes = [
            'João Silva', 'Pedro Santos', 'Manuel Rodrigues', 'Francisco Lima',
            'Teresa Alves', 'Isabel Ferreira', 'Rosa Gonçalves', 'Catarina Nunes',
            'Miguel Teixeira', 'Rui Cardoso', 'Paulo Martins', 'André Sousa',
            'Cristina Oliveira', 'Fernanda Pinto', 'Sónia Ribeiro', 'Carla Dias',
            'José Pereira', 'Luís Correia', 'Mário Lopes', 'Bruno Machado'
        ];

        for ($i = 6; $i <= 25; $i++) {
            $idade = rand(18, 70);
            $dataNascimento = Carbon::now()->subYears($idade)->subMonths(rand(1, 12))->subDays(rand(1, 28));
            
            $sintomas_variedade = [
                'Diarreia aquosa, vômitos',
                'Diarreia, desidratação leve',
                'Vômitos, dor abdominal',
                'Diarreia líquida, fraqueza',
                'Náuseas, diarreia ocasional',
                'Dor de estômago, mal-estar',
                'Febre baixa, diarreia',
                'Vômitos frequentes, desidratação'
            ];

            $enderecos = [
                'Bairro Rangel, Luanda',
                'Município de Belas, Luanda',
                'Bairro Alvalade, Luanda',
                'Município de Icolo e Bengo',
                'Bairro Miramar, Luanda',
                'Município de Quiçama, Luanda'
            ];

            $paciente = Paciente::create([
                'nome' => $nomes[array_rand($nomes)],
                'bi' => $gerarBI(),
                'idade' => $idade,
                'data_nascimento' => $dataNascimento,
                'sexo' => rand(0, 1) ? 'masculino' : 'feminino',
                'endereco' => $enderecos[array_rand($enderecos)],
                'telefone' => '+244 9' . rand(20, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'sintomas' => $sintomas_variedade[array_rand($sintomas_variedade)],
                'observacoes' => 'Caso registrado para monitoramento epidemiológico.',
                'risco' => ['baixo', 'medio', 'alto'][rand(0, 2)],
                'estabelecimento_id' => $estabelecimentos->random()->id,
                'data_triagem' => now()->subDays(rand(1, 30)),
                'contato_caso_confirmado' => rand(0, 1),
                'area_surto' => rand(0, 1),
                'agua_contaminada' => rand(0, 1),
                'numero_caso' => 'COL-2024-' . str_pad($i, 3, '0', STR_PAD_LEFT),
            ]);

            $resultado = $coleraService->avaliarPaciente($paciente);
            $paciente->update([
                'diagnostico_colera' => $resultado['diagnostico'],
                'probabilidade_colera' => $resultado['probabilidade'],
                'data_diagnostico' => $paciente->data_triagem,
                'sintomas_colera' => $resultado['sintomas_identificados'],
                'fatores_risco' => $resultado['fatores_risco'],
                'recomendacoes' => $resultado['recomendacoes'],
            ]);
        }

        $this->command->info('Pacientes criados com sucesso!');
        $this->command->info('Total de pacientes: ' . Paciente::count());
        $this->command->info('Casos confirmados: ' . Paciente::where('diagnostico_colera', 'confirmado')->count());
        $this->command->info('Casos prováveis: ' . Paciente::where('diagnostico_colera', 'provavel')->count());
        $this->command->info('Casos suspeitos: ' . Paciente::where('diagnostico_colera', 'suspeito')->count());
    }
}
