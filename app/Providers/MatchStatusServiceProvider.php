<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class MatchStatusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registriert Hilfsfunktion für Match-Status-Farben
        Blade::directive('matchStatusColor', function ($status) {
            return "<?php echo match_status_color($status); ?>";
        });

        // Registriert die Match-Status-Badge-Komponente
        Blade::component('match-status', \App\View\Components\MatchStatus::class);
    }
}
