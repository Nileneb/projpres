<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The application's timezone.
     *
     * @var string
     */
    protected $timezone = 'Europe/Berlin';

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Wöchentliche Transition jeden Sonntag um Mitternacht ausführen
        $schedule->command('app:weekly-transition')
            ->sundays()
            ->at('00:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
