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
        Schema::create('participants', function (Blueprint $t) {
        $t->id();
        $t->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
        $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $t->string('role')->nullable(); // "member" | "lead" (optional)
        $t->timestamps();
        $t->unique(['team_id','user_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
