<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchController extends Controller {
    public function show(MatchModel $match){ return view('matches.show', compact('match')); }

    public function updateChallenge(UpdateChallengeRequest $req, MatchModel $match){
        $this->authorize('updateChallenge', $match);
        $match->update(['challenge_text'=>$req->validated()['challenge_text']]);
        return back()->with('ok','Challenge updated');
    }

    public function submit(SubmitMatchRequest $req, MatchModel $match){
        $this->authorize('submit', $match);
        $match->update([
            'submission_url'=>$req->validated()['submission_url'],
            'submitted_at'=>now(),
            'status'=>'submitted',
        ]);
        return back()->with('ok','Submission saved');
    }
}

