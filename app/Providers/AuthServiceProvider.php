<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Matches;
use App\Policies\MatchPolicy;
use App\Models\Vote;
use App\Policies\VotePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Matches::class => MatchPolicy::class,
        Vote::class    => VotePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin-Berechtigung für Team-Verwaltung
        Gate::define('manage-teams', function (User $user) {
            // Benutzer mit is_admin = true können Teams verwalten
            return $user->is_admin;
        });
    }
}
