<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\StartRideInput;
use Core\Domain\Exceptions\RideNotFoundException;

class StartRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
    ) {
    }

    public function execute(StartRideInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }
        $ride->start();

        $this->rideRepository->update($ride);
    }
}
