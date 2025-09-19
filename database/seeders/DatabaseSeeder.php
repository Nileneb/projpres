<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Erstelle Test-User
        // Use TestDataSeeder instead of individual user creation
        $this->call(TestDataSeeder::class);
    }

    /**
     * Stelle sicher, dass der Test-User einem Team zugewiesen ist
     */
    private function assignTeamToTestUser(User $user): void
    {
        if ($user->teams()->count() === 0) {
            // Wenn der Benutzer noch keinem Team zugewiesen ist, füge ihn zum ersten verfügbaren Team hinzu
            $team = \App\Models\Team::first();

            if ($team) {
                \App\Models\Participant::create([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                    'role' => 'member'
                ]);
            }
        }
    }
}
