<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('bi')->unique();
            $table->text('telefone_encrypted')->nullable();
            $table->date('data_nascimento');
            $table->enum('sexo', ['masculino', 'feminino']);
            $table->text('sintomas')->nullable();
            $table->enum('risco', ['baixo', 'medio', 'alto'])->default('baixo');
            $table->string('qr_code')->nullable();
            $table->foreignId('estabelecimento_id')->nullable()->constrained('estabelecimentos');
            $table->timestamp('data_triagem')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pacientes');
    }
};