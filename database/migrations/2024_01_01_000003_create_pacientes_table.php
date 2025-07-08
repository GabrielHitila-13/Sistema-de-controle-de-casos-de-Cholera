<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('bi')->unique()->nullable(); // Make BI nullable as it might not always be available
            $table->string('telefone')->nullable();
            $table->date('data_nascimento');
            $table->enum('sexo', ['masculino', 'feminino']);
            $table->string('endereco')->nullable(); // Make endereco nullable to prevent the error
            $table->integer('idade')->nullable(); // Add idade field as nullable
            $table->foreignId('estabelecimento_id')->nullable()->constrained('estabelecimentos')->onDelete('set null');
            $table->text('sintomas')->nullable();
            $table->text('observacoes')->nullable();
            $table->enum('risco', ['baixo', 'medio', 'alto'])->default('baixo');
            $table->enum('status', ['aguardando', 'em_atendimento', 'finalizado', 'transferido'])->default('aguardando');
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'critica'])->default('media');
            $table->timestamp('data_triagem')->nullable();
            $table->longText('qr_code')->nullable();
            
            // Cholera-specific fields
            $table->enum('diagnostico_colera', ['pendente', 'suspeito', 'provavel', 'confirmado', 'descartado'])->default('pendente');
            $table->decimal('probabilidade_colera', 5, 2)->nullable();
            $table->timestamp('data_diagnostico')->nullable();
            $table->json('sintomas_colera')->nullable();
            $table->json('fatores_risco')->nullable();
            $table->text('recomendacoes')->nullable();
            $table->string('numero_caso')->nullable();
            $table->boolean('contato_caso_confirmado')->default(false);
            $table->boolean('area_surto')->default(false);
            $table->boolean('agua_contaminada')->default(false);
            
            // Vehicle and hospital assignment
            $table->foreignId('veiculo_id')->nullable()->constrained('veiculos')->onDelete('set null');
            $table->foreignId('hospital_destino_id')->nullable()->constrained('estabelecimentos')->onDelete('set null');
            $table->foreignId('ponto_atendimento_id')->nullable()->constrained('pontos_atendimento')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['risco', 'status']);
            $table->index(['diagnostico_colera', 'data_diagnostico']);
            $table->index(['estabelecimento_id', 'created_at']);
            $table->index('data_triagem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
