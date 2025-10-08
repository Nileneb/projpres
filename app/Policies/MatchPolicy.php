<?php

namespace App\Policies;

use App\Models\Matches;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MatchPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Matches $matches): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Basic authorization for creating matches
    }

    /**
     * Determine whether the user can create a challenge for a specific week.
     */
    public function createChallenge(User $user, string $weekLabel): bool
    {
        // Check if the user belongs to a team for this week
        return $user->teams()->where('week_label', $weekLabel)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Matches $matches): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Matches $matches): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Matches $matches): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Matches $matches): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the challenge text.
     */
    public function updateChallenge(User $u, Matches $m)
    {
        return $m->creator->users->contains($u->id); // nur Creator-Team
    }

    /**
     * Determine whether the user can submit a solution.
     */
    public function submit(User $u, Matches $m)
    {
        // nur Solver-Team & Status created oder in_progress & (optional) Zeitfenster prüfen
        return $m->solver->users->contains($u->id) && 
               ($m->status === 'created' || $m->status === 'in_progress');
    }
}
