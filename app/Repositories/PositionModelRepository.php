<?php

namespace App\Repositories;

use App\Position as PositionModel;
use Core\Application\Repositories\PositionRepository;
use Core\Domain\Entities\Position;

class PositionModelRepository implements PositionRepository
{
    public function __construct(
        private readonly PositionModel $positionModel
    ) {}

    /**
     * @param  Position  $position
     */
    public function save(object $position): void
    {
        $this->positionModel->create([
            'position_id' => $position->getId(),
            'ride_id' => $position->getRideId(),
            'latitude' => $position->getLatitude(),
            'longitude' => $position->getLongitude(),
        ]);
    }

    /**
     * @return Position[]
     */
    public function getPositionsByRideId(string $rideId): array
    {
        $positionsModel = $this->positionModel->query()->where('ride_id', $rideId)->get();

        return array_map(
            fn (PositionModel $positionModel) => new Position($positionModel->position_id, $positionModel->ride_id, $positionModel->latitude, $positionModel->longitude),
            $positionsModel->all()
        );
    }
}
