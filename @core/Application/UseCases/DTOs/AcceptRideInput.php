<?php

namespace Core\Application\UseCases\DTOs;

class AcceptRideInput
{
    public function __construct(
        public readonly string $rideId,
        public readonly string $driverId,
    ) {
    }
}
