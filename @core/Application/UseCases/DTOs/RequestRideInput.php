<?php

namespace Core\Application\UseCases\DTOs;

class RequestRideInput
{
    public function __construct(
        public readonly string $passengerId,
        public readonly string $fromLatitude,
        public readonly string $fromLongitude,
        public readonly string $toLatitude,
        public readonly string $toLongitude,
    ) {}
}
