<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Team;
use App\Models\Participant;
use App\Http\Controllers\TeamAssignmentController;
use App\Http\Requests\GenerateTeamRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class, WithFaker::class);

test('team generation only includes active users', function () {
    // Create admin user
    $adminUser = User::factory()->create(['is_admin' => true]);
    
    // Create 5 active users and 3 inactive users
    $activeUsers = User::factory()->count(5)->create(['is_active' => true]);
    $inactiveUsers = User::factory()->count(3)->create(['is_active' => false]);
    
    // Get current week label
    $teamAssignmentService = app(\App\Services\TeamAssignmentService::class);
    $currentWeekLabel = $teamAssignmentService->getCurrentWeekLabel();
    
    // Login as admin
    Auth::login($adminUser);
    
    // Allow admin to manage teams
    Gate::define('manage-teams', fn($user) => $user->is_admin);
    
    // Make request to generate teams
    $response = $this->post(route('teams.generate.store'), [
        'week_label' => $currentWeekLabel,
        'team_size' => 3,
        'force' => true,
    ]);
    
    // Assert response is successful
    $response->assertSessionHas('success');
    
    // Get generated teams
    $teams = Team::where('week_label', $currentWeekLabel)->get();
    $participants = Participant::whereIn('team_id', $teams->pluck('id'))->get();
    
    // Assert that all active users are assigned to teams
    foreach ($activeUsers as $activeUser) {
        $this->assertDatabaseHas('participants', [
            'user_id' => $activeUser->id,
        ]);
    }
    
    // Assert that no inactive users are assigned to teams
    foreach ($inactiveUsers as $inactiveUser) {
        $this->assertDatabaseMissing('participants', [
            'user_id' => $inactiveUser->id,
        ]);
    }
    
    // Assert that participant count matches active user count + admin
    expect($participants->count())->toBe($activeUsers->count() + 1);
});

test('user can toggle is_active setting in profile', function() {
    // Create user
    $user = User::factory()->create(['is_active' => true]);
    
    // Login as user
    Auth::login($user);
    
    // Submit form to update is_active to false (omit is_active from request)
    $response = $this->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        // is_active is not included to simulate unchecked checkbox
    ]);
    
    // Assert successful update
    $response->assertSessionHas('status', 'profile-updated');
    
    // Refresh user from database
    $user->refresh();
    
    // Assert is_active is now false
    expect($user->is_active)->toBeFalse();
    
    // Submit form to update is_active back to true
    $response = $this->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'is_active' => 'on', // Simulate a checked checkbox
    ]);
    
    // Refresh user from database
    $user->refresh();
    
    // Assert is_active is now true
    expect($user->is_active)->toBeTrue();
});

test('is_active is false when checkbox is not checked', function() {
    // Create user
    $user = User::factory()->create(['is_active' => true]);
    
    // Login as user
    Auth::login($user);
    
    // Submit form without is_active checkbox
    $response = $this->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        // is_active is not included when checkbox is unchecked
    ]);
    
    // Assert successful update
    $response->assertSessionHas('status', 'profile-updated');
    
    // Refresh user from database
    $user->refresh();
    
    // Assert is_active is now false
    expect($user->is_active)->toBeFalse();
});