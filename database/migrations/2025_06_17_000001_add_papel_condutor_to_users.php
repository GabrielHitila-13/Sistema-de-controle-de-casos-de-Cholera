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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('papel');
            $table->unsignedBigInteger('veiculo_id')->nullable()->after('estabelecimento_id');
            $table->json('permissoes_extras')->nullable()->after('ativo');
            $table->string('numero_licenca', 50)->nullable()->after('permissoes_extras');
            $table->date('validade_licenca')->nullable()->after('numero_licenca');
            $table->string('telefone', 20)->nullable()->after('validade_licenca');
            $table->text('observacoes')->nullable()->after('telefone');
            
            $table->foreign('veiculo_id')->references('id')->on('veiculos')->onDelete('set null');
            $table->index(['papel', 'ativo']);
            $table->index(['estabelecimento_id', 'papel']);
        });

        // Atualizar enum do papel para incluir novos papéis
        DB::statement("ALTER TABLE users MODIFY COLUMN papel ENUM('administrador', 'gestor', 'medico', 'tecnico', 'enfermeiro', 'condutor', 'visualizacao') NOT NULL DEFAULT 'visualizacao'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['veiculo_id']);
            $table->dropIndex(['papel', 'ativo']);
            $table->dropIndex(['estabelecimento_id', 'papel']);
            $table->dropColumn([
                'ativo', 'veiculo_id', 'permissoes_extras', 'numero_licenca', 
                'validade_licenca', 'telefone', 'observacoes'
            ]);
        });

        // Reverter enum do papel
        DB::statement("ALTER TABLE users MODIFY COLUMN papel ENUM('administrador', 'gestor', 'medico', 'tecnico', 'enfermeiro') NOT NULL DEFAULT 'tecnico'");
    }
};
