<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Team;
use App\Models\Matches;
use Illuminate\Support\Facades\DB;

class TestActiveUserFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:active-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the active user filtering in team assignment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing active user filtering...');

        // Count total users
        $totalUsers = User::count();
        $this->info("Total users: {$totalUsers}");

        // Count active users
        $activeUsers = User::where('is_active', true)->count();
        $this->info("Active users: {$activeUsers}");

        // Count inactive users
        $inactiveUsers = User::where('is_active', false)->count();
        $this->info("Inactive users: {$inactiveUsers}");

        // Get team assignment service
        $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);
        $currentWeekLabel = $teamAssignmentService->getCurrentWeekLabel();
        $this->info("Current week label: {$currentWeekLabel}");

        // Check if teams exist for current week
        $teamsCount = Team::where('week_label', $currentWeekLabel)->count();
        $this->info("Teams for current week: {$teamsCount}");

        if ($teamsCount > 0) {
            // Get participants count for current week
            $participantsCount = DB::table('participants')
                ->join('teams', 'participants.team_id', '=', 'teams.id')
                ->where('teams.week_label', $currentWeekLabel)
                ->count();

            $this->info("Participants in current week teams: {$participantsCount}");

            // Check if all participants are active users
            $inactiveParticipants = DB::table('participants')
                ->join('teams', 'participants.team_id', '=', 'teams.id')
                ->join('users', 'participants.user_id', '=', 'users.id')
                ->where('teams.week_label', $currentWeekLabel)
                ->where('users.is_active', false)
                ->count();

            if ($inactiveParticipants > 0) {
                $this->error("Found {$inactiveParticipants} inactive users in current week teams!");
            } else {
                $this->info("All participants in current week teams are active users.");
            }
        }

        return self::SUCCESS;
    }
}
