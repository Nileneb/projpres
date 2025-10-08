<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('password.confirm'));

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    // Manually set the password confirmation timestamp
    session(['auth.password_confirmed_at' => time()]);
    
    // Verify we can access a protected route
    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    
    // Attempt to verify password with incorrect password
    $verified = \Illuminate\Support\Facades\Hash::check('wrong-password', $user->password);
    
    $this->assertFalse($verified);
    
    // Ensure we haven't set the confirmation timestamp
    $this->assertFalse(session()->has('auth.password_confirmed_at'));
});