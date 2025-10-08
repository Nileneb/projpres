<?php

use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Matches;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Helper function for Pest tests
function actingAs($user) {
    return test()->actingAs($user);
}

test('users can vote on submitted matches with score', function () {
    // Create users and teams
    $creatorUser = User::factory()->create();
    $solverUser = User::factory()->create();
    $voterUser = User::factory()->create();
    
    $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
    $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);
    
    // Assign users to teams
    Participant::factory()->create([
        'user_id' => $creatorUser->id,
        'team_id' => $creatorTeam->id,
    ]);
    
    Participant::factory()->create([
        'user_id' => $solverUser->id,
        'team_id' => $solverTeam->id,
    ]);
    
    // Create a match
    $match = Matches::factory()->create([
        'creator_team_id' => $creatorTeam->id,
        'solver_team_id' => $solverTeam->id,
        'status' => 'submitted',
        'week_label' => '2023-KW01',
    ]);
    
    // Submit a vote as voter
    actingAs($voterUser)
        ->post(route('votes.store', $match), [
            'score' => 4,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    // Check if vote was saved correctly
    $this->assertDatabaseHas('votes', [
        'match_id' => $match->id,
        'user_id' => $voterUser->id,
        'score' => 4,
        'comment' => null,
    ]);
});

test('users can vote on submitted matches with score and comment', function () {
    // Create users and teams
    $creatorUser = User::factory()->create();
    $solverUser = User::factory()->create();
    $voterUser = User::factory()->create();
    
    $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
    $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);
    
    // Assign users to teams
    Participant::factory()->create([
        'user_id' => $creatorUser->id,
        'team_id' => $creatorTeam->id,
    ]);
    
    Participant::factory()->create([
        'user_id' => $solverUser->id,
        'team_id' => $solverTeam->id,
    ]);
    
    // Create a match
    $match = Matches::factory()->create([
        'creator_team_id' => $creatorTeam->id,
        'solver_team_id' => $solverTeam->id,
        'status' => 'submitted',
        'week_label' => '2023-KW01',
    ]);
    
    $comment = "This was a great challenge!";
    
    // Submit a vote as voter with comment
    actingAs($voterUser)
        ->post(route('votes.store', $match), [
            'score' => 5,
            'comment' => $comment,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    // Check if vote with comment was saved correctly
    $this->assertDatabaseHas('votes', [
        'match_id' => $match->id,
        'user_id' => $voterUser->id,
        'score' => 5,
        'comment' => $comment,
    ]);
});

test('users cannot vote on matches they created or solved', function () {
    // Create users and teams
    $creatorUser = User::factory()->create();
    $solverUser = User::factory()->create();
    
    $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
    $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);
    
    // Assign users to teams
    Participant::factory()->create([
        'user_id' => $creatorUser->id,
        'team_id' => $creatorTeam->id,
    ]);
    
    Participant::factory()->create([
        'user_id' => $solverUser->id,
        'team_id' => $solverTeam->id,
    ]);
    
    // Create a match
    $match = Matches::factory()->create([
        'creator_team_id' => $creatorTeam->id,
        'solver_team_id' => $solverTeam->id,
        'status' => 'submitted',
        'week_label' => '2023-KW01',
    ]);
    
    // Creator user tries to vote
    actingAs($creatorUser)
        ->post(route('votes.store', $match), [
            'score' => 5,
        ])
        ->assertForbidden();
    
    // Solver user tries to vote
    actingAs($solverUser)
        ->post(route('votes.store', $match), [
            'score' => 5,
        ])
        ->assertForbidden();
});

test('users can update their existing vote', function () {
    // Create users and teams
    $creatorUser = User::factory()->create();
    $solverUser = User::factory()->create();
    $voterUser = User::factory()->create();
    
    $creatorTeam = Team::factory()->create(['week_label' => '2023-KW01']);
    $solverTeam = Team::factory()->create(['week_label' => '2023-KW01']);
    
    // Assign users to teams
    Participant::factory()->create([
        'user_id' => $creatorUser->id,
        'team_id' => $creatorTeam->id,
    ]);
    
    Participant::factory()->create([
        'user_id' => $solverUser->id,
        'team_id' => $solverTeam->id,
    ]);
    
    // Create a match
    $match = Matches::factory()->create([
        'creator_team_id' => $creatorTeam->id,
        'solver_team_id' => $solverTeam->id,
        'status' => 'submitted',
        'week_label' => '2023-KW01',
    ]);
    
    // Create initial vote
    Vote::factory()->create([
        'match_id' => $match->id,
        'user_id' => $voterUser->id,
        'score' => 3,
        'comment' => "Initial comment"
    ]);
    
    // Update the vote
    actingAs($voterUser)
        ->post(route('votes.store', $match), [
            'score' => 4,
            'comment' => "Updated comment",
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    // Check if vote was updated correctly
    $this->assertDatabaseHas('votes', [
        'match_id' => $match->id,
        'user_id' => $voterUser->id,
        'score' => 4,
        'comment' => "Updated comment",
    ]);
    
    // Check that there's only one vote from this user for this match
    $this->assertEquals(1, Vote::where('match_id', $match->id)->where('user_id', $voterUser->id)->count());
});