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
        if (!Schema::hasTable('matches')) {
            // Skip this migration if the matches table doesn't exist yet
            return;
        }

        Schema::table('matches', function (Blueprint $table) {
            try {
                // Attempt to drop the old unique constraint
                $table->dropIndex('matches_week_label_creator_team_id_solver_team_id_unique');
            } catch (\Exception $e) {
                // Ignore if the index doesn't exist
            }

            try {
                // Füge einen neuen Unique Constraint hinzu
                $table->unique(['week_label', 'creator_team_id', 'solver_team_id', 'challenge_text'], 'matches_unique_challenge');
            } catch (\Exception $e) {
                // Ignore if the index already exists
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('matches')) {
            // Skip this migration if the matches table doesn't exist anymore
            return;
        }

        Schema::table('matches', function (Blueprint $table) {
            try {
                // Attempt to drop the new constraint
                $table->dropUnique('matches_unique_challenge');
            } catch (\Exception $e) {
                // Ignore if the index doesn't exist
            }

            try {
                // Stelle den alten Constraint wieder her
                $table->unique(['week_label', 'creator_team_id', 'solver_team_id'], 'matches_week_label_creator_team_id_solver_team_id_unique');
            } catch (\Exception $e) {
                // Ignore if the index already exists
            }
        });
    }
};
