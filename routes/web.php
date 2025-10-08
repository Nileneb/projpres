<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\LeaderboardController;

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

    // Team assignment routes
    Route::get('/teams/generate', [App\Http\Controllers\TeamAssignmentController::class, 'index'])
        ->middleware(['can:manage-teams'])
        ->name('teams.generate');
    Route::post('/teams/generate', [App\Http\Controllers\TeamAssignmentController::class, 'generate'])
        ->middleware(['can:manage-teams'])
        ->name('teams.generate.store');
    Route::get('/teams/assignments', [App\Http\Controllers\TeamAssignmentController::class, 'showAssignments'])
        ->name('teams.assignments');

    // Match routes
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/create', [MatchController::class, 'create'])->name('matches.create');
    Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
    Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
    Route::post('/matches/{match}/challenge', [MatchController::class, 'updateChallenge'])->name('matches.updateChallenge');
    Route::post('/matches/{match}/start', [MatchController::class, 'start'])->name('matches.start');
    Route::get('/matches/{match}/submit', [MatchController::class, 'submitForm'])->name('matches.submit');
    Route::post('/matches/{match}/submit-solution', [MatchController::class, 'submitSolution'])->name('matches.submitSolution');

    // Vote routes
    Route::post('/matches/{match}/votes', [VoteController::class, 'store'])->name('votes.store');

    // Leaderboard route
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
});

require __DIR__.'/auth.php';
