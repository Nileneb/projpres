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
        // Prüfen, ob der Admin-Benutzer bereits existiert (als Indikator für vorhandene Testdaten)
        if (User::where('email', 'admin@example.com')->exists()) {
            $this->command->info('Test data already exists. Skipping seeding to prevent duplicates.');
            $this->command->info('To force re-seeding, run: php artisan db:seed --class=TestDataSeeder --force');

            // Wenn --force Flag gesetzt ist, lösche vorhandene Daten und erstelle neue
            if ($this->command->option('force')) {
                $this->command->info('Force flag detected. Clearing existing data...');
                MatchModel::query()->delete();
                Participant::query()->delete();
                Team::query()->delete();
                User::query()->delete();
            } else {
                return;
            }
        } else {
            $this->command->info('Creating new test data...');
        }

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

        // TimeService für Zeitfunktionen nutzen
        $timeService = app(\App\Services\TimeService::class);
        $weekLabel = $timeService->currentWeekLabel();

        // Alternative: Falls ein fixer Wert für Tests bevorzugt wird
        // $weekLabel = '2025-KW38';

        // Teams erstellen
        $this->command->info('Creating teams for week ' . $weekLabel);
        $teamNames = ['Team Alpha', 'Team Beta', 'Team Gamma'];
        $teams = collect();

        foreach ($teamNames as $index => $name) {
            $team = Team::firstOrCreate(
                ['week_label' => $weekLabel, 'name' => $name],
                []
            );
            $teams->push($team);
        }

        // Teams für bessere Lesbarkeit extrahieren
        $team1 = $teams[0];
        $team2 = $teams[1];
        $team3 = $teams[2];

        // Benutzer den Teams zuordnen
        $this->command->info('Assigning users to teams...');
        foreach ($teams as $index => $team) {
            // Jedem Team 4 Benutzer zuordnen
            $teamUsers = $allUsers->slice($index * 4, 4);
            foreach ($teamUsers as $user) {
                // Überprüfen, ob der Benutzer bereits diesem Team zugeordnet ist
                $exists = Participant::where('team_id', $team->id)
                                    ->where('user_id', $user->id)
                                    ->exists();

                if (!$exists) {
                    Participant::create([
                        'team_id' => $team->id,
                        'user_id' => $user->id,
                        'role' => $user->id === $admin->id ? 'leader' : 'member'
                    ]);
                }
            }
        }

        // Status-Optionen für Matches
        $statuses = ['created', 'in_progress', 'submitted'];

        // Beispiel-Challenges erstellen
        $this->command->info('Creating sample challenges...');

        // Challenges als Array definieren für einfachere Verwaltung
        $challenges = [
            [
                'week_label' => $weekLabel,
                'creator_team_id' => $team1->id,
                'solver_team_id' => $team2->id,
                'challenge_text' => 'Erstellt ein kurzes Video (max. 1 Minute), das eure Teamdynamik zeigt.',
                'time_limit_minutes' => 20,
                'status' => $statuses[0], // created
            ],
            [
                'week_label' => $weekLabel,
                'creator_team_id' => $team2->id,
                'solver_team_id' => $team3->id,
                'challenge_text' => 'Schreibt einen Rap-Text über euer Projekt in mindestens 8 Zeilen.',
                'time_limit_minutes' => 15,
                'status' => $statuses[1], // in_progress
                'started_at' => now()->subMinutes(5),
                'deadline' => now()->addMinutes(10),
            ],
            [
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
                'submission_notes' => 'Hier ist unser Meme zum Projekt. Wir haben uns für ein klassisches "Expectation vs. Reality" Format entschieden.',
            ]
        ];

        // Challenges erstellen oder bestehende aktualisieren
        $matchIds = [];
        foreach ($challenges as $index => $challenge) {
            // Suchen nach einer bestehenden Challenge mit denselben Teams
            $existingMatch = MatchModel::where('week_label', $challenge['week_label'])
                                       ->where('creator_team_id', $challenge['creator_team_id'])
                                       ->where('solver_team_id', $challenge['solver_team_id'])
                                       ->first();

            if ($existingMatch) {
                $existingMatch->update($challenge);
                $match = $existingMatch;
            } else {
                $match = MatchModel::create($challenge);
            }

            $matchIds[$index] = $match->id;
        }

        // Referenzen für spätere Nutzung
        $match2Id = $matchIds[1];
        $match3Id = $matchIds[2];

        // Archivierte Woche erstellen
        $archivedWeekLabel = '2025-KW37';
        $this->command->info('Creating archived teams for week ' . $archivedWeekLabel);

        // Archivierte Teams erstellen oder aktualisieren
        $archivedTeam1 = Team::firstOrCreate(
            ['week_label' => $archivedWeekLabel, 'name' => 'Legacy Team A'],
            ['is_archived' => true]
        );
        $archivedTeam2 = Team::firstOrCreate(
            ['week_label' => $archivedWeekLabel, 'name' => 'Legacy Team B'],
            ['is_archived' => true]
        );

        // Benutzer den archivierten Teams zuordnen (überlappend mit aktuellen Teams)
        $teamAUsers = $allUsers->slice(0, 5);
        $teamBUsers = $allUsers->slice(5, 6);

        foreach ($teamAUsers as $user) {
            // Überprüfen, ob der Benutzer bereits diesem Team zugeordnet ist
            $exists = Participant::where('team_id', $archivedTeam1->id)
                                ->where('user_id', $user->id)
                                ->exists();

            if (!$exists) {
                Participant::create([
                    'team_id' => $archivedTeam1->id,
                    'user_id' => $user->id,
                    'role' => 'member'
                ]);
            }
        }

        foreach ($teamBUsers as $user) {
            // Überprüfen, ob der Benutzer bereits diesem Team zugeordnet ist
            $exists = Participant::where('team_id', $archivedTeam2->id)
                                ->where('user_id', $user->id)
                                ->exists();

            if (!$exists) {
                Participant::create([
                    'team_id' => $archivedTeam2->id,
                    'user_id' => $user->id,
                    'role' => 'member'
                ]);
            }
        }

        // Archivierte Matches erstellen oder aktualisieren
        $existingArchivedMatch = MatchModel::where('week_label', $archivedWeekLabel)
                                        ->where('creator_team_id', $archivedTeam1->id)
                                        ->where('solver_team_id', $archivedTeam2->id)
                                        ->first();

        $archivedMatchData = [
            'week_label' => $archivedWeekLabel,
            'creator_team_id' => $archivedTeam1->id,
            'solver_team_id' => $archivedTeam2->id,
            'challenge_text' => 'Ein Archiv-Challenge mit Lösung.',
            'time_limit_minutes' => 15,
            'status' => 'submitted',
            'started_at' => now()->subWeeks(1),
            'deadline' => now()->subWeeks(1)->addMinutes(15),
            'submitted_at' => now()->subWeeks(1)->addMinutes(14),
            'submission_url' => 'https://example.com/archived-solution.jpg',
            'submission_notes' => 'Diese Archiv-Einreichung demonstriert, wie wir frühere Challenges gelöst haben.',
        ];

        if ($existingArchivedMatch) {
            $existingArchivedMatch->update($archivedMatchData);
            $archivedMatch = $existingArchivedMatch;
        } else {
            $archivedMatch = MatchModel::create($archivedMatchData);
        }

        // Add votes to test the leaderboard
        $this->command->info('Creating votes for testing the leaderboard...');

        // Determine users who are not in teams for the current match
        $team1Users = $team1->users->pluck('id')->toArray();
        $team2Users = $team2->users->pluck('id')->toArray();
        $team3Users = $team3->users->pluck('id')->toArray();

        // Find users who can vote on match3 (not in team1 or team3)
        $validVotersForMatch3 = $allUsers->filter(function($user) use ($team1Users, $team3Users) {
            return !in_array($user->id, $team1Users) && !in_array($user->id, $team3Users);
        });

        // Create votes for match3
        foreach ($validVotersForMatch3 as $user) {
            // Generate random scores between 1-5
            $score = rand(1, 5);

            // Create vote - prüfe zuerst, ob die Stimme bereits existiert
            $existingVote = \App\Models\Vote::where('match_id', $match3Id)
                                           ->where('user_id', $user->id)
                                           ->first();

            if ($existingVote) {
                $existingVote->update([
                    'score' => $score,
                    'comment' => "Rating: {$score}/5 - " . ($score >= 3 ? 'Good job!' : 'Could be improved.'),
                ]);
            } else {
                \App\Models\Vote::create([
                    'match_id' => $match3Id,
                    'user_id' => $user->id,
                    'score' => $score,
                    'comment' => "Rating: {$score}/5 - " . ($score >= 3 ? 'Good job!' : 'Could be improved.'),
                ]);
            }
        }

        // Create votes for the archived match
        $archivedTeam1Users = $archivedTeam1->users->pluck('id')->toArray();
        $archivedTeam2Users = $archivedTeam2->users->pluck('id')->toArray();

        $validVotersForArchivedMatch = $allUsers->filter(function($user) use ($archivedTeam1Users, $archivedTeam2Users) {
            return !in_array($user->id, $archivedTeam1Users) && !in_array($user->id, $archivedTeam2Users);
        });

        foreach ($validVotersForArchivedMatch as $user) {
            $score = rand(1, 5);

            // Prüfen, ob bereits eine Stimme existiert
            $existingVote = \App\Models\Vote::where('match_id', $archivedMatch->id)
                                           ->where('user_id', $user->id)
                                           ->first();

            if ($existingVote) {
                $existingVote->update([
                    'score' => $score,
                    'comment' => "Archive Rating: {$score}/5",
                ]);
            } else {
                \App\Models\Vote::create([
                    'match_id' => $archivedMatch->id,
                    'user_id' => $user->id,
                    'score' => $score,
                    'comment' => "Archive Rating: {$score}/5",
                ]);
            }
        }

        $this->command->info('Test data has been successfully seeded.');
    }
}
