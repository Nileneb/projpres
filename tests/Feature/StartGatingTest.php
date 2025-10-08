<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Matches;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('only solver team members can start a match', function () {
    // Create teams and users
    $creatorTeam = Team::factory()->create(['week_label' => '2025-KW42']);
    $solverTeam = Team::factory()->create(['week_label' => '2025-KW42']);
    
    $solverUser = User::factory()->create();
    $creatorUser = User::factory()->create();
    $otherUser = User::factory()->create();
    
    // Add users to respective teams
    Participant::factory()->create([
        'user_id' => $solverUser->id,
        'team_id' => $solverTeam->id
    ]);
    
    Participant::factory()->create([
        'user_id' => $creatorUser->id,
        'team_id' => $creatorTeam->id
    ]);
    
    // Create match with created status
    $match = Matches::factory()->create([
        'creator_team_id' => $creatorTeam->id,
        'solver_team_id' => $solverTeam->id,
        'status' => 'created',
        'week_label' => '2025-KW42',
    ]);
    
    // Test 1: Solver team member can start the match
    Auth::login($solverUser);
    
    $response = $this->post(route('matches.start', $match));
    
    $response->assertStatus(302);
    $response->assertSessionHas('success');
    
    // Match should be updated
    $this->assertDatabaseHas('matches', [
        'id' => $match->id,
        'status' => 'in_progress',
    ]);
    
    // Reset the match status for next test
    $match->update(['status' => 'created']);
    
    // Test 2: Creator team member cannot start the match
    Auth::login($creatorUser);
    
    $response = $this->post(route('matches.start', $match));
    
    $response->assertStatus(403); // Forbidden
    
    // Match status should remain unchanged
    $this->assertDatabaseHas('matches', [
        'id' => $match->id,
        'status' => 'created',
    ]);
    
    // Test 3: Other user (not in either team) cannot start the match
    Auth::login($otherUser);
    
    $response = $this->post(route('matches.start', $match));
    
    $response->assertStatus(403); // Forbidden
    
    // Match status should remain unchanged
    $this->assertDatabaseHas('matches', [
        'id' => $match->id,
        'status' => 'created',
    ]);
});

test('cannot start a match that is not in created status', function () {
    // Create teams and users
    $creatorTeam = Team::factory()->create(['week_label' => '2025-KW42']);
    $solverTeam = Team::factory()->create(['week_label' => '2025-KW42']);
    
    $solverUser = User::factory()->create();
    
    // Add user to solver team
    Participant::factory()->create([
        'user_id' => $solverUser->id,
        'team_id' => $solverTeam->id
    ]);
    
    // Create match with in_progress status (already started)
    $match = Matches::factory()->create([
        'creator_team_id' => $creatorTeam->id,
        'solver_team_id' => $solverTeam->id,
        'status' => 'in_progress',
        'week_label' => '2025-KW42',
        'started_at' => now()->subMinutes(5),
        'deadline' => now()->addMinutes(15),
    ]);
    
    // Login as solver team member
    Auth::login($solverUser);
    
    // Try to start the match again
    $response = $this->post(route('matches.start', $match));
    
    // Should fail or redirect with error
    $response->assertStatus(302);
    
    // Match status should remain in_progress
    $this->assertDatabaseHas('matches', [
        'id' => $match->id,
        'status' => 'in_progress',
    ]);
});