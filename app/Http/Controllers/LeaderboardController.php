<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Team;
use App\Models\Matches;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Display the user points leaderboard
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get filter options
        $timeframe = $request->get('timeframe', 'all_time'); // 'all_time' or 'current_week'
        $includeArchived = $request->has('include_archived') && $request->include_archived == 1;

        // Get current week label
        $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);
        $currentWeekLabel = $teamAssignmentService->getCurrentWeekLabel();

        // Base query builder
        $query = User::select('users.id', 'users.name', DB::raw('COALESCE(SUM(votes.score), 0) as total_points'))
            ->leftJoin('participants', 'participants.user_id', '=', 'users.id')
            ->leftJoin('teams', 'teams.id', '=', 'participants.team_id')
            ->leftJoin('matches', function ($join) {
                $join->on('matches.week_label', '=', 'teams.week_label')
                    ->where(function ($query) {
                        $query->whereColumn('matches.creator_team_id', '=', 'teams.id')
                            ->orWhereColumn('matches.solver_team_id', '=', 'teams.id');
                    });
            })
            ->leftJoin('votes', 'votes.match_id', '=', 'matches.id');

        // Apply filters
        if ($timeframe === 'current_week') {
            $query->where('matches.week_label', $currentWeekLabel);
        }

        if (!$includeArchived) {
            $query->where('teams.is_archived', false);
        }

        // Complete and execute the query
        $leaderboard = $query->groupBy('users.id', 'users.name')
            ->orderByDesc('total_points')
            ->get();

        return view('leaderboard.index', compact('leaderboard', 'timeframe', 'includeArchived', 'currentWeekLabel'));
    }
}
