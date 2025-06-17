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
        Schema::create('estabelecimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('gabinete_id')->constrained('gabinetes')->onDelete('cascade');
            $table->enum('categoria', ['geral', 'municipal', 'centro', 'posto', 'clinica', 'outros']);
            $table->text('endereco')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->integer('capacidade')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estabelecimentos');
    }
};
