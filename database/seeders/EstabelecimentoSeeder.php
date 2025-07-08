<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Estabelecimento;
use App\Models\Gabinete;

class EstabelecimentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gabinetes = Gabinete::all();

        // Hospitais Principais
        Estabelecimento::create([
            'nome' => 'Hospital Geral de Luanda',
            'tipo' => 'hospital',
            'endereco' => 'Rua Major Kanhangulo, Ingombota, Luanda',
            'telefone' => '+244 222 334 455',
            'email' => 'geral@hgl.gov.ao',
            'capacidade' => 500,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Luanda%')->first()?->id,
          
            'ativo' => true,
        ]);

        Estabelecimento::create([
            'nome' => 'Hospital Américo Boavida',
            'tipo' => 'hospital',
            'endereco' => 'Rua Comandante Gika, Maianga, Luanda',
            'telefone' => '+244 222 445 566',
            'email' => 'boavida@hab.gov.ao',
            'capacidade' => 400,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Luanda%')->first()?->id,
           
            'ativo' => true,
        ]);

        Estabelecimento::create([
            'nome' => 'Hospital Provincial do Bengo',
            'tipo' => 'hospital',
            'endereco' => 'Caxito, Província do Bengo',
            'telefone' => '+244 234 567 890',
            'email' => 'provincial@bengo.gov.ao',
            'capacidade' => 200,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Bengo%')->first()?->id,
          
            'ativo' => true,
        ]);

        // Centros de Saúde
        Estabelecimento::create([
            'nome' => 'Centro de Saúde da Ingombota',
            'tipo' => 'centro_saude',
            'endereco' => 'Bairro Ingombota, Luanda',
            'telefone' => '+244 222 678 901',
            'email' => 'ingombota@cs.gov.ao',
            'capacidade' => 100,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Luanda%')->first()?->id,
         
            'ativo' => true,
        ]);

        Estabelecimento::create([
            'nome' => 'Centro de Saúde de Viana',
            'tipo' => 'centro_saude',
            'endereco' => 'Município de Viana, Luanda',
            'telefone' => '+244 222 789 012',
            'email' => 'viana@cs.gov.ao',
            'capacidade' => 80,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Luanda%')->first()?->id,
 
            'ativo' => true,
        ]);

        Estabelecimento::create([
            'nome' => 'Centro de Saúde de Cacuaco',
            'tipo' => 'centro_saude',
            'endereco' => 'Município de Cacuaco, Luanda',
            'telefone' => '+244 222 890 123',
            'email' => 'cacuaco@cs.gov.ao',
            'capacidade' => 60,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Luanda%')->first()?->id,
           
            'ativo' => true,
        ]);

        // Postos de Saúde
        Estabelecimento::create([
            'nome' => 'Posto de Saúde do Sambizanga',
            'tipo' => 'posto_saude',
            'endereco' => 'Bairro Sambizanga, Luanda',
            'telefone' => '+244 222 901 234',
            'email' => 'sambizanga@ps.gov.ao',
            'capacidade' => 30,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Luanda%')->first()?->id,
            
            'ativo' => true,
        ]);

        Estabelecimento::create([
            'nome' => 'Posto de Saúde de Dande',
            'tipo' => 'posto_saude',
            'endereco' => 'Município de Dande, Bengo',
            'telefone' => '+244 234 012 345',
            'email' => 'dande@ps.gov.ao',
            'capacidade' => 25,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Bengo%')->first()?->id,
            
            'ativo' => true,
        ]);

        Estabelecimento::create([
            'nome' => 'Posto de Saúde de Nambuangongo',
            'tipo' => 'posto_saude',
            'endereco' => 'Município de Nambuangongo, Bengo',
            'telefone' => '+244 234 123 456',
            'email' => 'nambuangongo@ps.gov.ao',
            'capacidade' => 20,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Bengo%')->first()?->id,
            
            'ativo' => true,
        ]);

        // Clínicas Especializadas
        Estabelecimento::create([
            'nome' => 'Clínica de Doenças Infecciosas',
            'tipo' => 'clinica',
            'endereco' => 'Rua Rainha Ginga, Maianga, Luanda',
            'telefone' => '+244 222 234 567',
            'email' => 'infecciosas@clinica.gov.ao',
            'capacidade' => 50,
            'gabinete_id' => $gabinetes->where('nome', 'like', '%Luanda%')->first()?->id,
            
            'ativo' => true,
        ]);

        $this->command->info('Estabelecimentos criados com sucesso!');
    }
}
