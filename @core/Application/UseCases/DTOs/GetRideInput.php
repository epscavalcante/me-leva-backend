<?php

namespace Core\Application\UseCases\DTOs;

class GetRideInput
{
    public function __construct(
        public readonly string $rideId,
    ) {
    }
}
