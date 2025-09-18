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
        Schema::create('matches', function (Blueprint $t) {
            $t->id();
            $t->string('week_label');
            $t->foreignId('creator_team_id')->constrained('teams')->cascadeOnDelete();
            $t->foreignId('solver_team_id')->constrained('teams')->cascadeOnDelete();
            $t->text('challenge_text')->nullable();
            $t->unsignedTinyInteger('time_limit_minutes')->default(20);
            $t->string('submission_url')->nullable();
            $t->timestamp('submitted_at')->nullable();
            $t->string('status')->default('pending'); // pending|submitted|closed
            $t->timestamps();
            $t->unique(['week_label','creator_team_id','solver_team_id']);
            $t->index(['week_label','status']);

            // CHECK-Constraint direkt in der CREATE TABLE-Anweisung
            //$t->raw("CHECK (status IN ('pending','submitted','closed'))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
