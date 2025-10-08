<?php

namespace App\Providers;

use App\Services\TimeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TimeService as a singleton
        $this->app->singleton(TimeService::class, function ($app) {
            return new TimeService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
