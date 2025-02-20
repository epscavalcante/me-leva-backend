<?php

namespace Core\Application\Repositories;

use Core\Domain\Entities\Ride;

interface RideSearchResult extends SearchResult
{
    /**
     * @return Ride[]
     */
    public function items(): array;

    public function total(): int;
}
