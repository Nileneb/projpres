<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateChallengeRequest;
use App\Http\Requests\SubmitMatchRequest;
use App\Http\Requests\CreateChallengeRequest;
use App\Http\Requests\SelectTeamRequest;

class MatchController extends Controller {
    public function index() {
        $matches = Matches::with(['creator', 'solver', 'votes'])->get();
        return view('matches.index', compact('matches'));
    }

    public function show(Matches $match){
        return view('matches.show', compact('match'));
    }

    public function create(Request $request) {
        // Get current week label and possible solver teams
        $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);
        $weekLabel = $teamAssignmentService->getCurrentWeekLabel();

        // Get solver team if provided, otherwise show selection form
        if ($request->has('solver_team_id') && $request->has('week_label')) {
            $request->validate([
                'solver_team_id' => 'required|exists:teams,id',
                'week_label' => 'required|string'
            ]);
            $solverTeam = Team::findOrFail($request->solver_team_id);
            $weekLabel = $request->week_label;
        } else {
            // Get all teams for the current week
            $solverTeams = Team::where('week_label', $weekLabel)->get();
            return view('matches.select_team', compact('solverTeams', 'weekLabel'));
        }
        $timeLimit = 20; // Default time limit

        return view('matches.create', compact('solverTeam', 'weekLabel', 'timeLimit'));
    }

    public function store(CreateChallengeRequest $request) {
        $validated = $request->validated();

        // Finde das Team des aktuellen Benutzers für die angegebene Woche
        $user = request()->user();
        $creatorTeam = $user->teams()
            ->where('week_label', $validated['week_label'])
            ->firstOrFail();

        // Team-Zuweisung Service laden
        $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);

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
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $validated['solver_team_id'],
            'challenge_text' => $validated['challenge_text'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'status' => 'created'
        ]);        return redirect()->route('matches.index')
            ->with('success', 'Challenge created successfully!');
    }

    public function updateChallenge(UpdateChallengeRequest $req, Matches $match){
        if (!\Illuminate\Support\Facades\Gate::allows('updateChallenge', $match)) {
            abort(403, 'Unauthorized action.');
        }
        $match->update(['challenge_text'=>$req->validated()['challenge_text']]);
        return back()->with('success','Challenge updated');
    }

    public function start(Matches $match) {
        // Ensure the current user is from the solver team
        $user = request()->user();
        $isInSolverTeam = $user->teams()
            ->where('teams.id', $match->solver_team_id)
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
        $user = request()->user();
        $isInSolverTeam = $user->teams()
            ->where('teams.id', $match->solver_team_id)
            ->where('week_label', $match->week_label)
            ->exists();

        if (!$isInSolverTeam) {
            abort(403, 'You are not authorized to submit a solution for this challenge');
        }

        return view('matches.submit', compact('match'));
    }

    public function submitSolution(SubmitMatchRequest $request, Matches $match) {
        // Ensure the current user is from the solver team
        $user = request()->user();
        $isInSolverTeam = $user->teams()
            ->where('teams.id', $match->solver_team_id)
            ->where('week_label', $match->week_label)
            ->exists();

        if (!$isInSolverTeam) {
            abort(403, 'You are not authorized to submit a solution for this challenge');
        }

        // Team-Zuweisung Service laden
        $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);

        // Prüfen, ob das Team bereits eine Challenge gelöst hat
        if ($teamAssignmentService->hasTeamSolvedChallengeForWeek($match->solver_team_id, $match->week_label)) {
            return back()->with('error', 'Dein Team hat bereits eine Challenge für diese Woche gelöst.');
        }

        $validated = $request->validated();

        $match->update([
            'submission_url' => $validated['submission_url'],
            'submitted_at' => now(),
            'status' => 'submitted'
        ]);

        return redirect()->route('matches.index')
            ->with('success', 'Solution submitted successfully!');
    }
}

