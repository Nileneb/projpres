<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Matches;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Map old status values to new ones using direct DB queries (more performant)
        Schema::disableForeignKeyConstraints();

        // Update 'pending' to 'created'
        DB::table('matches')
            ->where('status', 'pending')
            ->update(['status' => 'created']);

        // Update 'completed' to 'submitted'
        DB::table('matches')
            ->where('status', 'completed')
            ->update(['status' => 'submitted']);

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map new status values back to old ones using direct DB queries
        Schema::disableForeignKeyConstraints();

        // Revert 'created' back to 'pending'
        DB::table('matches')
            ->where('status', 'created')
            ->update(['status' => 'pending']);

        // Revert 'submitted' back to 'completed' (only those that were previously completed)
        // Note: This is an estimation since we can't know for sure which 'submitted' were originally 'completed'
        DB::table('matches')
            ->where('status', 'submitted')
            ->update(['status' => 'completed']);

        Schema::enableForeignKeyConstraints();
    }
};
