<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\PositionRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\FinishRideInput;
use Core\Domain\Events\EventDispatcher;
use Core\Domain\Events\RideFinishedEvent;
use Core\Domain\Exceptions\RideNotFoundException;

class FinishRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly PositionRepository $positionRepository,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function execute(FinishRideInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException;
        }

        $ride->register(RideFinishedEvent::name(), function ($event) use ($ride) {
            $this->rideRepository->update($ride);
            $this->eventDispatcher->dispatch($event);
        });

        $positions = $this->positionRepository->getPositionsByRideId($ride->getId());

        $ride->finish($positions);
    }
}
