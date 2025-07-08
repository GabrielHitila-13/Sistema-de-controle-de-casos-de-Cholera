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
        Schema::table('veiculos', function (Blueprint $table) {
            // Adiciona apenas o campo que não está na migration original
            $table->unsignedBigInteger('estabelecimento_id')->nullable()->after('id');

            // Índices e FK
            $table->foreign('estabelecimento_id')
                  ->references('id')
                  ->on('estabelecimentos')
                  ->onDelete('set null');

            $table->index(['estabelecimento_id', 'status']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $table->dropForeign(['estabelecimento_id']);
            $table->dropIndex(['estabelecimento_id', 'status']);
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn('estabelecimento_id');
        });
    }
};
