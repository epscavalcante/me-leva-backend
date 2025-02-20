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
     * @param  Ride  $entity
     */
    public function update(object $entity): void;

    /**
     * @return Ride | null
     */
    public function getById(string $rideId): ?object;

    public function getRides(?int $page, ?int $perPage, ?string $sortBy, ?string $sortDir = null, ?string $status = null): RideSearchResult;
}
