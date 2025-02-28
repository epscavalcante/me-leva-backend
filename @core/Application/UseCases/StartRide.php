<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\StartRideInput;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Events\RideStartedEvent;
use Core\Domain\Exceptions\RideNotFoundException;

class StartRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly EventDispatcher $eventDispatcher
    ) {
    }

    public function execute(StartRideInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }

        $ride->register(RideStartedEvent::name(), function ($event) use ($ride) {
            $this->rideRepository->update($ride);
            $this->eventDispatcher->dispatch($event);
        });

        $ride->start();
    }
}
