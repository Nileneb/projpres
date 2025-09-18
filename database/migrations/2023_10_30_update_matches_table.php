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
        Schema::table('matches', function (Blueprint $table) {
            // Füge neue Spalten hinzu
            $table->timestamp('started_at')->nullable()->after('time_limit_minutes');
            $table->timestamp('deadline')->nullable()->after('started_at');

            // Aktualisiere den Standard-Statuswert
            // Hinweis: In der Realität würden wir eine komplexere Migration schreiben,
            // die bestehende Daten umwandelt. In diesem Fall gehen wir davon aus, dass
            // die Tabelle leer ist oder die neuen Statuswerte kompatibel sind.
        });

        // Umbenennungen durchführen (dies muss separat erfolgen, um Fremdschlüsselprobleme zu vermeiden)
        Schema::table('matches', function (Blueprint $table) {
            $table->renameColumn('creator_team_id', 'creator_id');
            $table->renameColumn('solver_team_id', 'solver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Rückwärts umbenennungen durchführen
            $table->renameColumn('creator_id', 'creator_team_id');
            $table->renameColumn('solver_id', 'solver_team_id');

            // Entferne hinzugefügte Spalten
            $table->dropColumn('started_at');
            $table->dropColumn('deadline');
        });
    }
};
