<?php

namespace App\Repositories;

use App\Ride as RideModel;
use Core\Application\Repositories\RideRepository;
use Core\Domain\Entities\Ride;

class RideModelRepository implements RideRepository
{
    public function __construct(
        private readonly RideModel $rideModel
    ) {
    }

    /**
     * @param  Ride  $ride
     */
    public function save(object $ride): void
    {
        $this->rideModel->create([
            'ride_id' => $ride->getId(),
            'passenger_id' => $ride->getPassengerId(),
            'driver_id' => $ride->getDriverId(),
            'status' => $ride->getStatus(),
            'distance' => $ride->getDistance(),
            'fare' => $ride->getFare(),
            'from_latitude' => $ride->getFromLatitude(),
            'from_longitude' => $ride->getFromLongitude(),
            'to_latitude' => $ride->getToLatitude(),
            'to_longitude' => $ride->getToLongitude(),
        ]);
    }

    /**
     * @param  Ride  $ride
     */
    public function update(object $ride): void
    {
        $rideModel = $this->getBy('ride_id', $ride->getId());
        if (! $rideModel) {
            return;
        }

        $rideModel->update([
            'ride_id' => $ride->getId(),
            'passenger_id' => $ride->getPassengerId(),
            'driver_id' => $ride->getDriverId(),
            'status' => $ride->getStatus(),
            'distance' => $ride->getDistance(),
            'fare' => $ride->getFare(),
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
        $rideModel = $this->getBy('ride_id', $rideId);

        if (! $rideModel) {
            return null;
        }

        return new Ride(
            rideId: $rideModel->ride_id,
            passengerId: $rideModel->passenger_id,
            driverId: $rideModel->driver_id,
            status: $rideModel->status,
            fare: (int) $rideModel->fare,
            distance: (int) $rideModel->distance,
            fromLatitude: $rideModel->from_latitude,
            fromLongitude: $rideModel->from_longitude,
            toLatitude: $rideModel->to_latitude,
            toLongitude: $rideModel->to_longitude,
        );
    }

    private function getBy(string $field, string|int $value): ?RideModel
    {
        return $this->rideModel->query()
            ->firstWhere($field, $value);
    }
}
