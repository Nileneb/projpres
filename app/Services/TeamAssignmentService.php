<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Models\Participant;
use App\Models\Matches;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TimeService;

class TeamAssignmentService
{
    /**
     * @var TimeService
     */
    protected $timeService;

    /**
     * Create a new TeamAssignmentService instance.
     *
     * @param TimeService $timeService
     */
    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }
    /**
     * Weist jedem Team ein Gegnerteam zu
     *
     * @param string $weekLabel
     * @return array
     */
    public function assignOpponents($weekLabel)
    {
        $teams = Team::where('week_label', $weekLabel)->get();

        // Wenn weniger als 2 Teams vorhanden sind, können wir keine Zuweisungen vornehmen
        if ($teams->count() < 2) {
            return [
                'success' => false,
                'message' => 'Mindestens 2 Teams werden benötigt, um Gegner zuzuweisen.'
            ];
        }

        // Teams mischen
        $shuffledTeams = $teams->shuffle();
        $assignments = [];

        // Für jedes Team einen Gegner finden
        for ($i = 0; $i < $shuffledTeams->count(); $i++) {
            $team = $shuffledTeams[$i];

            // Das nächste Team im Array als Gegner nehmen, oder das erste Team für das letzte Team
            $opponentIndex = ($i + 1) % $shuffledTeams->count();
            $opponent = $shuffledTeams[$opponentIndex];

            $assignments[] = [
                'team' => $team,
                'opponent' => $opponent
            ];
        }

        return [
            'success' => true,
            'assignments' => $assignments
        ];
    }

    /**
     * Überprüft, ob ein Team bereits eine Challenge für eine bestimmte Woche erstellt hat
     *
     * @param int $teamId
     * @param string $weekLabel
     * @return bool
     */
    public function hasTeamCreatedChallengeForWeek($teamId, $weekLabel)
    {
        return Matches::where('creator_team_id', $teamId)
            ->where('week_label', $weekLabel)
            ->exists();
    }

    /**
     * Überprüft, ob ein Team bereits eine Challenge für eine bestimmte Woche erhalten hat
     *
     * @param int $teamId
     * @param string $weekLabel
     * @return bool
     */
    public function hasTeamReceivedChallengeForWeek($teamId, $weekLabel)
    {
        return Matches::where('solver_team_id', $teamId)
            ->where('week_label', $weekLabel)
            ->exists();
    }

    /**
     * Überprüft, ob ein Team bereits eine Challenge für eine bestimmte Woche gelöst hat
     *
     * @param int $teamId
     * @param string $weekLabel
     * @return bool
     */
    public function hasTeamSolvedChallengeForWeek($teamId, $weekLabel)
    {
        return Matches::where('solver_team_id', $teamId)
            ->where('week_label', $weekLabel)
            ->where('status', 'submitted')
            ->exists();
    }

    /**
     * Generiert einen Label für die aktuelle Woche im Format "YYYY-KWnn"
     *
     * @return string
     */
    public function getCurrentWeekLabel()
    {
        return $this->timeService->currentWeekLabel();
    }

    /**
     * Überprüft, ob ein Benutzer bereits einem Team für eine bestimmte Woche zugewiesen ist
     *
     * @param int $userId
     * @param string $weekLabel
     * @return bool
     */
    public function isUserAlreadyInTeamForWeek($userId, $weekLabel)
    {
        return Participant::whereHas('team', function($query) use ($weekLabel) {
                $query->where('week_label', $weekLabel);
            })
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Fügt einen Benutzer zu einem Team hinzu, aber nur wenn er noch nicht in einem Team für diese Woche ist
     *
     * @param int $userId
     * @param int $teamId
     * @param string $weekLabel
     * @param string|null $role
     * @return array
     */
    public function addUserToTeam($userId, $teamId, $weekLabel, $role = null)
    {
        // Prüfen, ob der Benutzer bereits in einem Team für diese Woche ist
        if ($this->isUserAlreadyInTeamForWeek($userId, $weekLabel)) {
            Log::warning("User ID {$userId} ist bereits einem Team für die Woche {$weekLabel} zugeordnet und wird übersprungen.");
            return [
                'success' => false,
                'message' => "Benutzer ist bereits einem Team für diese Woche zugeordnet."
            ];
        }

        // Benutzer zum Team hinzufügen
        try {
            Participant::create([
                'user_id' => $userId,
                'team_id' => $teamId,
                'role' => $role
            ]);

            return [
                'success' => true,
                'message' => "Benutzer erfolgreich zum Team hinzugefügt."
            ];
        } catch (\Exception $e) {
            Log::error("Fehler beim Hinzufügen des Benutzers {$userId} zum Team {$teamId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Fehler beim Hinzufügen des Benutzers zum Team: " . $e->getMessage()
            ];
        }
    }
}
