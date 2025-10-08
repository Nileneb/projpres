<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use App\Models\Matches as MatchModel;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Alle existierenden Daten löschen (optional)
        $this->command->info('Clearing existing data...');
        MatchModel::query()->delete();
        Participant::query()->delete();
        Team::query()->delete();
        User::query()->delete();

        // Benutzer erstellen
        $this->command->info('Creating users...');
        $admin = User::create([
            'email' => 'admin@example.com',
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // Weitere Benutzer erstellen
        $users = User::factory()->count(11)->create();
        $allUsers = collect([$admin])->merge($users);

        // Aktuelle Woche festlegen
        $weekLabel = '2025-KW38';

        // Teams erstellen
        $this->command->info('Creating teams for week ' . $weekLabel);
        $team1 = Team::create(['week_label' => $weekLabel, 'name' => 'Team Alpha']);
        $team2 = Team::create(['week_label' => $weekLabel, 'name' => 'Team Beta']);
        $team3 = Team::create(['week_label' => $weekLabel, 'name' => 'Team Gamma']);
        $teams = collect([$team1, $team2, $team3]);

        // Benutzer den Teams zuordnen
        $this->command->info('Assigning users to teams...');
        foreach ($teams as $index => $team) {
            // Jedem Team 4 Benutzer zuordnen
            $teamUsers = $allUsers->slice($index * 4, 4);
            foreach ($teamUsers as $user) {
                Participant::create([
                    'team_id' => $team->id,
                    'user_id' => $user->id,
                    'role' => $user->id === $admin->id ? 'leader' : 'member'
                ]);
            }
        }

        // Status-Optionen für Matches
        $statuses = ['created', 'in_progress', 'submitted'];

        // Beispiel-Challenges erstellen
        $this->command->info('Creating sample challenges...');

        // Team1 fordert Team2 heraus
        MatchModel::create([
            'week_label' => $weekLabel,
            'creator_team_id' => $team1->id,
            'solver_team_id' => $team2->id,
            'challenge_text' => 'Erstellt ein kurzes Video (max. 1 Minute), das eure Teamdynamik zeigt.',
            'time_limit_minutes' => 20,
            'status' => $statuses[0], // created
        ]);

        // Team2 fordert Team3 heraus (in progress)
        $match2 = MatchModel::create([
            'week_label' => $weekLabel,
            'creator_team_id' => $team2->id,
            'solver_team_id' => $team3->id,
            'challenge_text' => 'Schreibt einen Rap-Text über euer Projekt in mindestens 8 Zeilen.',
            'time_limit_minutes' => 15,
            'status' => $statuses[1], // in_progress
            'started_at' => now()->subMinutes(5),
            'deadline' => now()->addMinutes(10),
        ]);

        // Team3 fordert Team1 heraus (abgeschlossen)
        $match3 = MatchModel::create([
            'week_label' => $weekLabel,
            'creator_team_id' => $team3->id,
            'solver_team_id' => $team1->id,
            'challenge_text' => 'Erstellt ein Meme zu eurem Projektfortschritt.',
            'time_limit_minutes' => 10,
            'status' => $statuses[2], // submitted
            'started_at' => now()->subHours(1),
            'deadline' => now()->subMinutes(50),
            'submitted_at' => now()->subMinutes(52),
            'submission_url' => 'https://example.com/meme.jpg',
        ]);

        $this->command->info('Test data has been successfully seeded.');
    }
}
