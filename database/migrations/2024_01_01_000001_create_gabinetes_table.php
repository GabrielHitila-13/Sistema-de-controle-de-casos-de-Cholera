<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gabinetes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('tipo', ['provincial', 'municipal']);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('endereco')->nullable();
            $table->string('telefone')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gabinetes');
    }
};