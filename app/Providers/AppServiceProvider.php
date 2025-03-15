<?php

namespace App\Providers;

use App\Events\Ride\RideEventFactory;
use App\Repositories\AccountModelRepository;
use App\Repositories\PositionModelRepository;
use App\Repositories\RideModelRepository;
use App\Services\MessageBroker\MessageBroker;
use App\Services\MessageBroker\RabbitMQMessageBroker;
use App\Services\TokenGenerator\MyJwt;
use App\Services\TokenGenerator\TokenGenerator;
use Core\Application\Repositories\AccountRepository;
use Core\Application\Repositories\PositionRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\GenerateReceiptInput;
use Core\Application\UseCases\GenerateReceipt;
use Core\Domain\Events\Event as DomainEvent;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Events\RideAcceptedEvent as RideAcceptedDomainEvent;
use Core\Domain\Events\RideFinishedEvent as RideFinishedDomainEvent;
use Core\Domain\Events\RidePositionUpdatedEvent as RidePositionUpdatedDomainEvent;
use Core\Domain\Events\RideRequestedEvent as RideRequestedDomainEvent;
use Core\Domain\Events\RideStartedEvent as RideStartedDomainEvent;
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

        $this->setupRideEvents($eventDispatcher);

        // Model::preventLazyLoading(! $this->app->isProduction());
    }

    private function setupRideEvents($eventDispatcher)
    {
        $eventsName = [
            RideRequestedDomainEvent::name(),
            RideAcceptedDomainEvent::name(),
            RideStartedDomainEvent::name(),
            RideFinishedDomainEvent::name(),
            RidePositionUpdatedDomainEvent::name(),
        ];

        foreach ($eventsName as $eventName) {
            $eventDispatcher->register(
                $eventName,
                fn (DomainEvent $event) => event(RideEventFactory::create($event))
            );
        }
    }
}
