<?php

namespace App\Repositories;

use Core\Application\Repositories\RideSearchResult as IRideSearchResult;

class RideSearchResult implements IRideSearchResult
{
    public function __construct(
        private readonly ?array $items = [],
        private readonly ?int $total = 0,
    ) {}

    /**
     * @return Ride[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }
}
