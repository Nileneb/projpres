<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('user can toggle is_active setting', function () {
    // Create a user with is_active set to true by default
    $user = User::factory()->create(['is_active' => true]);

    // Login as user
    Auth::login($user);

    // Verify user is currently active
    $this->assertTrue($user->is_active);

    // Test 1: Turn off is_active by not including it in the request
    $response = $this->put(route('settings.profile.update'), [
        'name' => 'Updated Name',
        'email' => $user->email,
        // is_active is not included
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('status', 'profile-updated');

    // Reload user from database
    $user->refresh();

    // Verify is_active was turned off
    $this->assertFalse($user->is_active);

    // Test 2: Turn on is_active by including it in the request
    $response = $this->put(route('settings.profile.update'), [
        'name' => 'Updated Name Again',
        'email' => $user->email,
        'is_active' => 'on', // Include is_active
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('status', 'profile-updated');

    // Reload user from database
    $user->refresh();

    // Verify is_active was turned on
    $this->assertTrue($user->is_active);
});

test('admin can view all users in team generation regardless of is_active', function () {
    // Create users with different active states
    $admin = User::factory()->create(['is_admin' => true]);
    $activeUser = User::factory()->create(['is_active' => true]);
    $inactiveUser = User::factory()->create(['is_active' => false]);

    // Login as admin
    Auth::login($admin);

    // Visit the team generation page
    $response = $this->get(route('teams.generate'));

    $response->assertStatus(200);
    $response->assertViewHas('users');
    $response->assertViewHas('activeUsers');

    // Get the users and activeUsers from the view
    $users = $response->viewData('users');
    $activeUsers = $response->viewData('activeUsers');

    // Check that all users are displayed
    $this->assertTrue($users->contains($activeUser));
    $this->assertTrue($users->contains($inactiveUser));

    // Check that only active users are in activeUsers collection
    $this->assertTrue($activeUsers->contains($activeUser));
    $this->assertFalse($activeUsers->contains($inactiveUser));
});
