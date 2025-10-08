<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Vote;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVoteRequest;

class VoteController extends Controller {
    public function store(Request $request, Matches $match) {
        \Illuminate\Support\Facades\Gate::authorize('create', [Vote::class, $match]); // ← NEU
        $data = $request->validate(['rating'=>'required|integer|min:1|max:5']);
        Vote::updateOrCreate(
          ['match_id'=>$match->id,'user_id'=>$request->user()->id],
          ['score'=>$data['rating']]
        );

        return back()->with('success', 'Vote submitted successfully!');
    }
}

