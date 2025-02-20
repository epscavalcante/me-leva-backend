<?php

namespace App\Repositories;

use App\Ride as RideModel;
use Core\Application\Repositories\RideRepository;
use Core\Domain\Entities\Ride;
use Illuminate\Database\Eloquent\Builder;

class RideModelRepository implements RideRepository
{
    public function __construct(
        private readonly RideModel $rideModel
    ) {}

    public function getRides(?int $page = 1, ?int $perPage = 10, ?string $sortBy = null, ?string $sortDir = null, ?string $status = null): RideSearchResult
    {
        $query = $this->rideModel->query();

        $query->when(
            value: $status,
            callback: function (Builder $query, string $status) {
                $query->where('status', $status);
            }
        );

        $query->orderBy(
            $sortBy ?? 'created_at',
            $sortDir ?? 'DESC'
        );

        $result = $query->paginate(
            perPage: $perPage,
            page: $page
        );
        $items = array_map(
            callback: function (RideModel $rideModel) {
                return new Ride(
                    rideId: $rideModel->ride_id,
                    passengerId: $rideModel->passenger_id,
                    driverId: $rideModel->driver_id,
                    status: $rideModel->status,
                    fare: $rideModel->fare,
                    distance: $rideModel->distance,
                    fromLatitude: $rideModel->from_latitude,
                    fromLongitude: $rideModel->from_longitude,
                    toLatitude: $rideModel->to_latitude,
                    toLongitude: $rideModel->to_longitude,
                );
            },
            array: $result->items()
        );
        $total = $result->total();

        return new RideSearchResult($items, $total);
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
            fare: $rideModel->fare,
            distance: $rideModel->distance,
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
