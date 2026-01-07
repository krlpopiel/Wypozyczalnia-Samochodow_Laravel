<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // Skąd wypożyczono (domyślnie tam gdzie stoi auto)
            $table->foreignId('origin_branch_id')->nullable()->constrained('branches');
            // Gdzie ma zostać oddane
            $table->foreignId('destination_branch_id')->nullable()->constrained('branches');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['origin_branch_id']);
            $table->dropForeign(['destination_branch_id']);
            $table->dropColumn(['origin_branch_id', 'destination_branch_id']);
        });
    }
};