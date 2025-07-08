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
        Schema::table('pacientes', function (Blueprint $table) {
            // Make sure endereco is nullable if it exists
            if (Schema::hasColumn('pacientes', 'endereco')) {
                $table->string('endereco')->nullable()->change();
            } else {
                $table->string('endereco')->nullable()->after('sexo');
            }
            
            // Add missing fields if they don't exist
            if (!Schema::hasColumn('pacientes', 'idade')) {
                $table->integer('idade')->nullable()->after('data_nascimento');
            }
            
            if (!Schema::hasColumn('pacientes', 'status')) {
                $table->enum('status', ['aguardando', 'em_atendimento', 'finalizado', 'transferido'])
                      ->default('aguardando')->after('risco');
            }
            
            if (!Schema::hasColumn('pacientes', 'prioridade')) {
                $table->enum('prioridade', ['baixa', 'media', 'alta', 'critica'])
                      ->default('media')->after('status');
            }
            
            if (!Schema::hasColumn('pacientes', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('sintomas');
            }
            
            // Make BI nullable if it's not already
            if (Schema::hasColumn('pacientes', 'bi')) {
                $table->string('bi')->nullable()->change();
            }
            
            // Make estabelecimento_id nullable
            if (Schema::hasColumn('pacientes', 'estabelecimento_id')) {
                $table->foreignId('estabelecimento_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            // Reverse the changes if needed
            if (Schema::hasColumn('pacientes', 'endereco')) {
                $table->string('endereco')->nullable(false)->change();
            }
        });
    }
};
