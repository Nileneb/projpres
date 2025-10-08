<?php

namespace Tests\Feature;

use App\Models\Matches;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_vote_on_match_if_not_in_creator_or_solver_team()
    {
        // Erstelle Benutzer und Teams
        $user = User::factory()->create();
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);

        // Erstelle ein Match mit submitted Status
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'submitted',
            'week_label' => '2023-KW01',
        ]);

        // Teste, dass der Benutzer abstimmen kann (da er in keinem der Teams ist)
        $this->actingAs(User::find($user->id));

        $this->assertTrue($user->can('create', [Vote::class, $match]));
    }

    public function test_creator_team_member_cannot_vote()
    {
        // Erstelle Benutzer und Teams
        $user = User::factory()->create();
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);

        // Füge den Benutzer zum Creator-Team hinzu
        Participant::factory()->create([
            'user_id' => $user->id,
            'team_id' => $creatorTeam->id,
        ]);

        // Erstelle ein Match mit submitted Status
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'submitted',
            'week_label' => '2023-KW01',
        ]);

        // Teste, dass der Benutzer NICHT abstimmen kann (da er im Creator-Team ist)
        $this->actingAs(User::find($user->id));

        $this->assertFalse($user->can('create', [Vote::class, $match]));
    }

    public function test_solver_team_member_cannot_vote()
    {
        // Erstelle Benutzer und Teams
        $user = User::factory()->create();
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);

        // Füge den Benutzer zum Solver-Team hinzu
        Participant::factory()->create([
            'user_id' => $user->id,
            'team_id' => $solverTeam->id,
        ]);

        // Erstelle ein Match mit submitted Status
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'submitted',
            'week_label' => '2023-KW01',
        ]);

        // Teste, dass der Benutzer NICHT abstimmen kann (da er im Solver-Team ist)
        $this->actingAs(User::find($user->id));

        $this->assertFalse($user->can('create', [Vote::class, $match]));
    }

    public function test_user_cannot_vote_if_match_not_submitted()
    {
        // Erstelle Benutzer und Teams
        $user = User::factory()->create();
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);

        // Erstelle ein Match mit created Status (nicht submitted)
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'created',
            'week_label' => '2023-KW01',
        ]);

        // Teste, dass der Benutzer nicht abstimmen kann (da der Match-Status nicht "submitted" ist)
        $this->actingAs(User::find($user->id));

        $this->assertFalse($user->can('create', [Vote::class, $match]));
    }
}
