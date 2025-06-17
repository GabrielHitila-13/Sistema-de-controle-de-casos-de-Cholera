<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('estabelecimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gabinete_id')->constrained('gabinetes')->onDelete('cascade');
            $table->string('nome');
            $table->enum('categoria', ['geral', 'municipal', 'centro', 'posto', 'clinica', 'outros']);
            $table->text('endereco')->nullable();
            $table->string('telefone')->nullable();
            $table->integer('capacidade')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estabelecimentos');
    }
};