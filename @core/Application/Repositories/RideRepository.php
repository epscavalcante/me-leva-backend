<?php

namespace Core\Application\Repositories;

use Core\Domain\Entities\Ride;

interface RideRepository
{
    /**
     * @param  Ride  $entity
     */
    public function save(object $entity): void;

    /**
     * @return Ride | null
     */
    public function getById(string $rideId): ?object;
}

