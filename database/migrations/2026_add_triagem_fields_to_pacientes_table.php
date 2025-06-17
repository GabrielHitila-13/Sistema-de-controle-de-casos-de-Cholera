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
            $table->decimal('latitude_triagem', 10, 8)->nullable()->after('qr_code');
            $table->decimal('longitude_triagem', 11, 8)->nullable()->after('latitude_triagem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['latitude_triagem', 'longitude_triagem']);
        });
    }
};
