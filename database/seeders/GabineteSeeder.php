<?php

namespace Database\Seeders;

use App\Models\Gabinete;
use Illuminate\Database\Seeder;

class GabineteSeeder extends Seeder
{
    public function run(): void
    {
        $gabinetes = [
            // Gabinetes Provinciais
            [
                'nome' => 'Gabinete Provincial de Saúde de Luanda',
                'tipo' => 'provincial',
                'endereco' => 'Rua Rainha Ginga, Luanda',
                'telefone' => '+244 222 334 455',
                'latitude' => -8.8383,
                'longitude' => 13.2344,
            ],
            [
                'nome' => 'Gabinete Provincial de Saúde do Bengo',
                'tipo' => 'provincial',
                'endereco' => 'Caxito, Província do Bengo',
                'telefone' => '+244 234 567 890',
                'latitude' => -8.5783,
                'longitude' => 13.6644,
            ],
            [
                'nome' => 'Gabinete Provincial de Saúde de Malanje',
                'tipo' => 'provincial',
                'endereco' => 'Cidade de Malanje',
                'telefone' => '+244 251 123 456',
                'latitude' => -9.5402,
                'longitude' => 16.3410,
            ],
            [
                'nome' => 'Gabinete Provincial de Saúde do Cuanza Sul',
                'tipo' => 'provincial',
                'endereco' => 'Sumbe, Cuanza Sul',
                'telefone' => '+244 271 234 567',
                'latitude' => -11.2058,
                'longitude' => 13.8432,
            ],
            [
                'nome' => 'Gabinete Provincial de Saúde do Huambo',
                'tipo' => 'provincial',
                'endereco' => 'Cidade do Huambo',
                'telefone' => '+244 241 345 678',
                'latitude' => -12.7756,
                'longitude' => 15.7396,
            ],
            [
                'nome' => 'Gabinete Provincial de Saúde da Huíla',
                'tipo' => 'provincial',
                'endereco' => 'Lubango, Huíla',
                'telefone' => '+244 261 456 789',
                'latitude' => -14.9177,
                'longitude' => 13.4925,
            ],
            [
                'nome' => 'Gabinete Provincial de Saúde de Cabinda',
                'tipo' => 'provincial',
                'endereco' => 'Cidade de Cabinda',
                'telefone' => '+244 231 567 890',
                'latitude' => -5.5500,
                'longitude' => 12.2000,
            ],
            [
                'nome' => 'Gabinete Provincial de Saúde do Uíge',
                'tipo' => 'provincial',
                'endereco' => 'Uíge, Província do Uíge',
                'telefone' => '+244 232 678 901',
                'latitude' => -7.6086,
                'longitude' => 15.0564,
            ],
            [
                'nome' => 'Gabinete Provincial de Saúde do Zaire',
                'tipo' => 'provincial',
                'endereco' => 'M\'banza-Kongo, Zaire',
                'telefone' => '+244 242 789 012',
                'latitude' => -6.2692,
                'longitude' => 14.2428,
            ],

            // Gabinetes Municipais (alguns exemplos)
            [
                'nome' => 'Gabinete Municipal de Saúde de Viana',
                'tipo' => 'municipal',
                'endereco' => 'Viana, Luanda',
                'telefone' => '+244 222 445 566',
                'latitude' => -8.8833,
                'longitude' => 13.3667,
            ],
            [
                'nome' => 'Gabinete Municipal de Saúde de Cacuaco',
                'tipo' => 'municipal',
                'endereco' => 'Cacuaco, Luanda',
                'telefone' => '+244 222 556 677',
                'latitude' => -8.7833,
                'longitude' => 13.3667,
            ],
            [
                'nome' => 'Gabinete Municipal de Saúde de Cazenga',
                'tipo' => 'municipal',
                'endereco' => 'Cazenga, Luanda',
                'telefone' => '+244 222 667 788',
                'latitude' => -8.8500,
                'longitude' => 13.2833,
            ],
        ];

        foreach ($gabinetes as $gabineteData) {
            // Verificar se o gabinete já existe
            $existingGabinete = Gabinete::where('nome', $gabineteData['nome'])->first();
            
            if (!$existingGabinete) {
                Gabinete::create($gabineteData);
                $this->command->info("Gabinete criado: {$gabineteData['nome']}");
            } else {
                $this->command->warn("Gabinete já existe: {$gabineteData['nome']}");
            }
        }

        $this->command->info('Gabinetes processados com sucesso!');
    }
}
