<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\FinishRideInput;
use Core\Domain\Exceptions\RideNotFoundException;

class FinishRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
    ) {
    }

    public function execute(FinishRideInput $input): void
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }

        $ride->finish();

        $this->rideRepository->update($ride);
    }
}
