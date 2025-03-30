<?php

namespace App\Providers;

use App\Repositories\AccountModelRepository;
use App\Repositories\PositionModelRepository;
use App\Repositories\RideModelRepository;
use App\Services\MessageBroker\MessageBroker;
use App\Services\MessageBroker\RabbitMQMessageBroker;
use App\Services\TokenGenerator\MyJwt;
use App\Services\TokenGenerator\TokenGenerator;
use App\Services\UnitOfWork\DatabaseUnitOfWork;
use App\Services\UnitOfWork\UnitOfWork;
use Core\Application\Repositories\AccountRepository;
use Core\Application\Repositories\PositionRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\GenerateReceiptInput;
use Core\Application\UseCases\GenerateReceipt;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Events\RideFinishedEvent as RideFinishedDomainEvent;
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
            PositionRepository::class,
            PositionModelRepository::class
        );

        $this->app->singleton(
            EventDispatcher::class,
            EventDispatcher::class
        );

        $this->app->singleton(
            TokenGenerator::class,
            MyJwt::class
        );

        $this->app->singleton(
            abstract: MessageBroker::class,
            concrete: RabbitMQMessageBroker::class
        );

        $this->app->bind(
            abstract: UnitOfWork::class,
            concrete: DatabaseUnitOfWork::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $generateReceipt = $this->app->make(GenerateReceipt::class);
        $eventDispatcher = $this->app->get(EventDispatcher::class);
        $eventDispatcher->register(RideFinishedDomainEvent::name(), function (RideFinishedDomainEvent $event) use ($generateReceipt) {
            $rideId = $event->getData()['ride_id'];
            $generateReceiptInput = new GenerateReceiptInput($rideId);
            $generateReceipt->execute($generateReceiptInput);
        });

        // Model::preventLazyLoading(! $this->app->isProduction());
    }
}
