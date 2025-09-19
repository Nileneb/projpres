<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateChallengeRequest;
use App\Http\Requests\SubmitMatchRequest;
use App\Http\Requests\CreateChallengeRequest;

class MatchController extends Controller {
    public function index() {
        $matches = Matches::with(['creator', 'solver', 'votes'])->get();
        return view('matches.index', compact('matches'));
    }

    public function show(Matches $match){
        return view('matches.show', compact('match'));
    }

    public function create(Request $request) {
        $validated = $request->validate([
            'solver_team_id' => 'required|exists:teams,id',
            'week_label' => 'required|string'
        ]);

        // Get the solver team
        $solverTeam = Team::findOrFail($request->solver_team_id);
        $weekLabel = $request->week_label;
        $timeLimit = 20; // Default time limit

        return view('matches.create', compact('solverTeam', 'weekLabel', 'timeLimit'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'solver_team_id' => 'required|exists:teams,id',
            'challenge_text' => 'required|string|min:10',
            'time_limit_minutes' => 'required|integer|min:1|max:60',
            'week_label' => 'required|string'
        ]);
        
        // Finde das Team des aktuellen Benutzers für die angegebene Woche
        $creatorTeam = auth()->user()->teams()
            ->where('week_label', $validated['week_label'])
            ->firstOrFail();
            
        // Team-Zuweisung Service laden
        $teamAssignmentService = app(App\Services\TeamAssignmentService::class);
        
        // Prüfen, ob das Team bereits eine Challenge erstellt hat
        if ($teamAssignmentService->hasTeamCreatedChallengeForWeek($creatorTeam->id, $validated['week_label'])) {
            return back()->with('error', 'Dein Team hat bereits eine Challenge für diese Woche erstellt.');
        }
        
        // Prüfen, ob das Zielteam bereits eine Challenge erhalten hat
        if ($teamAssignmentService->hasTeamReceivedChallengeForWeek($validated['solver_team_id'], $validated['week_label'])) {
            return back()->with('error', 'Dieses Team hat bereits eine Challenge für diese Woche erhalten.');
        }
        
        // Create the challenge
        $match = Matches::create([
            'week_label' => $validated['week_label'],
            'creator_id' => $creatorTeam->id,
            'solver_id' => $validated['solver_team_id'],
            'challenge_text' => $validated['challenge_text'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'status' => 'created'
        ]);        return redirect()->route('matches.index')
            ->with('success', 'Challenge created successfully!');
    }

    public function updateChallenge(UpdateChallengeRequest $req, Matches $match){
        $this->authorize('updateChallenge', $match);
        $match->update(['challenge_text'=>$req->validated()['challenge_text']]);
        return back()->with('success','Challenge updated');
    }

    public function start(Matches $match) {
        // Ensure the current user is from the solver team
        $isInSolverTeam = auth()->user()->teams()
            ->where('teams.id', $match->solver_id)
            ->where('week_label', $match->week_label)
            ->exists();

        if (!$isInSolverTeam) {
            abort(403, 'You are not authorized to start this challenge');
        }

        // Update the match status
        $match->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'deadline' => now()->addMinutes($match->time_limit_minutes)
        ]);

        return redirect()->route('matches.index')
            ->with('success', 'Challenge started! You have ' . $match->time_limit_minutes . ' minutes to complete it.');
    }

    public function submitForm(Matches $match) {
        // Ensure the current user is from the solver team
        $isInSolverTeam = auth()->user()->teams()
            ->where('teams.id', $match->solver_id)
            ->where('week_label', $match->week_label)
            ->exists();

        if (!$isInSolverTeam) {
            abort(403, 'You are not authorized to submit a solution for this challenge');
        }

        return view('matches.submit', compact('match'));
    }

    public function submitSolution(Request $request, Matches $match) {
        // Ensure the current user is from the solver team
        $isInSolverTeam = auth()->user()->teams()
            ->where('teams.id', $match->solver_id)
            ->where('week_label', $match->week_label)
            ->exists();

        if (!$isInSolverTeam) {
            abort(403, 'You are not authorized to submit a solution for this challenge');
        }
        
        // Team-Zuweisung Service laden
        $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);
        
        // Prüfen, ob das Team bereits eine Challenge gelöst hat
        if ($teamAssignmentService->hasTeamSolvedChallengeForWeek($match->solver_id, $match->week_label)) {
            return back()->with('error', 'Dein Team hat bereits eine Challenge für diese Woche gelöst.');
        }

        $validated = $request->validate([
            'submission_url' => 'required|url'
        ]);

        $match->update([
            'submission_url' => $validated['submission_url'],
            'submitted_at' => now(),
            'status' => 'completed'
        ]);

        return redirect()->route('matches.index')
            ->with('success', 'Solution submitted successfully!');
    }
}

