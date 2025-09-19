<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Vote;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVoteRequest;

class VoteController extends Controller {
    public function store(Request $request, Matches $match) {
        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        // Check if user is eligible to vote (not in creator or solver team)
        $user = request()->user();
        $isInInvolvedTeams = $user->teams()
            ->where('week_label', $match->week_label)
            ->whereIn('teams.id', [$match->creator_team_id, $match->solver_team_id])
            ->exists();

        if ($isInInvolvedTeams) {
            return back()->with('error', 'You cannot vote on challenges involving your team.');
        }

        // Create or update the vote
        Vote::updateOrCreate(
            ['match_id' => $match->id, 'user_id' => $user->getAuthIdentifier()],
            ['score' => $validated['rating']]
        );

        return back()->with('success', 'Vote submitted successfully!');
    }
}

