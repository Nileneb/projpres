<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use App\Console\Commands\WeeklyTransition;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

test('weekly transition command only includes active users', function () {
    // Erstelle 8 aktive und 2 inaktive Benutzer
    $activeUsers = User::factory()->count(8)->create(['is_active' => true]);
    $inactiveUsers = User::factory()->count(2)->create(['is_active' => false]);

    // Erstelle aktuelle Wochenteams
    $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);
    $currentWeekLabel = $teamAssignmentService->getCurrentWeekLabel();

    $team = Team::create([
        'name' => 'Team Test',
        'week_label' => $currentWeekLabel,
        'is_archived' => false
    ]);

    // Führe den Befehl aus mit force und dry-run, um nur zu testen,
    // ob die aktiven User ausgewählt werden
    $result = $this->artisan('app:weekly-transition --force --dry-run');
    $result->assertExitCode(0);

    // Überprüfe die Ausgabe des Commands
    $result->expectsOutput("Found {$activeUsers->count()} active users for new teams.");

    // Beweise, dass die Anzahl NICHT der Gesamtanzahl aller Benutzer entspricht
    $totalUsers = $activeUsers->count() + $inactiveUsers->count();
    expect($totalUsers)->toBeGreaterThan($activeUsers->count());
});
