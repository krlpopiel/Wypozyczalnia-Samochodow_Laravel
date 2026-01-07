<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Dodajemy rental_id, który może być unikalny (jedna opinia na wynajem)
            $table->foreignId('rental_id')
                  ->nullable() // Dla kompatybilności wstecznej (jeśli masz stare dane)
                  ->after('car_id')
                  ->constrained()
                  ->cascadeOnDelete();
            
            // Opcjonalnie: Unikalność (jeden wynajem = jedna opinia)
            // $table->unique('rental_id'); 
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['rental_id']);
            $table->dropColumn('rental_id');
        });
    }
};