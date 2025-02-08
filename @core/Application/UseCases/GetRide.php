<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Domain\Exceptions\AccountNotFoundException;
use Core\Domain\Exceptions\RideNotFoundException;

class GetRide
{
    public function __construct(
        private readonly RideRepository $rideRepository
    ) {}

    public function execute(GetRideInput $input): GetRideOutput
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }

        return new GetRideOutput(
            rideId: $ride->getId(),
            passengerId: $ride->getPassengerId(),
            //driverId: $ride->getDriverId(),
            status: $ride->getStatus(),
            fromLatitude: $ride->getFromLatitude(),
            fromLongitude: $ride->getFromLongitude(),
            toLatitude: $ride->getToLatitude(),
            toLongitude: $ride->getToLongitude(),
        );
    }
}
