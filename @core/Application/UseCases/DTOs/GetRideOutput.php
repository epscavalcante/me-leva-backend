<?php

namespace Core\Application\UseCases\DTOs;

class GetRideOutput
{
    public function __construct(
        public readonly string $rideId,
        public readonly string $passengerId,
        public readonly string $status,
        public readonly string $fromLatitude,
        public readonly string $fromLongitude,
        public readonly string $toLatitude,
        public readonly string $toLongitude,
        public readonly ?string $driverId = null,
    ) {
    }
}
