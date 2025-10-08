<?php

namespace Tests\Feature;

use App\Models\Matches;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_creator_and_solver_team_members_get_equal_points()
    {
        // Erstelle Benutzer
        $creatorUser = User::factory()->create(['name' => 'Creator User']);
        $solverUser = User::factory()->create(['name' => 'Solver User']);
        $voterUser = User::factory()->create(['name' => 'Voter User']);
        
        // Erstelle Teams
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01', 'name' => 'Creator Team']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01', 'name' => 'Solver Team']);
        
        // Füge Benutzer zu Teams hinzu
        Participant::factory()->create([
            'user_id' => $creatorUser->id,
            'team_id' => $creatorTeam->id,
        ]);
        
        Participant::factory()->create([
            'user_id' => $solverUser->id,
            'team_id' => $solverTeam->id,
        ]);
        
        // Erstelle ein Match im Status "submitted"
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'submitted',
            'week_label' => '2023-KW01',
        ]);
        
        // Erstelle Stimmen für das Match
        Vote::factory()->create([
            'match_id' => $match->id,
            'user_id' => $voterUser->id,
            'score' => 4,
        ]);
        
        // Berechne Punkte nach der SQL-Abfrage aus der Copilot-Instruction
        $points = DB::select("
            SELECT u.id, u.name,
                COALESCE(SUM(v.score),0) AS total_points
            FROM users u
            LEFT JOIN participants p ON p.user_id = u.id
            LEFT JOIN teams t ON t.id = p.team_id
            LEFT JOIN matches m ON m.week_label = t.week_label
                AND (m.creator_team_id = t.id OR m.solver_team_id = t.id)
            LEFT JOIN votes v ON v.match_id = m.id
            WHERE u.id IN (?, ?)
            GROUP BY u.id, u.name
            ORDER BY total_points DESC
        ", [$creatorUser->id, $solverUser->id]);
        
        // Überprüfe, dass beide Benutzer die gleiche Punktzahl haben
        $this->assertEquals(2, count($points));
        $this->assertEquals($points[0]->total_points, $points[1]->total_points);
        $this->assertEquals(4, $points[0]->total_points);
    }
}