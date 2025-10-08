<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Matches;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Map old status values to new ones
        $matches = Matches::all();
        foreach ($matches as $match) {
            switch ($match->status) {
                case 'pending':
                    $match->status = 'created';
                    break;
                case 'submitted':
                    // Keep as is
                    break;
                case 'closed':
                    // Keep as is
                    break;
                case 'completed':
                    $match->status = 'submitted';
                    break;
                default:
                    // Keep other values as they are
                    break;
            }
            $match->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map new status values back to old ones
        $matches = Matches::all();
        foreach ($matches as $match) {
            switch ($match->status) {
                case 'created':
                    $match->status = 'pending';
                    break;
                case 'submitted':
                    $match->status = 'completed';
                    break;
                default:
                    // Keep other values as they are
                    break;
            }
            $match->save();
        }
    }
};