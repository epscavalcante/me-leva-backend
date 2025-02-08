<?php

namespace App\Providers;

use App\Repositories\AccountModelRepository;
use App\Repositories\RideModelRepository;
use Core\Application\Repositories\AccountRepository;
use Core\Application\Repositories\RideRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            AccountRepository::class,
            AccountModelRepository::class
        );

        $this->app->singleton(
            RideRepository::class,
            RideModelRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Model::preventLazyLoading(! $this->app->isProduction());
    }
}
