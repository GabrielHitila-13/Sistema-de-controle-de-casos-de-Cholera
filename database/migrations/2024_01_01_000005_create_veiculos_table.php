<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa')->unique();
            $table->enum('tipo', ['ambulancia', 'outro']);
            $table->enum('status', ['disponivel', 'em_atendimento', 'manutencao'])->default('disponivel');
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('veiculos');
    }
};