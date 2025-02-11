<?php

namespace App\Providers;

use App\Repositories\AccountModelRepository;
use App\Repositories\RideModelRepository;
use Core\Application\Repositories\AccountRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\GenerateReceiptInput;
use Core\Application\UseCases\GenerateReceipt;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Events\RideFinishedEvent;
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

        $this->app->singleton(
            EventDispatcher::class,
            EventDispatcher::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $generateReceipt = $this->app->make(GenerateReceipt::class);
        $eventDispatcher = $this->app->get(EventDispatcher::class);
        $eventDispatcher->register(RideFinishedEvent::name(), function (RideFinishedEvent $event) use ($generateReceipt) {
            $rideId = $event->getData()['ride_id'];
            $generateReceiptInput = new GenerateReceiptInput($rideId);
            $generateReceipt->execute($generateReceiptInput);
        });

        //Model::preventLazyLoading(! $this->app->isProduction());
    }
}
