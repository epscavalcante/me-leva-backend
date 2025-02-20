<?php

namespace Core\Application\UseCases\DTOs;

class GetRidesInput
{
    public function __construct(
        public readonly ?string $passengerId = null,
        public readonly ?string $driverId = null,
        public readonly ?string $status = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $sortDir = null,
    ) {
    }
}
