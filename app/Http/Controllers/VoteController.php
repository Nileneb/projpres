<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Vote;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVoteRequest;

class VoteController extends Controller {
    public function store(StoreVoteRequest $req, Matches $match){
        $this->authorize('create', [Vote::class, $match]);
        $data = $req->validated();
        $vote = Vote::updateOrCreate(
            ['match_id'=>$match->id, 'user_id'=>auth()->id()],
            ['score'=>$data['score'], 'comment'=>$data['comment'] ?? null]
        );
        return back()->with('ok','Vote recorded');
    }
}

