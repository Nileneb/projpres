<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Models\Participant;
use App\Models\Matches;
use Illuminate\Support\Facades\DB;

class TeamAssignmentService
{
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
        return Matches::where('creator_id', $teamId)
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
        return Matches::where('solver_id', $teamId)
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
        return Matches::where('solver_id', $teamId)
            ->where('week_label', $weekLabel)
            ->where('status', 'completed')
            ->exists();
    }
    
    /**
     * Generiert einen Label für die aktuelle Woche im Format "YYYY-KWnn"
     * 
     * @return string
     */
    public function getCurrentWeekLabel()
    {
        $year = date('Y');
        $weekNumber = date('W');
        return "{$year}-KW{$weekNumber}";
    }
}