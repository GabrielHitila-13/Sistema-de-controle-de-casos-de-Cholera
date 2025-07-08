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
            $table->enum('diagnostico_colera', ['pendente', 'suspeito', 'provavel', 'confirmado', 'descartado'])
                  ->default('pendente')->after('risco');
            $table->decimal('probabilidade_colera', 5, 2)->nullable()->after('diagnostico_colera');
            $table->timestamp('data_diagnostico')->nullable()->after('probabilidade_colera');
            $table->text('sintomas_colera')->nullable()->after('data_diagnostico');
            $table->text('fatores_risco')->nullable()->after('sintomas_colera');
            $table->text('recomendacoes')->nullable()->after('fatores_risco');
            $table->string('numero_caso', 50)->unique()->nullable()->after('recomendacoes');
            $table->boolean('contato_caso_confirmado')->default(false)->after('numero_caso');
            $table->boolean('area_surto')->default(false)->after('contato_caso_confirmado');
            $table->boolean('agua_contaminada')->default(false)->after('area_surto');
            
            $table->index(['diagnostico_colera', 'data_diagnostico']);
            $table->index(['risco', 'diagnostico_colera']);
            $table->index('numero_caso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropIndex(['diagnostico_colera', 'data_diagnostico']);
            $table->dropIndex(['risco', 'diagnostico_colera']);
            $table->dropIndex('numero_caso');
            $table->dropColumn([
                'diagnostico_colera', 'probabilidade_colera', 'data_diagnostico',
                'sintomas_colera', 'fatores_risco', 'recomendacoes', 'numero_caso',
                'contato_caso_confirmado', 'area_surto', 'agua_contaminada'
            ]);
        });
    }
};
