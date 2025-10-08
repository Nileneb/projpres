<?php

namespace Tests\Feature;

use App\Models\Matches;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchSubmitTest extends TestCase
{
    use RefreshDatabase;

    public function test_solver_team_member_can_submit_solution_for_in_progress_match()
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

        // Erstelle ein Match im Status "in_progress" mit gesetzter deadline
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'in_progress',
            'week_label' => '2023-KW01',
            'started_at' => now()->subMinutes(5),
            'deadline' => now()->addMinutes(15),
        ]);

        // Teste, dass der Benutzer als Mitglied des Solver-Teams eine Lösung einreichen kann
        $this->actingAs(User::find($user->id));

        $response = $this->post(route('matches.submitSolution', $match), [
            'submission_url' => 'https://example.com/solution',
            'submission_notes' => 'This is my solution'
        ]);

        // Überprüfe, dass die Lösung eingereicht wurde (Redirect zur Erfolgsseite)
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Überprüfe, dass der Match-Status auf "submitted" geändert wurde
        $this->assertEquals('submitted', $match->fresh()->status);
    }

    public function test_creator_team_member_cannot_submit_solution()
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

        // Erstelle ein Match im Status "in_progress"
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'in_progress',
            'week_label' => '2023-KW01',
        ]);

        // Teste, dass der Benutzer als Mitglied des Creator-Teams KEINE Lösung einreichen kann
        $this->actingAs(User::find($user->id));

        $response = $this->post(route('matches.submitSolution', $match), [
            'submission_url' => 'https://example.com/solution',
            'submission_notes' => 'This is my solution'
        ]);

        // Überprüfe, dass der Zugriff verweigert wurde
        $response->assertStatus(403);

        // Überprüfe, dass der Match-Status immer noch "in_progress" ist
        $this->assertEquals('in_progress', $match->fresh()->status);
    }

    public function test_cannot_submit_solution_if_match_not_in_progress()
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

        // Erstelle ein Match im Status "created" (nicht in_progress)
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'created',
            'week_label' => '2023-KW01',
        ]);

        // Teste, dass keine Lösung eingereicht werden kann, wenn der Match-Status nicht "in_progress" ist
        $this->actingAs(User::find($user->id));

        $response = $this->post(route('matches.submitSolution', $match), [
            'submission_url' => 'https://example.com/solution',
            'submission_notes' => 'This is my solution'
        ]);

        // Überprüfe, dass der Zugriff verweigert wurde
        $response->assertStatus(403);

        // Überprüfe, dass der Match-Status immer noch "created" ist
        $this->assertEquals('created', $match->fresh()->status);
    }

    public function test_cannot_access_submit_form_if_match_not_in_progress()
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

        // Erstelle ein Match im Status "created" (nicht in_progress)
        $match = Matches::factory()->create([
            'creator_team_id' => $creatorTeam->id,
            'solver_team_id' => $solverTeam->id,
            'status' => 'created',
            'week_label' => '2023-KW01',
        ]);

        // Teste, dass das Formular nicht aufgerufen werden kann, wenn der Match-Status nicht "in_progress" ist
        $this->actingAs(User::find($user->id));

        $response = $this->get(route('matches.submit', $match));

        // Überprüfe, dass der Zugriff verweigert wurde
        $response->assertStatus(403);
    }
}
