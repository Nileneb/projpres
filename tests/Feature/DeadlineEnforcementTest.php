<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Matches;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('solver can submit solution before deadline', function () {
    // Create teams and users
    $creatorTeam = Team::factory()->create(['week_label' => '2025-KW42']);
    $solverTeam = Team::factory()->create(['week_label' => '2025-KW42']);

    $user = User::factory()->create();

    // Add user to solver team
    Participant::factory()->create([
        'user_id' => $user->id,
        'team_id' => $solverTeam->id
    ]);

    // Create match with started status and future deadline
    $match = Matches::factory()->create([
        'creator_team_id' => $creatorTeam->id,
        'solver_team_id' => $solverTeam->id,
        'status' => 'in_progress',
        'week_label' => '2025-KW42',
        'started_at' => now()->subMinutes(10),
        'deadline' => now()->addMinutes(10), // Deadline is in the future
    ]);

    // Login as solver team member
    Auth::login($user);

    // Submit solution
    $response = $this->post(route('matches.submitSolution', $match), [
        'submission_url' => 'https://example.com/solution'
    ]);

    // Assert response is successful and redirects to matches index
    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Solution submitted successfully!');

    // Assert that match was updated
    $this->assertDatabaseHas('matches', [
        'id' => $match->id,
        'status' => 'submitted',
        'submission_url' => 'https://example.com/solution'
    ]);
});

test('solver cannot submit solution after deadline', function () {
    // Create teams and users
    $creatorTeam = Team::factory()->create(['week_label' => '2025-KW42']);
    $solverTeam = Team::factory()->create(['week_label' => '2025-KW42']);

    $user = User::factory()->create();

    // Add user to solver team
    Participant::factory()->create([
        'user_id' => $user->id,
        'team_id' => $solverTeam->id
    ]);

    // Create match with started status and past deadline
    $match = Matches::factory()->create([
        'creator_team_id' => $creatorTeam->id,
        'solver_team_id' => $solverTeam->id,
        'status' => 'in_progress',
        'week_label' => '2025-KW42',
        'started_at' => now()->subMinutes(30),
        'deadline' => now()->subMinutes(10), // Deadline is in the past
        'submission_url' => null // Explicitly set submission_url to null
    ]);

    // Login as solver team member
    Auth::login($user);

    // Try to submit solution
    $response = $this->post(route('matches.submitSolution', $match), [
        'submission_url' => 'https://example.com/solution'
    ]);

    // Assert response is redirected back with error message
    $response->assertStatus(302);
    $response->assertSessionHas('error', 'Die Bearbeitungszeit ist abgelaufen. Die Lösung kann nicht mehr eingereicht werden.');

    // Assert that match was not updated
    $this->assertDatabaseHas('matches', [
        'id' => $match->id,
        'status' => 'in_progress', // Status remains unchanged
        'submission_url' => null    // URL not updated
    ]);
});
