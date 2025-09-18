<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\VoteController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    // Team routes
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');

    // Match routes
    Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
    Route::post('/matches/{match}/challenge', [MatchController::class, 'updateChallenge'])->name('matches.updateChallenge');
    Route::post('/matches/{match}/submit', [MatchController::class, 'submit'])->name('matches.submit');

    // Vote routes
    Route::post('/matches/{match}/votes', [VoteController::class, 'store'])->name('votes.store');
});

require __DIR__.'/auth.php';
