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
        
        // Archivierte Woche erstellen
        $archivedWeekLabel = '2025-KW37';
        $this->command->info('Creating archived teams for week ' . $archivedWeekLabel);
        
        // Archivierte Teams erstellen
        $archivedTeam1 = Team::create(['week_label' => $archivedWeekLabel, 'name' => 'Legacy Team A', 'is_archived' => true]);
        $archivedTeam2 = Team::create(['week_label' => $archivedWeekLabel, 'name' => 'Legacy Team B', 'is_archived' => true]);
        
        // Benutzer den archivierten Teams zuordnen (überlappend mit aktuellen Teams)
        $teamAUsers = $allUsers->slice(0, 5);
        $teamBUsers = $allUsers->slice(5, 6);
        
        foreach ($teamAUsers as $user) {
            Participant::create([
                'team_id' => $archivedTeam1->id,
                'user_id' => $user->id,
                'role' => 'member'
            ]);
        }
        
        foreach ($teamBUsers as $user) {
            Participant::create([
                'team_id' => $archivedTeam2->id,
                'user_id' => $user->id,
                'role' => 'member'
            ]);
        }
        
        // Archivierte Matches erstellen
        $archivedMatch = MatchModel::create([
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
        ]);
        
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
            
            // Create vote
            \App\Models\Vote::create([
                'match_id' => $match3->id,
                'user_id' => $user->id,
                'score' => $score,
                'comment' => "Rating: {$score}/5 - " . ($score >= 3 ? 'Good job!' : 'Could be improved.'),
            ]);
        }
        
        // Create votes for the archived match
        $archivedTeam1Users = $archivedTeam1->users->pluck('id')->toArray();
        $archivedTeam2Users = $archivedTeam2->users->pluck('id')->toArray();
        
        $validVotersForArchivedMatch = $allUsers->filter(function($user) use ($archivedTeam1Users, $archivedTeam2Users) {
            return !in_array($user->id, $archivedTeam1Users) && !in_array($user->id, $archivedTeam2Users);
        });
        
        foreach ($validVotersForArchivedMatch as $user) {
            $score = rand(1, 5);
            
            \App\Models\Vote::create([
                'match_id' => $archivedMatch->id,
                'user_id' => $user->id,
                'score' => $score,
                'comment' => "Archive Rating: {$score}/5",
            ]);
        }

        $this->command->info('Test data has been successfully seeded.');
    }
}
