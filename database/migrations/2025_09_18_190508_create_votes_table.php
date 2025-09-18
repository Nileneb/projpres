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
        Schema::create('votes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->tinyInteger('score');        // 1..5
            $t->text('comment')->nullable();
            $t->timestamps();
            $t->unique(['match_id','user_id']);
            // $t->check('score >= 1 AND score <= 5');  // Dies funktioniert in neueren Laravel-Versionen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
