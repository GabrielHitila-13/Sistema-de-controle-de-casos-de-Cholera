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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa')->unique();
            $table->enum('tipo', ['ambulancia','apoio', 'outro'])->default('ambulancia');

            // Corrigido para incluir 'indisponivel'
            $table->enum('status', ['disponivel', 'em_atendimento', 'manutencao', 'indisponivel'])->default('disponivel');

            $table->string('modelo')->nullable();
            $table->year('ano')->nullable();
            $table->json('equipamentos')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('ultima_manutencao')->nullable();
            $table->integer('quilometragem')->nullable();
            $table->text('descricao')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
