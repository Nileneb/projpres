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
        // Create user and teams
        $user = User::factory()->create();
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);

        // Create a match with submitted status
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'submitted',
            'week_label' => '2023-KW01',
        ]);

        // Assert that the user can vote (since they are not in any team)
        $this->actingAs(User::find($user->id));

        $this->assertTrue($user->can('create', [Vote::class, $match]));
    }

    public function test_creator_team_member_cannot_vote()
    {
        // Erstelle Benutzer und Teams
        $user = User::factory()->create();
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);

        // Add the user to the creator team
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

        // Assert that the user cannot vote (since they are in the creator team)
        $this->actingAs(User::find($user->id));

        $this->assertFalse($user->can('create', [Vote::class, $match]));
    }

    public function test_solver_team_member_cannot_vote()
    {
        // Erstelle Benutzer und Teams
        $user = User::factory()->create();
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);

        // Add the user to the solver team
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

        // Assert that the user cannot vote (since they are in the solver team)
        $this->actingAs(User::find($user->id));

        $this->assertFalse($user->can('create', [Vote::class, $match]));
    }

    public function test_user_cannot_vote_if_match_not_submitted()
    {
        // Erstelle Benutzer und Teams
        $user = User::factory()->create();
        $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
        $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);

        // Create a match with created status (not submitted)
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'created',
            'week_label' => '2023-KW01',
        ]);

        // Assert that the user cannot vote (since the match status is not "submitted")
        $this->actingAs(User::find($user->id));

        $this->assertFalse($user->can('create', [Vote::class, $match]));
    }
}
