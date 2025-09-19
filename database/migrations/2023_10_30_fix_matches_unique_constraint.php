<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to fix unique constraints in the matches table.
     */
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Entferne den alten Unique Constraint
            $table->dropIndex('matches_week_label_creator_team_id_solver_team_id_unique');

            // Füge einen neuen Unique Constraint hinzu, der eindeutige Challenges sicherstellt,
            // aber auch mehrere Challenges zwischen denselben Teams ermöglicht
            $table->unique(['week_label', 'creator_id', 'solver_id', 'challenge_text'], 'matches_unique_challenge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Entferne den neuen Constraint
            $table->dropUnique('matches_unique_challenge');

            // Stelle den alten Constraint wieder her
            $table->unique(['week_label', 'creator_id', 'solver_id'], 'matches_week_label_creator_team_id_solver_team_id_unique');
        });
    }
};
