<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\FinishRideInput;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Exceptions\RideNotFoundException;

class FinishRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function execute(FinishRideInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }

        $ride->register('RIDE.COMPLETED', function ($event) use($ride) {
            $this->rideRepository->update($ride);
            $this->eventDispatcher->dispatch($event);
        });

        $ride->finish();
    }
}
