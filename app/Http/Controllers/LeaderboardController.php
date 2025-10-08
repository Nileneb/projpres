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
    public function index()
    {
        // Using the SQL query from copilot-instructions.md but in Laravel's query builder
        $leaderboard = User::select('users.id', 'users.name', DB::raw('COALESCE(SUM(votes.score), 0) as total_points'))
            ->leftJoin('participants', 'participants.user_id', '=', 'users.id')
            ->leftJoin('teams', 'teams.id', '=', 'participants.team_id')
            ->leftJoin('matches', function ($join) {
                $join->on('matches.week_label', '=', 'teams.week_label')
                    ->where(function ($query) {
                        $query->whereColumn('matches.creator_team_id', '=', 'teams.id')
                            ->orWhereColumn('matches.solver_team_id', '=', 'teams.id');
                    });
            })
            ->leftJoin('votes', 'votes.match_id', '=', 'matches.id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_points')
            ->get();

        return view('leaderboard.index', compact('leaderboard'));
    }
}
