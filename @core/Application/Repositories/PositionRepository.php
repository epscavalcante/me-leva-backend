<?php

namespace Core\Application\Repositories;

use Core\Domain\Entities\Position;

interface PositionRepository
{
    /**
     * @param  Position  $entity
     */
    public function save(object $entity): void;

    /**
     * @return Position[]
     */
    public function getPositionsByRideId(string $rideId): array;
}
