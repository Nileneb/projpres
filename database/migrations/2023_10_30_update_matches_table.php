<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to update the matches table.
     */
    public function up(): void
    {
        if (!Schema::hasTable('matches')) {
            // Skip this migration if the matches table doesn't exist yet
            return;
        }

        try {
            Schema::table('matches', function (Blueprint $table) {
                // Füge neue Spalten hinzu
                $table->timestamp('started_at')->nullable()->after('time_limit_minutes');
                $table->timestamp('deadline')->nullable()->after('started_at');
            });
        } catch (\Exception $e) {
            // Ignore errors
        }

        try {
            // No need to rename columns - they should remain as creator_team_id and solver_team_id
            // Schema::table('matches', function (Blueprint $table) {
            //     if (Schema::hasColumn('matches', 'creator_team_id')) {
            //         $table->renameColumn('creator_team_id', 'creator_id');
            //     }
            //     if (Schema::hasColumn('matches', 'solver_team_id')) {
            //         $table->renameColumn('solver_team_id', 'solver_id');
            //     }
            // });
        } catch (\Exception $e) {
            // Ignore errors
        }
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

        try {
            Schema::table('matches', function (Blueprint $table) {
                // Rückwärts umbenennungen durchführen
                // No need to rename columns anymore - they should already be creator_team_id and solver_team_id
            });
        } catch (\Exception $e) {
            // Ignore errors
        }

        try {
            Schema::table('matches', function (Blueprint $table) {
                // Entferne hinzugefügte Spalten
                if (Schema::hasColumn('matches', 'started_at')) {
                    $table->dropColumn('started_at');
                }
                if (Schema::hasColumn('matches', 'deadline')) {
                    $table->dropColumn('deadline');
                }
            });
        } catch (\Exception $e) {
            // Ignore errors
        }
    }
};
