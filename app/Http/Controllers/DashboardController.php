<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Matches;
use App\Services\TeamAssignmentService;
use App\Services\TimeService;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with team and challenge information.
     *
     * @param  \App\Services\TeamAssignmentService  $teamService
     * @param  \App\Services\TimeService  $timeService
     * @return \Illuminate\View\View
     */
    public function index(TeamAssignmentService $teamService, TimeService $timeService)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $weekLabel = $teamService->getCurrentWeekLabel();

        // Get the user's team for the current week
        $myTeam = $user->teams()->where('week_label', $weekLabel)->first();

        // Check if the user's team has created a challenge
        $myTeamHasCreatedChallenge = false;
        if ($myTeam) {
            $myTeamHasCreatedChallenge = Matches::where('week_label', $weekLabel)
                ->where('creator_team_id', $myTeam->id)
                ->exists();
        }

        // Get challenge received by the user's team
        $receivedChallenge = null;
        if ($myTeam) {
            $receivedChallenge = Matches::where('week_label', $weekLabel)
                ->where('solver_team_id', $myTeam->id)
                ->first();
        }

        // Get all matches for the current week
        $matches = Matches::where('week_label', $weekLabel)
            ->with(['creator', 'solver', 'votes'])
            ->get();

        return view('dashboard', compact(
            'weekLabel',
            'myTeam',
            'myTeamHasCreatedChallenge',
            'receivedChallenge',
            'matches',
            'timeService'
        ));
    }
}
