<?php

namespace Core\Application\UseCases\DTOs;

class StartRideInput
{
    public function __construct(
        public readonly string $rideId,
    ) {}
}
