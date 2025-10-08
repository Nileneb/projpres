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
        Schema::table('matches', function (Blueprint $table) {
            // Füge das submission_notes-Feld nach submission_url hinzu
            $table->text('submission_notes')->nullable()->after('submission_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Entferne das submission_notes-Feld
            $table->dropColumn('submission_notes');
        });
    }
};
