<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Participant;
use App\Services\TeamAssignmentService;
use App\Http\Requests\GenerateTeamRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamAssignmentController extends Controller
{
    protected $teamAssignmentService;

    public function __construct(TeamAssignmentService $teamAssignmentService)
    {
        $this->teamAssignmentService = $teamAssignmentService;
        // To apply middleware, use in routes file instead of controller constructor:
        // Route::middleware(['auth', 'can:manage-teams'])->group(function () { ... });
    }

    /**
     * Zeigt die Formular-Ansicht zum Generieren neuer Teams
     */
    public function index()
    {
        $currentWeekLabel = $this->teamAssignmentService->getCurrentWeekLabel();
        $users = User::all();

        // Prüfen, ob es bereits Teams für diese Woche gibt
        $existingTeams = Team::where('week_label', $currentWeekLabel)->count();

        return view('teams.generate', compact('currentWeekLabel', 'users', 'existingTeams'));
    }

    /**
     * Generiert neue Teams für die angegebene Woche
     */
    public function generate(GenerateTeamRequest $request)
    {
        $validated = $request->validated();

        // Überprüfen, ob bereits Teams für diese Woche existieren
        $existingTeams = Team::where('week_label', $validated['week_label'])->exists();

        if ($existingTeams && !isset($validated['force'])) {
            return back()->with('error', 'Für diese Woche existieren bereits Teams. Markiere "Bestehende Teams überschreiben", um fortzufahren.');
        }

        DB::beginTransaction();

        try {
            // Wenn "force" aktiviert ist, lösche alle bestehenden Teams für diese Woche
            if (isset($validated['force'])) {
                // Finde alle Team-IDs für diese Woche
                $teamIds = Team::where('week_label', $validated['week_label'])->pluck('id')->toArray();

                // Lösche zugehörige Teilnahmen
                Participant::whereIn('team_id', $teamIds)->delete();

                // Lösche die Teams selbst
                Team::whereIn('id', $teamIds)->delete();
            }

            // Alle Benutzer abrufen
            $users = User::all()->shuffle();

            // Berechnen, wie viele Teams wir erstellen können
            $teamCount = ceil($users->count() / $validated['team_size']);

            // Teams erstellen
            $teams = [];
            for ($i = 0; $i < $teamCount; $i++) {
                $teams[] = Team::create([
                    'name' => 'Team ' . ($i + 1),
                    'week_label' => $validated['week_label']
                ]);
            }

            // Benutzer auf Teams aufteilen
            $teamIndex = 0;
            $skippedUsers = [];

            foreach ($users as $user) {
                $result = $this->teamAssignmentService->addUserToTeam(
                    $user->id,
                    $teams[$teamIndex % $teamCount]->id,
                    $validated['week_label']
                );

                if (!$result['success']) {
                    $skippedUsers[] = $user->name;
                }

                $teamIndex++;
            }

            // Wenn Benutzer übersprungen wurden, füge eine Warnung zur Erfolgsmeldung hinzu
            if (count($skippedUsers) > 0) {
                session()->flash('warning', 'Einige Benutzer wurden übersprungen, da sie bereits einem Team für diese Woche zugewiesen sind: ' . implode(', ', $skippedUsers));
            }

            // Gegner-Teams zuweisen
            $assignments = $this->teamAssignmentService->assignOpponents($validated['week_label']);

            // Create matches for each team pair
            if ($assignments['success']) {
                foreach ($assignments['assignments'] as $pair) {
                    \App\Models\Matches::firstOrCreate([
                        'week_label'       => $validated['week_label'],
                        'creator_team_id'  => $pair['team']->id,
                        'solver_team_id'   => $pair['opponent']->id,
                    ],[
                        'status' => 'created',
                        'time_limit_minutes' => 20,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('teams.assignments')
                ->with('success', 'Teams erfolgreich generiert! ' . count($teams) . ' Teams erstellt.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Fehler beim Generieren der Teams: ' . $e->getMessage());
        }
    }

    /**
     * Zeigt die aktuellen Team-Zuweisungen an
     */
    public function showAssignments()
    {
        $currentWeekLabel = $this->teamAssignmentService->getCurrentWeekLabel();

        $teams = Team::where('week_label', $currentWeekLabel)
            ->with(['participants.user'])
            ->get();

        $assignments = $this->teamAssignmentService->assignOpponents($currentWeekLabel);

        return view('teams.assignments', compact('teams', 'assignments', 'currentWeekLabel'));
    }
}
