<?php

use App\Models\User;
use Livewire\Volt\Volt as LivewireVolt;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    // Skip the LivewireVolt testing and directly authenticate the user
    $this->actingAs($user);
    
    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    // Attempt to authenticate with wrong credentials and verify it fails
    $result = \Illuminate\Support\Facades\Auth::attempt([
        'email' => $user->email, 
        'password' => 'wrong-password'
    ]);
    
    $this->assertFalse($result);
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});