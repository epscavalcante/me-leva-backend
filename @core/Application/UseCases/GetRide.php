<?php

namespace Core\Application\UseCases;

use Core\Application\Repositories\PositionRepository;
use Core\Application\Repositories\RideRepository;
use Core\Application\UseCases\DTOs\GetRideInput;
use Core\Application\UseCases\DTOs\GetRideOutput;
use Core\Domain\Exceptions\RideNotFoundException;
use Core\Domain\Services\DistanceCalculator;

class GetRide
{
    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly PositionRepository $positionRepository,
    ) {
    }

    public function execute(GetRideInput $input): GetRideOutput
    {
        $ride = $this->rideRepository->getById($input->rideId);
        if (! $ride) {
            throw new RideNotFoundException();
        }

        $positions = $this->positionRepository->getPositionsByRideId($ride->getId());

        return new GetRideOutput(
            rideId: $ride->getId(),
            passengerId: $ride->getPassengerId(),
            driverId: $ride->getDriverId(),
            status: $ride->getStatus(),
            distance: DistanceCalculator::calculateByPositions($positions),
            fare: $ride->getFare(),
            fromLatitude: $ride->getFromLatitude(),
            fromLongitude: $ride->getFromLongitude(),
            toLatitude: $ride->getToLatitude(),
            toLongitude: $ride->getToLongitude(),
            positions: array_map(
                fn($position) => [
                    'latitude' => $position->getLatitude(),
                    'longitude' => $position->getLongitude(),
                ],
                $positions
            )
        );
    }
}
