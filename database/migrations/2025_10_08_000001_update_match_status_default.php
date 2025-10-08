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
            // First, change the default for new records
            $table->string('status')->default('created')->change();

            // Optionally, add a check constraint if your database supports it
            // DB::statement("ALTER TABLE matches ADD CONSTRAINT check_valid_status CHECK (status IN ('created', 'in_progress', 'submitted', 'closed'))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Revert the default value
            $table->string('status')->default('pending')->change();
            
            // If you added a check constraint, remove it
            // DB::statement("ALTER TABLE matches DROP CONSTRAINT IF EXISTS check_valid_status");
        });
    }
};