<?php

namespace App\Repositories;

use App\Ride as RideModel;
use Core\Application\Repositories\RideRepository;
use Core\Domain\Entities\Ride;

class RideModelRepository implements RideRepository
{
    public function __construct(
        private readonly RideModel $rideModel
    ) {}

    /**
     * @param Ride $ride
     */
    public function save(object $ride): void
    {
        $this->rideModel->create([
            'ride_id' => $ride->getId(),
            'passenger_id' => $ride->getPassengerId(),
            'status' => $ride->getStatus(),
            'from_latitude' => $ride->getFromLatitude(),
            'from_longitude' => $ride->getFromLongitude(),
            'to_latitude' => $ride->getToLatitude(),
            'to_longitude' => $ride->getToLongitude(),
        ]);
    }

    /**
     * @return Ride | null
     */
    public function getById(string $rideId): ?object
    {
        $ride = $this->getBy('ride_id', $rideId);

        if (! $ride) {
            return null;
        }

        return new Ride(
            rideId: $ride->ride_id,
            passengerId: $ride->passenger_id,
            // driverId: $ride->driver_id,
            status: $ride->status,
            fromLatitude: $ride->from_latitude,
            fromLongitude: $ride->from_longitude,
            toLatitude: $ride->to_latitude,
            toLongitude: $ride->to_longitude,
        );
    }

    private function getBy(string $field, string|int $value): ?RideModel
    {
        return $this->rideModel->query()
            ->firstWhere($field, $value);
    }
}
