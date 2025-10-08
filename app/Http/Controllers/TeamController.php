<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\ArchiveTeamRequest;

class TeamController extends Controller {
    public function index(Request $request){
        // Prüfen, ob archivierte Teams angezeigt werden sollen
        $showArchived = $request->has('show_archived') && $request->show_archived == 1;

        // Teams basierend auf Archivierungsstatus holen
        $teamsQuery = $showArchived ? Team::archived() : Team::active();
        $teams = $teamsQuery->get();

        $weekLabels = $teams->pluck('week_label')->unique()->sort()->values()->all();

        // Teams nach Woche gruppieren
        $teamsByWeek = [];
        foreach ($weekLabels as $weekLabel) {
            $teamsByWeek[$weekLabel] = $teams->where('week_label', $weekLabel)->all();
        }

        return view('teams.index', compact('weekLabels', 'teamsByWeek', 'showArchived'));
    }

    /**
     * Archive a team or all teams from a specific week.
     */
    public function archive(ArchiveTeamRequest $request)
    {
        // Validierung über FormRequest
        $validated = $request->validated();

        if (isset($validated['team_id'])) {
            // Einzelnes Team archivieren
            $team = Team::findOrFail($validated['team_id']);
            $team->archive();

            return back()->with('success', "Team '{$team->name}' wurde archiviert.");
        }

        if (isset($validated['week_label'])) {
            // Alle Teams einer Woche archivieren
            $teams = Team::where('week_label', $validated['week_label'])->get();
            foreach ($teams as $team) {
                $team->archive();
            }

            return back()->with('success', "Alle Teams der Woche {$validated['week_label']} wurden archiviert.");
        }

        return back()->with('error', 'Weder Team-ID noch Wochen-Label angegeben.');
    }
}
