<?php

namespace Database\Seeders;

use App\Models\Estabelecimento;
use App\Models\Gabinete;
use Illuminate\Database\Seeder;

class EstabelecimentoSeeder extends Seeder
{
    public function run(): void
    {
        $gabinetes = Gabinete::all();
        
        if ($gabinetes->isEmpty()) {
            $this->command->warn('Nenhum gabinete encontrado. Execute primeiro o GabineteSeeder.');
            return;
        }

        $estabelecimentos = [
            // Hospitais Gerais
            [
                'nome' => 'Hospital Geral de Luanda',
                'categoria' => 'geral',
                'endereco' => 'Rua 17 de Setembro, Luanda',
                'telefone' => '+244 222 330 100',
                'capacidade' => 500,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Luanda',
            ],
            [
                'nome' => 'Hospital Américo Boavida',
                'categoria' => 'geral',
                'endereco' => 'Maianga, Luanda',
                'telefone' => '+244 222 330 200',
                'capacidade' => 400,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Luanda',
            ],
            [
                'nome' => 'Hospital Geral do Huambo',
                'categoria' => 'geral',
                'endereco' => 'Centro da Cidade, Huambo',
                'telefone' => '+244 241 220 100',
                'capacidade' => 300,
                'gabinete_nome' => 'Gabinete Provincial de Saúde do Huambo',
            ],

            // Hospitais Municipais
            [
                'nome' => 'Hospital Municipal de Viana',
                'categoria' => 'municipal',
                'endereco' => 'Viana, Luanda',
                'telefone' => '+244 222 445 100',
                'capacidade' => 150,
                'gabinete_nome' => 'Gabinete Municipal de Saúde de Viana',
            ],
            [
                'nome' => 'Hospital Municipal de Cacuaco',
                'categoria' => 'municipal',
                'endereco' => 'Cacuaco, Luanda',
                'telefone' => '+244 222 556 100',
                'capacidade' => 120,
                'gabinete_nome' => 'Gabinete Municipal de Saúde de Cacuaco',
            ],

            // Centros de Saúde
            [
                'nome' => 'Centro de Saúde da Maianga',
                'categoria' => 'centro',
                'endereco' => 'Maianga, Luanda',
                'telefone' => '+244 222 334 001',
                'capacidade' => 80,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Luanda',
            ],
            [
                'nome' => 'Centro de Saúde de Ingombota',
                'categoria' => 'centro',
                'endereco' => 'Ingombota, Luanda',
                'telefone' => '+244 222 334 002',
                'capacidade' => 60,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Luanda',
            ],
            [
                'nome' => 'Centro de Saúde do Bengo',
                'categoria' => 'centro',
                'endereco' => 'Caxito, Bengo',
                'telefone' => '+244 234 567 001',
                'capacidade' => 50,
                'gabinete_nome' => 'Gabinete Provincial de Saúde do Bengo',
            ],

            // Postos de Saúde
            [
                'nome' => 'Posto de Saúde de Cazenga',
                'categoria' => 'posto',
                'endereco' => 'Cazenga, Luanda',
                'telefone' => '+244 222 667 001',
                'capacidade' => 30,
                'gabinete_nome' => 'Gabinete Municipal de Saúde de Cazenga',
            ],
            [
                'nome' => 'Posto de Saúde de Sambizanga',
                'categoria' => 'posto',
                'endereco' => 'Sambizanga, Luanda',
                'telefone' => '+244 222 334 003',
                'capacidade' => 25,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Luanda',
            ],
            [
                'nome' => 'Posto de Saúde de Malanje Centro',
                'categoria' => 'posto',
                'endereco' => 'Centro, Malanje',
                'telefone' => '+244 251 123 001',
                'capacidade' => 40,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Malanje',
            ],

            // Clínicas
            [
                'nome' => 'Clínica Sagrada Esperança',
                'categoria' => 'clinica',
                'endereco' => 'Talatona, Luanda',
                'telefone' => '+244 222 000 100',
                'capacidade' => 100,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Luanda',
            ],
            [
                'nome' => 'Clínica Girassol',
                'categoria' => 'clinica',
                'endereco' => 'Miramar, Luanda',
                'telefone' => '+244 222 000 200',
                'capacidade' => 80,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Luanda',
            ],

            // Outros estabelecimentos em diferentes províncias
            [
                'nome' => 'Hospital Regional de Cabinda',
                'categoria' => 'geral',
                'endereco' => 'Centro, Cabinda',
                'telefone' => '+244 231 567 100',
                'capacidade' => 200,
                'gabinete_nome' => 'Gabinete Provincial de Saúde de Cabinda',
            ],
            [
                'nome' => 'Centro de Saúde do Uíge',
                'categoria' => 'centro',
                'endereco' => 'Centro, Uíge',
                'telefone' => '+244 232 678 100',
                'capacidade' => 70,
                'gabinete_nome' => 'Gabinete Provincial de Saúde do Uíge',
            ],
            [
                'nome' => 'Hospital da Huíla',
                'categoria' => 'geral',
                'endereco' => 'Lubango, Huíla',
                'telefone' => '+244 261 456 100',
                'capacidade' => 250,
                'gabinete_nome' => 'Gabinete Provincial de Saúde da Huíla',
            ],
        ];

        foreach ($estabelecimentos as $estabelecimentoData) {
            $gabinete = $gabinetes->where('nome', $estabelecimentoData['gabinete_nome'])->first();
            
            if ($gabinete) {
                // Verificar se o estabelecimento já existe
                $existingEstabelecimento = Estabelecimento::where('nome', $estabelecimentoData['nome'])->first();
                
                if (!$existingEstabelecimento) {
                    Estabelecimento::create([
                        'nome' => $estabelecimentoData['nome'],
                        'categoria' => $estabelecimentoData['categoria'],
                        'endereco' => $estabelecimentoData['endereco'],
                        'telefone' => $estabelecimentoData['telefone'],
                        'capacidade' => $estabelecimentoData['capacidade'],
                        'gabinete_id' => $gabinete->id,
                    ]);
                    $this->command->info("Estabelecimento criado: {$estabelecimentoData['nome']}");
                } else {
                    $this->command->warn("Estabelecimento já existe: {$estabelecimentoData['nome']}");
                }
            } else {
                $this->command->error("Gabinete não encontrado: {$estabelecimentoData['gabinete_nome']}");
            }
        }

        $this->command->info('Estabelecimentos processados com sucesso!');
    }
}
