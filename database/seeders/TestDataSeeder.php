<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Erstelle Benutzer
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        $user1 = User::firstOrCreate([
            'email' => 'user1@example.com',
        ], [
            'name' => 'User 1',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::firstOrCreate([
            'email' => 'user2@example.com',
        ], [
            'name' => 'User 2',
            'password' => bcrypt('password'),
        ]);

        // Erstelle Teams für aktuelle Woche
        $weekLabel = '2025-KW38';

        $team1 = Team::firstOrCreate([
            'week_label' => $weekLabel,
            'name' => 'Team 1',
        ]);

        $team2 = Team::firstOrCreate([
            'week_label' => $weekLabel,
            'name' => 'Team 2',
        ]);

        // Teilnehmer zuweisen
        Participant::firstOrCreate([
            'user_id' => $admin->id,
            'team_id' => $team1->id,
        ], [
            'role' => 'leader',
        ]);

        Participant::firstOrCreate([
            'user_id' => $user1->id,
            'team_id' => $team2->id,
        ], [
            'role' => 'member',
        ]);

        Participant::firstOrCreate([
            'user_id' => $user2->id,
            'team_id' => $team2->id,
        ], [
            'role' => 'leader',
        ]);

        $this->command->info('Test data has been seeded.');
    }
}
