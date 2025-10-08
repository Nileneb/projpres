<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Vote;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVoteRequest;

class VoteController extends Controller {
    public function store(StoreVoteRequest $request, Matches $match) {
        \Illuminate\Support\Facades\Gate::authorize('create', [Vote::class, $match]); // ← NEU
        $validated = $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $voteData = ['score' => $validated['score']];
        if (isset($validated['comment'])) {
            $voteData['comment'] = $validated['comment'];
        }
        
        Vote::updateOrCreate(
          ['match_id' => $match->id, 'user_id' => $user->id],
          $voteData
        );

        return back()->with('success', 'Vote submitted successfully!');
    }
}

