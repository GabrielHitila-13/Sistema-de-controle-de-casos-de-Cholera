<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pontos_atendimento', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pontos_atendimento');
    }
};